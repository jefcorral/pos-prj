<?php

use App\Actions\CreateSale;
use App\Actions\HoldSale;
use App\Enums\SaleStatus;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\Sale;
use Illuminate\Validation\ValidationException;

function checkoutPayload(Product $product, float $qty = 1, array $overrides = []): array
{
    return array_merge([
        'items' => [['product_id' => $product->id, 'quantity' => $qty]],
        'payments' => [['method' => 'cash', 'amount' => 10000]],
    ], $overrides);
}

it('creates a sale with items, payment, inventory movement and receipt atomically', function () {
    [, $branch, $user] = tenant();
    $product = Product::factory()->create(['company_id' => $user->company_id, 'selling_price' => 100]);
    Inventory::create(['company_id' => $user->company_id, 'branch_id' => $branch->id, 'product_id' => $product->id, 'quantity' => 10]);

    $sale = app(CreateSale::class)->handle(checkoutPayload($product, 2), $user, $branch->id);

    expect($sale->status)->toBe(SaleStatus::Completed)
        ->and($sale->total)->toBe('200.00')
        ->and($sale->payments)->toHaveCount(1)
        ->and($sale->receipt)->not->toBeNull()
        ->and(Inventory::where('product_id', $product->id)->first()->quantity)->toBe('8.000')
        ->and($sale->payments->first()->amount)->toBe('200.00')
        ->and($sale->payments->first()->change)->toBe('9800.00');
});

it('supports split payments', function () {
    [, $branch, $user] = tenant();
    $product = Product::factory()->create(['company_id' => $user->company_id, 'selling_price' => 1000]);
    Inventory::create(['company_id' => $user->company_id, 'branch_id' => $branch->id, 'product_id' => $product->id, 'quantity' => 10]);

    $sale = app(CreateSale::class)->handle(checkoutPayload($product, 1, [
        'payments' => [
            ['method' => 'cash', 'amount' => 500],
            ['method' => 'gcash', 'amount' => 300],
            ['method' => 'card', 'amount' => 200],
        ],
    ]), $user, $branch->id);

    expect($sale->payments)->toHaveCount(3)
        ->and($sale->total)->toBe('1000.00')
        ->and($sale->change_total)->toBe('0.00');
});

it('applies percent discounts and recomputes totals server-side', function () {
    [, $branch, $user] = tenant();
    $product = Product::factory()->create(['company_id' => $user->company_id, 'selling_price' => 200]);
    Inventory::create(['company_id' => $user->company_id, 'branch_id' => $branch->id, 'product_id' => $product->id, 'quantity' => 10]);

    $sale = app(CreateSale::class)->handle(checkoutPayload($product, 1, [
        'discount' => ['type' => 'percent', 'value' => 10],
        'payments' => [['method' => 'cash', 'amount' => 180]],
    ]), $user, $branch->id);

    expect($sale->subtotal)->toBe('200.00')
        ->and($sale->discount_total)->toBe('20.00')
        ->and($sale->total)->toBe('180.00');
});

it('rejects underpayment', function () {
    [, $branch, $user] = tenant();
    $product = Product::factory()->create(['company_id' => $user->company_id, 'selling_price' => 500]);

    app(CreateSale::class)->handle(checkoutPayload($product, 1, [
        'payments' => [['method' => 'cash', 'amount' => 100]],
    ]), $user, $branch->id);
})->throws(ValidationException::class);

it('rejects insufficient stock and rolls back everything', function () {
    [, $branch, $user] = tenant();
    $product = Product::factory()->create(['company_id' => $user->company_id, 'selling_price' => 100]);
    Inventory::create(['company_id' => $user->company_id, 'branch_id' => $branch->id, 'product_id' => $product->id, 'quantity' => 1]);

    expect(fn () => app(CreateSale::class)->handle(checkoutPayload($product, 5), $user, $branch->id))
        ->toThrow(ValidationException::class);

    expect(Sale::count())->toBe(0)
        ->and(Inventory::where('product_id', $product->id)->first()->quantity)->toBe('1.000');
});

it('holds and resumes a sale', function () {
    [, $branch, $user] = tenant();
    $product = Product::factory()->create(['company_id' => $user->company_id, 'selling_price' => 100]);
    Inventory::create(['company_id' => $user->company_id, 'branch_id' => $branch->id, 'product_id' => $product->id, 'quantity' => 10]);

    $held = app(HoldSale::class)->handle(
        ['items' => [['product_id' => $product->id, 'quantity' => 3]]],
        $user, $branch->id
    );

    expect($held->status)->toBe(SaleStatus::Held);

    $sale = app(CreateSale::class)->handle(
        checkoutPayload($product, 3, ['held_sale_id' => $held->id]),
        $user, $branch->id
    );

    expect($sale->id)->toBe($held->id)
        ->and($sale->status)->toBe(SaleStatus::Completed)
        ->and((float) Inventory::where('product_id', $product->id)->first()->quantity)->toBe(7.0);
});

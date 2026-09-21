<?php

use App\Actions\CreateSale;
use App\Actions\RefundSale;
use App\Actions\VoidSale;
use App\Enums\SaleStatus;
use App\Models\Inventory;
use App\Models\Product;
use Illuminate\Validation\ValidationException;

it('restocks inventory when a completed sale is voided', function () {
    [, $branch, $user] = tenant();
    $product = Product::factory()->create(['company_id' => $user->company_id, 'selling_price' => 100]);
    Inventory::create(['company_id' => $user->company_id, 'branch_id' => $branch->id, 'product_id' => $product->id, 'quantity' => 10]);

    $sale = app(CreateSale::class)->handle([
        'items' => [['product_id' => $product->id, 'quantity' => 4]],
        'payments' => [['method' => 'cash', 'amount' => 400]],
    ], $user, $branch->id);

    app(VoidSale::class)->handle($sale, $user, 'test void');

    $sale->refresh();
    expect($sale->status)->toBe(SaleStatus::Voided)
        ->and((float) Inventory::where('product_id', $product->id)->first()->quantity)->toBe(10.0);
});

it('partially refunds a sale and restocks only refunded quantity', function () {
    [, $branch, $user] = tenant();
    $product = Product::factory()->create(['company_id' => $user->company_id, 'selling_price' => 100]);
    Inventory::create(['company_id' => $user->company_id, 'branch_id' => $branch->id, 'product_id' => $product->id, 'quantity' => 10]);

    $sale = app(CreateSale::class)->handle([
        'items' => [['product_id' => $product->id, 'quantity' => 4]],
        'payments' => [['method' => 'cash', 'amount' => 400]],
    ], $user, $branch->id);

    $refund = app(RefundSale::class)->handle($sale, [
        ['sale_item_id' => $sale->items->first()->id, 'quantity' => 1],
    ], $user);

    expect($refund->amount)->toBe('100.00')
        ->and($sale->refresh()->status)->toBe(SaleStatus::PartiallyRefunded)
        ->and((float) Inventory::where('product_id', $product->id)->first()->quantity)->toBe(7.0);
});

it('fully refunds a sale', function () {
    [, $branch, $user] = tenant();
    $product = Product::factory()->create(['company_id' => $user->company_id, 'selling_price' => 50]);
    Inventory::create(['company_id' => $user->company_id, 'branch_id' => $branch->id, 'product_id' => $product->id, 'quantity' => 10]);

    $sale = app(CreateSale::class)->handle([
        'items' => [['product_id' => $product->id, 'quantity' => 2]],
        'payments' => [['method' => 'gcash', 'amount' => 100]],
    ], $user, $branch->id);

    app(RefundSale::class)->handle($sale, [
        ['sale_item_id' => $sale->items->first()->id, 'quantity' => 2],
    ], $user);

    expect($sale->refresh()->status)->toBe(SaleStatus::Refunded);
});

it('rejects refunding more than sold', function () {
    [, $branch, $user] = tenant();
    $product = Product::factory()->create(['company_id' => $user->company_id, 'selling_price' => 50]);
    Inventory::create(['company_id' => $user->company_id, 'branch_id' => $branch->id, 'product_id' => $product->id, 'quantity' => 10]);

    $sale = app(CreateSale::class)->handle([
        'items' => [['product_id' => $product->id, 'quantity' => 1]],
        'payments' => [['method' => 'cash', 'amount' => 50]],
    ], $user, $branch->id);

    app(RefundSale::class)->handle($sale, [
        ['sale_item_id' => $sale->items->first()->id, 'quantity' => 5],
    ], $user);
})->throws(ValidationException::class);

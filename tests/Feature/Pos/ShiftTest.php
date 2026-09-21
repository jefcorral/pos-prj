<?php

use App\Actions\CloseCashierShift;
use App\Actions\CreateSale;
use App\Actions\OpenCashierShift;
use App\Actions\RecordCashMovement;
use App\Enums\CashMovementType;
use App\Models\Inventory;
use App\Models\Product;
use Illuminate\Validation\ValidationException;

it('opens a shift and prevents duplicates', function () {
    [, $branch, $user] = tenant();

    $shift = app(OpenCashierShift::class)->handle($user, $branch->id, 500);
    expect($shift->status)->toBe('open');

    app(OpenCashierShift::class)->handle($user, $branch->id, 500);
})->throws(ValidationException::class);

it('computes expected cash and variance on close', function () {
    [, $branch, $user] = tenant();
    $product = Product::factory()->create(['company_id' => $user->company_id, 'selling_price' => 100]);
    Inventory::create(['company_id' => $user->company_id, 'branch_id' => $branch->id, 'product_id' => $product->id, 'quantity' => 10]);

    $shift = app(OpenCashierShift::class)->handle($user, $branch->id, 500);

    app(CreateSale::class)->handle([
        'items' => [['product_id' => $product->id, 'quantity' => 1]],
        'payments' => [['method' => 'cash', 'amount' => 100]],
    ], $user, $branch->id);

    app(RecordCashMovement::class)->handle($shift, CashMovementType::In, 50, $user, 'petty cash');
    app(RecordCashMovement::class)->handle($shift, CashMovementType::Out, 20, $user, 'supplies');

    $shift = app(CloseCashierShift::class)->handle($shift->refresh(), $user, 640);

    // 500 opening + 100 sale + 50 in - 20 out = 630 expected; actual 640 → +10 variance
    expect((float) $shift->expected_cash)->toBe(630.0)
        ->and((float) $shift->variance)->toBe(10.0)
        ->and($shift->status)->toBe('closed');
});

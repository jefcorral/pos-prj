<?php

use App\Models\Inventory;
use App\Models\Product;

it('allows a cashier to use the POS but not refund or adjust inventory', function () {
    [, $branch, $user] = tenant('cashier');

    $this->actingAs($user)->get('/pos')->assertOk();
    $this->actingAs($user)->get('/inventory')->assertOk();

    $product = Product::factory()->create(['company_id' => $user->company_id]);
    Inventory::create(['company_id' => $user->company_id, 'branch_id' => $branch->id, 'product_id' => $product->id, 'quantity' => 5]);

    $this->actingAs($user)->post('/inventory/adjust', [
        'product_id' => $product->id,
        'quantity' => 10,
        'reason' => 'test',
    ])->assertForbidden();

    $this->actingAs($user)->get('/products/create')->assertForbidden();
});

it('blocks guests from the POS', function () {
    $this->get('/pos')->assertRedirect('/login');
});

it('lets a manager void a sale', function () {
    [, , $manager] = tenant('manager');
    expect($manager->can('sales.void'))->toBeTrue();
});

it('denies cashier the void permission', function () {
    [, , $cashier] = tenant('cashier');
    expect($cashier->can('sales.void'))->toBeFalse()
        ->and($cashier->can('sales.refund'))->toBeFalse();
});

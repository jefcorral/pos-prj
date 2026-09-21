<?php

use App\Http\Controllers\BranchController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\PosController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PurchaseOrderController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\ShiftController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::inertia('/', 'Welcome')->name('home');

Route::middleware(['auth'])->group(function () {
    Route::get('dashboard', DashboardController::class)->name('dashboard');

    // POS terminal
    Route::get('pos', [PosController::class, 'index'])->name('pos.index');
    Route::get('pos/products', [PosController::class, 'products'])->name('pos.products');
    Route::post('pos/checkout', [PosController::class, 'checkout'])->name('pos.checkout');
    Route::post('pos/hold', [PosController::class, 'hold'])->name('pos.hold');

    // Products
    Route::resource('products', ProductController::class)->except('show');

    // Catalog (categories, brands, units, taxes, discounts)
    Route::get('catalog', [CatalogController::class, 'index'])->name('catalog.index');
    Route::post('catalog/categories', [CatalogController::class, 'storeCategory'])->name('catalog.categories.store');
    Route::put('catalog/categories/{category}', [CatalogController::class, 'updateCategory'])->name('catalog.categories.update');
    Route::delete('catalog/categories/{category}', [CatalogController::class, 'destroyCategory'])->name('catalog.categories.destroy');
    Route::post('catalog/brands', [CatalogController::class, 'storeBrand'])->name('catalog.brands.store');
    Route::delete('catalog/brands/{brand}', [CatalogController::class, 'destroyBrand'])->name('catalog.brands.destroy');
    Route::post('catalog/units', [CatalogController::class, 'storeUnit'])->name('catalog.units.store');
    Route::post('catalog/taxes', [CatalogController::class, 'storeTax'])->name('catalog.taxes.store');
    Route::post('catalog/discounts', [CatalogController::class, 'storeDiscount'])->name('catalog.discounts.store');

    // Inventory
    Route::get('inventory', [InventoryController::class, 'index'])->name('inventory.index');
    Route::get('inventory/movements', [InventoryController::class, 'movements'])->name('inventory.movements');
    Route::post('inventory/adjust', [InventoryController::class, 'adjust'])->name('inventory.adjust');

    // Sales
    Route::get('sales', [SaleController::class, 'index'])->name('sales.index');
    Route::get('sales/{sale}', [SaleController::class, 'show'])->name('sales.show');
    Route::get('sales/{sale}/receipt', [SaleController::class, 'receipt'])->name('sales.receipt');
    Route::post('sales/{sale}/void', [SaleController::class, 'void'])->name('sales.void');
    Route::post('sales/{sale}/refund', [SaleController::class, 'refund'])->name('sales.refund');
    Route::delete('sales/{sale}/held', [SaleController::class, 'destroyHeld'])->name('sales.destroy-held');

    // Cashier shifts
    Route::get('shifts', [ShiftController::class, 'index'])->name('shifts.index');
    Route::post('shifts', [ShiftController::class, 'open'])->name('shifts.open');
    Route::post('shifts/{shift}/close', [ShiftController::class, 'close'])->name('shifts.close');
    Route::post('shifts/{shift}/cash', [ShiftController::class, 'cashMovement'])->name('shifts.cash');

    // Customers & suppliers
    Route::resource('customers', CustomerController::class)->only('index', 'store', 'update', 'destroy');
    Route::resource('suppliers', SupplierController::class)->only('index', 'store', 'update', 'destroy');

    // Purchasing
    Route::resource('purchase-orders', PurchaseOrderController::class)->only('index', 'create', 'store', 'show', 'update');
    Route::post('purchase-orders/{purchase_order}/receive', [PurchaseOrderController::class, 'receive'])->name('purchase-orders.receive');

    // Reports
    Route::get('reports', [ReportController::class, 'index'])->name('reports.index');

    // Admin
    Route::resource('users', UserController::class)->only('index', 'store', 'update');
    Route::resource('branches', BranchController::class)->only('index', 'store', 'update');
});

require __DIR__.'/settings.php';

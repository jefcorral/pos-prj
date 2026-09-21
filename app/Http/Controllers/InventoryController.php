<?php

namespace App\Http\Controllers;

use App\Actions\AdjustInventory;
use App\Http\Requests\AdjustStockRequest;
use App\Models\Inventory;
use App\Models\InventoryMovement;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class InventoryController extends Controller
{
    public function index(Request $request): Response
    {
        abort_unless($request->user()->can('inventory.view'), 403);
        $user = $request->user();

        $inventory = Inventory::query()
            ->where('branch_id', $user->branch_id)
            ->with(['product' => fn ($q) => $q->withTrashed()->with('category:id,name'), 'variant:id,name,sku'])
            ->when($request->search, fn ($q, $s) => $q->whereHas(
                'product', fn ($q) => $q->where('name', 'like', "%{$s}%")->orWhere('sku', 'like', "%{$s}%")
            ))
            ->when($request->boolean('low_stock'), function ($q) {
                $q->whereHas('product', fn ($q) => $q->whereColumn('inventories.quantity', '<=', 'products.low_stock_threshold'));
            })
            ->orderBy('quantity')
            ->paginate(20)
            ->withQueryString();

        $lowStockCount = Inventory::where('branch_id', $user->branch_id)
            ->whereHas('product', fn ($q) => $q->whereColumn('inventories.quantity', '<=', 'products.low_stock_threshold'))
            ->count();

        return Inertia::render('inventory/Index', [
            'inventory' => $inventory,
            'lowStockCount' => $lowStockCount,
            'filters' => $request->only('search', 'low_stock'),
        ]);
    }

    public function movements(Request $request): Response
    {
        abort_unless($request->user()->can('inventory.view'), 403);
        $user = $request->user();

        $movements = InventoryMovement::query()
            ->where('branch_id', $user->branch_id)
            ->with(['product:id,name,sku', 'variant:id,name', 'user:id,name'])
            ->when($request->type, fn ($q, $t) => $q->where('type', $t))
            ->when($request->product_id, fn ($q, $p) => $q->where('product_id', $p))
            ->when($request->from, fn ($q, $f) => $q->whereDate('created_at', '>=', $f))
            ->when($request->to, fn ($q, $t) => $q->whereDate('created_at', '<=', $t))
            ->latest('created_at')
            ->paginate(30)
            ->withQueryString();

        return Inertia::render('inventory/Movements', [
            'movements' => $movements,
            'filters' => $request->only('type', 'from', 'to'),
        ]);
    }

    public function adjust(AdjustStockRequest $request, AdjustInventory $action): RedirectResponse
    {
        $product = Product::where('company_id', $request->user()->company_id)
            ->whereKey($request->product_id)
            ->firstOrFail();

        $action->handle(
            product: $product,
            branchId: $request->user()->branch_id,
            quantity: (float) $request->quantity,
            reason: $request->reason,
            user: $request->user(),
            variantId: $request->product_variant_id,
            note: $request->note,
            allowNegative: $request->boolean('allow_negative'),
        );

        return back()->with('success', 'Stock adjusted.');
    }
}

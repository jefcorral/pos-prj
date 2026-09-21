<?php

namespace App\Http\Controllers;

use App\Actions\ReceiveGoods;
use App\Http\Requests\ReceiveGoodsRequest;
use App\Http\Requests\StorePurchaseOrderRequest;
use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use App\Support\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class PurchaseOrderController extends Controller
{
    public function index(Request $request): Response
    {
        abort_unless($request->user()->can('purchases.manage'), 403);

        $orders = PurchaseOrder::where('company_id', $request->user()->company_id)
            ->with(['supplier:id,name', 'branch:id,name'])
            ->withCount('items')
            ->when($request->status, fn ($q, $s) => $q->where('status', $s))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return Inertia::render('purchases/Index', [
            'orders' => $orders,
            'filters' => $request->only('status'),
        ]);
    }

    public function create(Request $request): Response
    {
        abort_unless($request->user()->can('purchases.manage'), 403);
        $companyId = $request->user()->company_id;

        return Inertia::render('purchases/Form', [
            'suppliers' => Supplier::where('company_id', $companyId)->where('is_active', true)->orderBy('name')->get(['id', 'name']),
            'products' => Product::where('company_id', $companyId)->where('is_active', true)
                ->orderBy('name')->get(['id', 'name', 'sku', 'cost_price']),
        ]);
    }

    public function store(StorePurchaseOrderRequest $request): RedirectResponse
    {
        $po = DB::transaction(function () use ($request) {
            $po = PurchaseOrder::create([
                'company_id' => $request->user()->company_id,
                'branch_id' => $request->user()->branch_id,
                'supplier_id' => $request->supplier_id,
                'user_id' => $request->user()->id,
                'status' => $request->status ?? 'draft',
                'expected_at' => $request->expected_at,
                'notes' => $request->notes,
            ]);

            foreach ($request->validated('items') as $item) {
                $po->items()->create([
                    'product_id' => $item['product_id'],
                    'product_variant_id' => $item['product_variant_id'] ?? null,
                    'quantity_ordered' => $item['quantity'],
                    'unit_cost' => $item['unit_cost'],
                    'total' => round($item['quantity'] * $item['unit_cost'], 2),
                ]);
            }

            $po->recalculateTotal();
            AuditLogger::log('po.created', $po, new: ['reference_no' => $po->reference_no]);

            return $po;
        });

        return redirect()->route('purchase-orders.show', $po)->with('success', "PO {$po->reference_no} created.");
    }

    public function show(Request $request, PurchaseOrder $purchaseOrder): Response
    {
        abort_unless($request->user()->can('purchases.manage'), 403);
        abort_if($purchaseOrder->company_id !== $request->user()->company_id, 404);

        $purchaseOrder->load(['supplier', 'items.product:id,name,sku', 'items.variant:id,name', 'receipts.items']);

        return Inertia::render('purchases/Show', ['order' => $purchaseOrder]);
    }

    public function update(Request $request, PurchaseOrder $purchaseOrder): RedirectResponse
    {
        abort_if($purchaseOrder->company_id !== $request->user()->company_id, 404);
        abort_unless($request->user()->can('purchases.manage'), 403);

        $request->validate(['status' => ['required', 'in:draft,ordered,cancelled']]);

        abort_if($purchaseOrder->status === 'received', 422, 'A received PO cannot be changed.');

        $purchaseOrder->update(['status' => $request->status]);
        AuditLogger::log('po.status', $purchaseOrder, new: ['status' => $request->status]);

        return back()->with('success', 'PO updated.');
    }

    public function receive(ReceiveGoodsRequest $request, PurchaseOrder $purchaseOrder, ReceiveGoods $action): RedirectResponse
    {
        abort_if($purchaseOrder->company_id !== $request->user()->company_id, 404);

        $action->handle($purchaseOrder, $request->validated('items'), $request->user(), $request->note);

        return back()->with('success', 'Goods received and inventory updated.');
    }
}

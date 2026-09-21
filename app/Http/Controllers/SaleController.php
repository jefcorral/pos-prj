<?php

namespace App\Http\Controllers;

use App\Actions\RefundSale;
use App\Actions\VoidSale;
use App\Enums\SaleStatus;
use App\Http\Requests\RefundSaleRequest;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class SaleController extends Controller
{
    public function index(Request $request)
    {
        abort_unless($request->user()->can('sales.view'), 403);
        $user = $request->user();

        $sales = Sale::query()
            ->where('company_id', $user->company_id)
            ->with(['cashier:id,name', 'customer:id,name', 'payments:id,sale_id,method,amount'])
            ->when($user->cannot('sales.void'), fn ($q) => $q->where('user_id', $user->id))
            ->when($request->status, fn ($q, $s) => $q->where('status', $s))
            ->when($request->branch_id, fn ($q, $b) => $q->where('branch_id', $b))
            ->when($request->from, fn ($q, $f) => $q->whereDate('created_at', '>=', $f))
            ->when($request->to, fn ($q, $t) => $q->whereDate('created_at', '<=', $t))
            ->when($request->search, fn ($q, $s) => $q->where('number', 'like', "%{$s}%"))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return Inertia::render('sales/Index', [
            'sales' => $sales,
            'filters' => $request->only('status', 'search', 'from', 'to'),
        ]);
    }

    public function show(Request $request, Sale $sale)
    {
        abort_unless($request->user()->can('sales.view'), 403);
        abort_if($sale->company_id !== $request->user()->company_id, 404);

        $sale->load([
            'items.product:id,name',
            'payments',
            'refunds.items',
            'cashier:id,name',
            'customer:id,name,phone',
            'branch:id,name,code',
            'receipt',
        ]);

        return Inertia::render('sales/Show', [
            'sale' => $sale,
            'can' => [
                'void' => $request->user()->can('sales.void'),
                'refund' => $request->user()->can('sales.refund'),
            ],
        ]);
    }

    public function receipt(Request $request, Sale $sale)
    {
        abort_if($sale->company_id !== $request->user()->company_id, 404);

        $receipt = $sale->receipt ?: $sale->receipt()->create([
            'number' => 'R-'.$sale->number,
            'payload' => $sale->load(['items', 'payments', 'cashier', 'customer', 'branch'])->toArray(),
        ]);

        return response()->json($receipt);
    }

    public function void(Request $request, Sale $sale, VoidSale $action)
    {
        abort_unless($request->user()->can('sales.void'), 403);
        abort_if($sale->company_id !== $request->user()->company_id, 404);

        $request->validate(['reason' => ['required', 'string', 'max:500']]);

        $action->handle($sale, $request->user(), $request->reason);

        return back()->with('success', "Sale {$sale->number} voided.");
    }

    public function refund(RefundSaleRequest $request, Sale $sale, RefundSale $action)
    {
        abort_if($sale->company_id !== $request->user()->company_id, 404);

        $action->handle(
            $sale,
            $request->validated('items'),
            $request->user(),
            $request->reason,
            $request->method,
        );

        return back()->with('success', 'Refund processed.');
    }

    /**
     * Discard a held sale (no stock/payment was recorded, so no reversal needed).
     */
    public function destroyHeld(Request $request, Sale $sale)
    {
        abort_unless($request->user()->can('pos.use'), 403);

        if ($sale->status !== SaleStatus::Held) {
            throw ValidationException::withMessages(['sale' => 'Only held sales can be discarded.']);
        }

        $sale->items()->delete();
        $sale->delete();

        return back()->with('success', 'Held sale discarded.');
    }
}

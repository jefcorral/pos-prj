<?php

namespace App\Http\Controllers;

use App\Enums\SaleStatus;
use App\Models\Branch;
use App\Models\Inventory;
use App\Models\InventoryMovement;
use App\Models\Payment;
use App\Models\Sale;
use App\Models\SaleItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class ReportController extends Controller
{
    public function index(Request $request): Response
    {
        abort_unless($request->user()->can('reports.view'), 403);
        $user = $request->user();

        $from = $request->input('from', now()->startOfMonth()->toDateString());
        $to = $request->input('to', now()->toDateString());
        $branchId = $request->integer('branch_id') ?: $user->branch_id;

        $salesQuery = Sale::where('company_id', $user->company_id)
            ->whereIn('status', [SaleStatus::Completed, SaleStatus::PartiallyRefunded, SaleStatus::Refunded])
            ->whereDate('completed_at', '>=', $from)
            ->whereDate('completed_at', '<=', $to);
        if ($branchId) {
            $salesQuery->where('branch_id', $branchId);
        }

        $salesIds = (clone $salesQuery)->select('id');

        $summary = (clone $salesQuery)->toBase()->selectRaw('
            coalesce(sum(total),0) as gross_sales,
            coalesce(sum(discount_total),0) as discounts,
            coalesce(sum(tax_total),0) as taxes,
            count(*) as transactions
        ')->first();

        $refundTotal = (float) DB::table('refunds')
            ->whereIn('sale_id', $salesIds)->sum('amount');

        $dailySales = (clone $salesQuery)
            ->selectRaw('date(completed_at) as date, sum(total) as total, count(*) as count')
            ->groupBy('date')->orderBy('date')->get();

        $byPaymentMethod = Payment::whereIn('sale_id', $salesIds)
            ->selectRaw('method, sum(amount) as total, count(*) as count')
            ->groupBy('method')->get();

        $itemsQuery = SaleItem::whereIn('sale_id', $salesIds);

        $byProduct = (clone $itemsQuery)
            ->selectRaw('product_id, name, sum(quantity) as qty, sum(total) as revenue')
            ->groupBy('product_id', 'name')->orderByDesc('revenue')->limit(20)->get();

        $byCategory = (clone $itemsQuery)
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->leftJoin('categories', 'products.category_id', '=', 'categories.id')
            ->selectRaw("coalesce(categories.name, 'Uncategorized') as category, sum(sale_items.quantity) as qty, sum(sale_items.total) as revenue")
            ->groupBy('category')->orderByDesc('revenue')->get();

        $byCashier = (clone $salesQuery)
            ->join('users', 'sales.user_id', '=', 'users.id')
            ->selectRaw('users.name as cashier, sum(sales.total) as total, count(*) as count')
            ->groupBy('users.name')->orderByDesc('total')->get();

        // Inventory report
        $inventoryQuery = Inventory::where('company_id', $user->company_id)
            ->with(['product:id,name,sku,cost_price,selling_price,low_stock_threshold', 'branch:id,name']);
        if ($branchId) {
            $inventoryQuery->where('branch_id', $branchId);
        }

        $inventoryValuation = (clone $inventoryQuery)
            ->join('products', 'inventories.product_id', '=', 'products.id')
            ->selectRaw('sum(inventories.quantity * products.cost_price) as cost_value, sum(inventories.quantity * products.selling_price) as retail_value')
            ->first();

        $lowStock = (clone $inventoryQuery)
            ->whereHas('product', fn ($q) => $q->whereColumn('inventories.quantity', '<=', 'products.low_stock_threshold'))
            ->orderBy('quantity')->limit(20)->get();

        $movements = InventoryMovement::where('company_id', $user->company_id)
            ->when($branchId, fn ($q) => $q->where('branch_id', $branchId))
            ->whereDate('created_at', '>=', $from)
            ->whereDate('created_at', '<=', $to)
            ->selectRaw('type, sum(quantity) as qty, count(*) as count')
            ->groupBy('type')->get();

        return Inertia::render('reports/Index', [
            'filters' => ['from' => $from, 'to' => $to, 'branch_id' => $branchId],
            'branches' => Branch::where('company_id', $user->company_id)->get(['id', 'name']),
            'summary' => [
                'gross_sales' => (float) $summary->gross_sales,
                'discounts' => (float) $summary->discounts,
                'taxes' => (float) $summary->taxes,
                'refunds' => $refundTotal,
                'net_sales' => round((float) $summary->gross_sales - $refundTotal, 2),
                'transactions' => (int) $summary->transactions,
            ],
            'dailySales' => $dailySales,
            'byPaymentMethod' => $byPaymentMethod,
            'byProduct' => $byProduct,
            'byCategory' => $byCategory,
            'byCashier' => $byCashier,
            'inventory' => [
                'cost_value' => (float) ($inventoryValuation->cost_value ?? 0),
                'retail_value' => (float) ($inventoryValuation->retail_value ?? 0),
                'low_stock' => $lowStock,
                'movements' => $movements,
            ],
        ]);
    }
}

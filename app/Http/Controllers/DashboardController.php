<?php

namespace App\Http\Controllers;

use App\Enums\SaleStatus;
use App\Models\Inventory;
use App\Models\Sale;
use App\Models\SaleItem;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function __invoke(Request $request)
    {
        $user = $request->user();
        $branchId = $user->branch_id;
        $today = now()->toDateString();

        $completedToday = Sale::where('branch_id', $branchId)
            ->where('status', SaleStatus::Completed)
            ->whereDate('completed_at', $today);

        $stats = [
            'today_sales' => (float) (clone $completedToday)->sum('total'),
            'today_transactions' => (clone $completedToday)->count(),
            'today_items' => (float) SaleItem::whereHas(
                'sale', fn ($q) => $q->where('branch_id', $branchId)
                    ->where('status', SaleStatus::Completed)
                    ->whereDate('completed_at', $today)
            )->sum('quantity'),
            'low_stock_count' => Inventory::where('branch_id', $branchId)
                ->whereHas('product', fn ($q) => $q->whereColumn('inventories.quantity', '<=', 'products.low_stock_threshold'))
                ->count(),
        ];
        $stats['average_transaction'] = $stats['today_transactions'] > 0
            ? round($stats['today_sales'] / $stats['today_transactions'], 2)
            : 0;

        // 14-day sales trend
        $trend = Sale::where('branch_id', $branchId)
            ->where('status', SaleStatus::Completed)
            ->where('completed_at', '>=', now()->subDays(13)->startOfDay())
            ->selectRaw('date(completed_at) as date, sum(total) as total, count(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $recentSales = Sale::where('branch_id', $branchId)
            ->whereIn('status', [SaleStatus::Completed, SaleStatus::PartiallyRefunded, SaleStatus::Refunded])
            ->with('cashier:id,name')
            ->latest('completed_at')
            ->limit(8)
            ->get(['id', 'number', 'total', 'status', 'completed_at', 'user_id']);

        $topProducts = SaleItem::whereHas(
            'sale', fn ($q) => $q->where('branch_id', $branchId)
                ->where('status', SaleStatus::Completed)
                ->where('completed_at', '>=', now()->subDays(30))
        )
            ->selectRaw('product_id, name, sum(quantity) as qty, sum(total) as revenue')
            ->groupBy('product_id', 'name')
            ->orderByDesc('revenue')
            ->limit(5)
            ->get();

        return Inertia::render('Dashboard', [
            'stats' => $stats,
            'trend' => $trend,
            'recentSales' => $recentSales,
            'topProducts' => $topProducts,
        ]);
    }
}

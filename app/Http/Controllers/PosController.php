<?php

namespace App\Http\Controllers;

use App\Actions\CreateSale;
use App\Actions\HoldSale;
use App\Enums\SaleStatus;
use App\Http\Requests\CheckoutRequest;
use App\Http\Requests\HoldSaleRequest;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Sale;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PosController extends Controller
{
    public function index(Request $request): Response
    {
        abort_unless($request->user()->can('pos.use'), 403);

        $user = $request->user();

        return Inertia::render('pos/Index', [
            'categories' => Category::where('company_id', $user->company_id)
                ->where('is_active', true)->orderBy('name')->get(['id', 'name']),
            'customers' => Customer::where('company_id', $user->company_id)
                ->where('is_active', true)->orderBy('name')->get(['id', 'name']),
            'heldSales' => Sale::where('branch_id', $user->branch_id)
                ->where('status', SaleStatus::Held)
                ->with('items')
                ->latest('held_at')
                ->limit(20)
                ->get(['id', 'number', 'total', 'note', 'held_at', 'customer_id']),
            'shift' => $user->currentShift(),
        ]);
    }

    /**
     * Product search for the POS grid / barcode scanner. Returns JSON.
     */
    public function products(Request $request): JsonResponse
    {
        abort_unless($request->user()->can('pos.use'), 403);

        $branchId = $request->user()->branch_id;

        $products = Product::query()
            ->where('company_id', $request->user()->company_id)
            ->where('is_active', true)
            ->with(['tax:id,rate,type', 'variants' => fn ($q) => $q->where('is_active', true)])
            ->with(['inventories' => fn ($q) => $q->where('branch_id', $branchId)])
            ->when($request->category_id, fn ($q, $c) => $q->where('category_id', $c))
            ->when($request->search, function ($q, $s) {
                $q->where(fn ($q) => $q->where('name', 'like', "%{$s}%")
                    ->orWhere('sku', 'like', "%{$s}%")
                    ->orWhere('barcode', $s)
                    ->orWhereHas('variants', fn ($v) => $v->where('barcode', $s))
                    ->orWhereHas('barcodes', fn ($b) => $b->where('barcode', $s))
                );
            })
            ->orderBy('name')
            ->limit(60)
            ->get()
            ->map(fn (Product $p): array => [
                'id' => $p->id,
                'name' => $p->name,
                'sku' => $p->sku,
                'barcode' => $p->barcode,
                'price' => (float) $p->selling_price,
                'tax_rate' => $p->tax ? (float) $p->tax->rate : 0,
                'tax_type' => $p->tax?->type,
                'image' => $p->image_path ? asset('storage/'.$p->image_path) : null,
                'stock' => (float) data_get($p->inventories->first(), 'quantity', 0),
                'track_stock' => $p->track_stock,
                'variants' => $p->variants->map(fn (ProductVariant $v): array => [
                    'id' => $v->id,
                    'name' => $v->name,
                    'sku' => $v->sku,
                    'barcode' => $v->barcode,
                    'price' => (float) ($v->selling_price ?? $p->selling_price),
                ]),
            ]);

        return response()->json($products);
    }

    public function checkout(CheckoutRequest $request, CreateSale $action): RedirectResponse
    {
        $sale = $action->handle($request->validated(), $request->user(), $request->user()->branch_id);

        return redirect()->route('pos.index')->with('completed_sale', $sale->id);
    }

    public function hold(HoldSaleRequest $request, HoldSale $action): RedirectResponse
    {
        $sale = $action->handle($request->validated(), $request->user(), $request->user()->branch_id);

        return back()->with('success', "Sale {$sale->number} held.");
    }
}

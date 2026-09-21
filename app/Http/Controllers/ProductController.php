<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\Tax;
use App\Models\Unit;
use App\Support\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class ProductController extends Controller
{
    public function index(Request $request): Response
    {
        abort_unless($request->user()->can('products.view'), 403);

        $products = Product::query()
            ->where('company_id', $request->user()->company_id)
            ->with(['category:id,name', 'brand:id,name', 'variants:id,product_id,name,sku,selling_price'])
            ->withSum('inventories', 'quantity')
            ->when($request->search, fn ($q, $s) => $q->where(
                fn ($q) => $q->where('name', 'like', "%{$s}%")
                    ->orWhere('sku', 'like', "%{$s}%")
                    ->orWhere('barcode', 'like', "%{$s}%")
            ))
            ->when($request->category_id, fn ($q, $c) => $q->where('category_id', $c))
            ->when($request->boolean('active_only'), fn ($q) => $q->where('is_active', true))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('products/Index', [
            'products' => $products,
            'categories' => Category::where('company_id', $request->user()->company_id)->orderBy('name')->get(['id', 'name']),
            'filters' => $request->only('search', 'category_id', 'active_only'),
        ]);
    }

    public function create(Request $request): Response
    {
        abort_unless($request->user()->can('products.create'), 403);

        return Inertia::render('products/Form', [
            'product' => null,
            ...$this->formOptions($request),
        ]);
    }

    public function store(StoreProductRequest $request): RedirectResponse
    {
        $product = DB::transaction(function () use ($request) {
            $data = $request->safe()->except(['image', 'variants']);
            $data['company_id'] = $request->user()->company_id;
            $data['branch_id'] = $request->user()->branch_id;
            $data['has_variants'] = ! empty($request->validated('variants'));

            if ($request->hasFile('image')) {
                $data['image_path'] = $request->file('image')->store('products', 'public');
            }

            $product = Product::create($data);

            foreach ($request->validated('variants') ?? [] as $variant) {
                $product->variants()->create($variant);
            }

            AuditLogger::log('product.created', $product, new: $product->only('name', 'sku', 'selling_price'));

            return $product;
        });

        return redirect()->route('products.index')->with('success', 'Product created.');
    }

    public function edit(Request $request, Product $product): Response
    {
        abort_unless($request->user()->can('products.update'), 403);
        $this->authorizeCompany($request, $product);

        return Inertia::render('products/Form', [
            'product' => $product->load('variants'),
            ...$this->formOptions($request),
        ]);
    }

    public function update(UpdateProductRequest $request, Product $product): RedirectResponse
    {
        $this->authorizeCompany($request, $product);

        DB::transaction(function () use ($request, $product) {
            $data = $request->safe()->except(['image', 'variants']);

            if ($request->hasFile('image')) {
                if ($product->image_path) {
                    Storage::disk('public')->delete($product->image_path);
                }
                $data['image_path'] = $request->file('image')->store('products', 'public');
            }

            $product->update($data);

            $keep = [];
            foreach ($request->validated('variants') ?? [] as $variant) {
                $variant = $product->variants()->updateOrCreate(
                    ['id' => $variant['id'] ?? null],
                    Arr::except($variant, 'id')
                );
                $keep[] = $variant->id;
            }
            $product->variants()->whereNotIn('id', $keep)->delete();
            $product->update(['has_variants' => count($keep) > 0]);

            AuditLogger::log('product.updated', $product, new: $product->only('name', 'sku', 'selling_price'));
        });

        return redirect()->route('products.index')->with('success', 'Product updated.');
    }

    public function destroy(Request $request, Product $product): RedirectResponse
    {
        abort_unless($request->user()->can('products.delete'), 403);
        $this->authorizeCompany($request, $product);

        AuditLogger::log('product.deleted', $product, old: $product->only('name', 'sku'));
        $product->delete();

        return back()->with('success', 'Product deleted.');
    }

    /** @return array<string, mixed> */
    private function formOptions(Request $request): array
    {
        $companyId = $request->user()->company_id;

        return [
            'categories' => Category::where('company_id', $companyId)->orderBy('name')->get(['id', 'name']),
            'brands' => Brand::where('company_id', $companyId)->orderBy('name')->get(['id', 'name']),
            'units' => Unit::where('company_id', $companyId)->orderBy('name')->get(['id', 'name', 'abbreviation']),
            'taxes' => Tax::where('company_id', $companyId)->where('is_active', true)->get(['id', 'name', 'rate', 'type']),
        ];
    }

    private function authorizeCompany(Request $request, Product $product): void
    {
        abort_if($product->company_id !== $request->user()->company_id, 404);
    }
}

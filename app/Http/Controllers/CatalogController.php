<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCategoryRequest;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Discount;
use App\Models\Tax;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

/**
 * Simple catalog management: categories, brands, units, taxes, discounts.
 */
class CatalogController extends Controller
{
    public function index(Request $request)
    {
        abort_unless($request->user()->can('catalog.manage'), 403);
        $companyId = $request->user()->company_id;

        return Inertia::render('catalog/Index', [
            'categories' => Category::where('company_id', $companyId)->withCount('products')->orderBy('name')->get(),
            'brands' => Brand::where('company_id', $companyId)->orderBy('name')->get(),
            'units' => Unit::where('company_id', $companyId)->orderBy('name')->get(),
            'taxes' => Tax::where('company_id', $companyId)->orderBy('name')->get(),
            'discounts' => Discount::where('company_id', $companyId)->orderBy('name')->get(),
        ]);
    }

    public function storeCategory(StoreCategoryRequest $request)
    {
        Category::create($request->validated() + [
            'company_id' => $request->user()->company_id,
            'slug' => str()->slug($request->name),
        ]);

        return back()->with('success', 'Category created.');
    }

    public function updateCategory(Request $request, Category $category)
    {
        $request->validate(['name' => ['required', 'string', 'max:255']]);
        abort_if($category->company_id !== $request->user()->company_id, 404);
        $category->update($request->only('name', 'parent_id', 'is_active'));

        return back()->with('success', 'Category updated.');
    }

    public function destroyCategory(Request $request, Category $category)
    {
        abort_if($category->company_id !== $request->user()->company_id, 404);
        $category->delete();

        return back();
    }

    public function storeBrand(Request $request)
    {
        $data = $request->validate(['name' => ['required', 'string', 'max:255']]);
        abort_unless($request->user()->can('catalog.manage'), 403);
        Brand::create($data + ['company_id' => $request->user()->company_id]);

        return back()->with('success', 'Brand created.');
    }

    public function destroyBrand(Request $request, Brand $brand)
    {
        abort_if($brand->company_id !== $request->user()->company_id, 404);
        abort_unless($request->user()->can('catalog.manage'), 403);
        $brand->delete();

        return back();
    }

    public function storeUnit(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'abbreviation' => ['required', 'string', 'max:10'],
        ]);
        abort_unless($request->user()->can('catalog.manage'), 403);
        Unit::create($data + ['company_id' => $request->user()->company_id]);

        return back()->with('success', 'Unit created.');
    }

    public function storeTax(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'rate' => ['required', 'numeric', 'min:0', 'max:100'],
            'type' => ['required', Rule::in(['inclusive', 'exclusive'])],
        ]);
        abort_unless($request->user()->can('catalog.manage'), 403);
        Tax::create($data + ['company_id' => $request->user()->company_id]);

        return back()->with('success', 'Tax created.');
    }

    public function storeDiscount(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'type' => ['required', Rule::in(['percent', 'fixed'])],
            'value' => ['required', 'numeric', 'min:0'],
        ]);
        abort_unless($request->user()->can('catalog.manage'), 403);
        Discount::create($data + ['company_id' => $request->user()->company_id]);

        return back()->with('success', 'Discount created.');
    }
}

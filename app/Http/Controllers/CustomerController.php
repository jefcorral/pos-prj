<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCustomerRequest;
use App\Models\Customer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CustomerController extends Controller
{
    public function index(Request $request): Response
    {
        abort_unless($request->user()->can('customers.manage'), 403);

        $customers = Customer::where('company_id', $request->user()->company_id)
            ->when($request->search, fn ($q, $s) => $q->where(
                fn ($q) => $q->where('name', 'like', "%{$s}%")->orWhere('phone', 'like', "%{$s}%")
            ))
            ->withCount('sales')
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return Inertia::render('customers/Index', [
            'customers' => $customers,
            'filters' => $request->only('search'),
        ]);
    }

    public function store(StoreCustomerRequest $request): RedirectResponse
    {
        Customer::create($request->validated() + ['company_id' => $request->user()->company_id]);

        return back()->with('success', 'Customer created.');
    }

    public function update(StoreCustomerRequest $request, Customer $customer): RedirectResponse
    {
        abort_if($customer->company_id !== $request->user()->company_id, 404);
        $customer->update($request->validated());

        return back()->with('success', 'Customer updated.');
    }

    public function destroy(Request $request, Customer $customer): RedirectResponse
    {
        abort_if($customer->company_id !== $request->user()->company_id, 404);
        $customer->delete();

        return back()->with('success', 'Customer deleted.');
    }
}

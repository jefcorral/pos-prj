<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class BranchController extends Controller
{
    public function index(Request $request): Response
    {
        abort_unless($request->user()->can('branches.manage'), 403);

        return Inertia::render('branches/Index', [
            'branches' => Branch::where('company_id', $request->user()->company_id)
                ->withCount('users')->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        abort_unless($request->user()->can('branches.manage'), 403);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:500'],
            'phone' => ['nullable', 'string', 'max:50'],
        ]);

        Branch::create($data + ['company_id' => $request->user()->company_id]);

        return back()->with('success', 'Branch created.');
    }

    public function update(Request $request, Branch $branch): RedirectResponse
    {
        abort_unless($request->user()->can('branches.manage'), 403);
        abort_if($branch->company_id !== $request->user()->company_id, 404);

        $branch->update($request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:500'],
            'phone' => ['nullable', 'string', 'max:50'],
            'is_active' => ['boolean'],
        ]));

        return back()->with('success', 'Branch updated.');
    }
}

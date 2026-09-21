<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\User;
use App\Support\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Inertia\Inertia;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index(Request $request)
    {
        abort_unless($request->user()->can('users.manage'), 403);

        return Inertia::render('users/Index', [
            'users' => User::where('company_id', $request->user()->company_id)
                ->with(['roles:id,name', 'branch:id,name'])
                ->orderBy('name')->get(),
            'roles' => Role::orderBy('name')->get(['id', 'name']),
            'branches' => Branch::where('company_id', $request->user()->company_id)->get(['id', 'name']),
        ]);
    }

    public function store(Request $request)
    {
        abort_unless($request->user()->can('users.manage'), 403);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', Password::defaults()],
            'role' => ['required', 'exists:roles,name'],
            'branch_id' => ['nullable', 'exists:branches,id'],
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
            'company_id' => $request->user()->company_id,
            'branch_id' => $data['branch_id'],
        ]);
        $user->assignRole($data['role']);

        AuditLogger::log('user.created', $user, new: ['email' => $user->email, 'role' => $data['role']]);

        return back()->with('success', 'User created.');
    }

    public function update(Request $request, User $user)
    {
        abort_unless($request->user()->can('users.manage'), 403);
        abort_if($user->company_id !== $request->user()->company_id, 404);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => ['nullable', Password::defaults()],
            'role' => ['required', 'exists:roles,name'],
            'branch_id' => ['nullable', 'exists:branches,id'],
            'is_active' => ['boolean'],
        ]);

        $user->fill(collect($data)->except(['password', 'role'])->all());
        if (! empty($data['password'])) {
            $user->password = $data['password'];
        }
        $user->save();
        $user->syncRoles([$data['role']]);

        AuditLogger::log('user.updated', $user, new: ['email' => $user->email, 'role' => $data['role']]);

        return back()->with('success', 'User updated.');
    }
}

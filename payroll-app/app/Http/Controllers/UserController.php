<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index()
    {
        $q = request('q');
        $users = User::leftJoin('companies', 'companies.id', '=', 'users.company_id')
            ->when($q, fn($qBuilder) => $qBuilder->where(function ($sub) use ($q) {
                $sub->where('users.name', 'like', "%{$q}%")
                    ->orWhere('users.email', 'like', "%{$q}%");
            }))
            ->select('users.*', 'companies.name as company_name')
            ->orderBy('users.name')
            ->paginate(20)
            ->withQueryString();

        return view('users.index', compact('users'));
    }

    public function create()
    {
        $companies = DB::table('companies')->orderBy('name')->pluck('name', 'id');
        $branches = Branch::orderBy('name')->pluck('name', 'id');
        $lazRoles = Role::orderBy('name')->pluck('name', 'id');
        
        return view('users.create', compact('companies', 'branches', 'lazRoles'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6'],
            'company_id' => ['nullable', 'integer', 'exists:companies,id'],
            'branch_id' => ['nullable', 'integer', 'exists:branches,id'],
            'role' => ['nullable', 'string', 'in:admin,hr,manager,volunteer'],
            'laz_roles' => ['nullable', 'array'],
            'laz_roles.*' => ['exists:roles,id'],
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'company_id' => $data['company_id'] ?? null,
            'branch_id' => $data['branch_id'] ?? null,
            'role' => $data['role'] ?? 'admin',
        ]);

        if (!empty($data['laz_roles'])) {
            $user->roles()->attach($data['laz_roles']);
        }

        return redirect()->route('users.index')->with('success', 'User ditambahkan.');
    }

    public function edit(int $id)
    {
        $user = User::with('roles')->findOrFail($id);
        $companies = DB::table('companies')->orderBy('name')->pluck('name', 'id');
        $branches = Branch::orderBy('name')->pluck('name', 'id');
        $lazRoles = Role::orderBy('name')->pluck('name', 'id');

        return view('users.edit', compact('user', 'companies', 'branches', 'lazRoles'));
    }

    public function update(Request $request, int $id)
    {
        $user = User::findOrFail($id);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($id)],
            'password' => ['nullable', 'string', 'min:6'],
            'company_id' => ['nullable', 'integer', 'exists:companies,id'],
            'branch_id' => ['nullable', 'integer', 'exists:branches,id'],
            'role' => ['nullable', 'string', 'in:admin,hr,manager,volunteer'],
            'laz_roles' => ['nullable', 'array'],
            'laz_roles.*' => ['exists:roles,id'],
        ]);

        $updateData = [
            'name' => $data['name'],
            'email' => $data['email'],
            'company_id' => $data['company_id'] ?? null,
            'branch_id' => $data['branch_id'] ?? null,
            'role' => $data['role'] ?? $user->role,
        ];
        
        if (!empty($data['password'])) {
            $updateData['password'] = Hash::make($data['password']);
        }

        $user->update($updateData);

        if (isset($data['laz_roles'])) {
            $user->roles()->sync($data['laz_roles']);
        } else {
            $user->roles()->detach();
        }

        return redirect()->route('users.index')->with('success', 'User diperbarui.');
    }

    public function destroy(int $id)
    {
        User::destroy($id);
        return redirect()->route('users.index')->with('success', 'User dihapus.');
    }
}

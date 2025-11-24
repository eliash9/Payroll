<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index()
    {
        $q = request('q');
        $users = DB::table('users')
            ->leftJoin('companies', 'companies.id', '=', 'users.company_id')
            ->when($q, fn($qBuilder) => $qBuilder->where(function ($sub) use ($q) {
                $sub->where('name', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%");
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
        return view('users.create', compact('companies'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6'],
            'company_id' => ['nullable', 'integer', 'exists:companies,id'],
            'role' => ['nullable', 'string', 'in:admin,hr,manager,volunteer'],
        ]);

        $now = now();
        DB::table('users')->insert([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'company_id' => $data['company_id'] ?? null,
            'role' => $data['role'] ?? 'admin',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        return redirect()->route('users.index')->with('success', 'User ditambahkan.');
    }

    public function edit(int $id)
    {
        $user = DB::table('users')->find($id);
        abort_unless($user, 404);

        $companies = DB::table('companies')->orderBy('name')->pluck('name', 'id');

        return view('users.edit', compact('user', 'companies'));
    }

    public function update(Request $request, int $id)
    {
        $user = DB::table('users')->find($id);
        abort_unless($user, 404);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($id)],
            'password' => ['nullable', 'string', 'min:6'],
            'company_id' => ['nullable', 'integer', 'exists:companies,id'],
            'role' => ['nullable', 'string', 'in:admin,hr,manager,volunteer'],
        ]);

        $update = [
            'name' => $data['name'],
            'email' => $data['email'],
            'company_id' => $data['company_id'] ?? null,
            'role' => $data['role'] ?? $user->role,
            'updated_at' => now(),
        ];
        if (!empty($data['password'])) {
            $update['password'] = Hash::make($data['password']);
        }

        DB::table('users')->where('id', $id)->update($update);

        return redirect()->route('users.index')->with('success', 'User diperbarui.');
    }

    public function destroy(int $id)
    {
        DB::table('users')->where('id', $id)->delete();
        return redirect()->route('users.index')->with('success', 'User dihapus.');
    }
}

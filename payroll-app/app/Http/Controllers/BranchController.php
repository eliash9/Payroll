<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BranchController extends Controller
{
    public function index()
    {
        $branches = DB::table('branches')
            ->join('companies', 'companies.id', '=', 'branches.company_id')
            ->select('branches.*', 'companies.name as company_name')
            ->orderBy('branches.name')
            ->paginate(20);

        return view('masters.branches.index', compact('branches'));
    }

    public function create()
    {
        $companies = DB::table('companies')->pluck('name', 'id');
        return view('masters.branches.create', compact('companies'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'company_id' => 'required|integer|exists:companies,id',
            'name' => 'required|string|max:191',
            'code' => 'nullable|string|max:50|unique:branches,code',
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:50',
        ]);

        $now = now();
        DB::table('branches')->insert(array_merge($data, [
            'created_at' => $now,
            'updated_at' => $now,
        ]));

        return redirect()->route('branches.index')->with('success', 'Cabang ditambahkan.');
    }

    public function edit(int $id)
    {
        $branch = DB::table('branches')->find($id);
        abort_unless($branch, 404);
        $companies = DB::table('companies')->pluck('name', 'id');

        return view('masters.branches.edit', compact('branch', 'companies'));
    }

    public function update(Request $request, int $id)
    {
        $branch = DB::table('branches')->find($id);
        abort_unless($branch, 404);

        $data = $request->validate([
            'company_id' => 'required|integer|exists:companies,id',
            'name' => 'required|string|max:191',
            'code' => 'nullable|string|max:50|unique:branches,code,' . $id,
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:50',
        ]);

        $data['updated_at'] = now();
        DB::table('branches')->where('id', $id)->update($data);

        return redirect()->route('branches.index')->with('success', 'Cabang diperbarui.');
    }

    public function destroy(int $id)
    {
        DB::table('branches')->where('id', $id)->delete();
        return redirect()->route('branches.index')->with('success', 'Cabang dihapus.');
    }
}

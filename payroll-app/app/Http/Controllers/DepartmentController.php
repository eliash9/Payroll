<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DepartmentController extends Controller
{
    public function index()
    {
        $departments = DB::table('departments')
            ->join('companies', 'companies.id', '=', 'departments.company_id')
            ->select('departments.*', 'companies.name as company_name')
            ->orderBy('departments.name')
            ->paginate(20);

        return view('masters.departments.index', compact('departments'));
    }

    public function create()
    {
        $companies = DB::table('companies')->pluck('name', 'id');
        return view('masters.departments.create', compact('companies'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'company_id' => 'required|integer|exists:companies,id',
            'name' => 'required|string|max:191',
            'code' => 'nullable|string|max:50|unique:departments,code',
        ]);

        $now = now();
        DB::table('departments')->insert(array_merge($data, [
            'created_at' => $now,
            'updated_at' => $now,
        ]));

        return redirect()->route('departments.index')->with('success', 'Departemen ditambahkan.');
    }

    public function edit(int $id)
    {
        $department = DB::table('departments')->find($id);
        abort_unless($department, 404);
        $companies = DB::table('companies')->pluck('name', 'id');

        return view('masters.departments.edit', compact('department', 'companies'));
    }

    public function update(Request $request, int $id)
    {
        $department = DB::table('departments')->find($id);
        abort_unless($department, 404);

        $data = $request->validate([
            'company_id' => 'required|integer|exists:companies,id',
            'name' => 'required|string|max:191',
            'code' => 'nullable|string|max:50|unique:departments,code,' . $id,
        ]);

        $data['updated_at'] = now();
        DB::table('departments')->where('id', $id)->update($data);

        return redirect()->route('departments.index')->with('success', 'Departemen diperbarui.');
    }

    public function destroy(int $id)
    {
        DB::table('departments')->where('id', $id)->delete();
        return redirect()->route('departments.index')->with('success', 'Departemen dihapus.');
    }
}

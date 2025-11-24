<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PositionController extends Controller
{
    public function index()
    {
        $positions = DB::table('positions')
            ->join('companies', 'companies.id', '=', 'positions.company_id')
            ->select('positions.*', 'companies.name as company_name')
            ->orderBy('positions.name')
            ->paginate(20);

        return view('masters.positions.index', compact('positions'));
    }

    public function create()
    {
        $companies = DB::table('companies')->pluck('name', 'id');
        return view('masters.positions.create', compact('companies'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'company_id' => 'required|integer|exists:companies,id',
            'name' => 'required|string|max:191',
            'grade' => 'nullable|string|max:50',
        ]);

        $now = now();
        DB::table('positions')->insert(array_merge($data, [
            'created_at' => $now,
            'updated_at' => $now,
        ]));

        return redirect()->route('positions.index')->with('success', 'Jabatan ditambahkan.');
    }

    public function edit(int $id)
    {
        $position = DB::table('positions')->find($id);
        abort_unless($position, 404);
        $companies = DB::table('companies')->pluck('name', 'id');

        return view('masters.positions.edit', compact('position', 'companies'));
    }

    public function update(Request $request, int $id)
    {
        $position = DB::table('positions')->find($id);
        abort_unless($position, 404);

        $data = $request->validate([
            'company_id' => 'required|integer|exists:companies,id',
            'name' => 'required|string|max:191',
            'grade' => 'nullable|string|max:50',
        ]);

        $data['updated_at'] = now();
        DB::table('positions')->where('id', $id)->update($data);

        return redirect()->route('positions.index')->with('success', 'Jabatan diperbarui.');
    }

    public function destroy(int $id)
    {
        DB::table('positions')->where('id', $id)->delete();
        return redirect()->route('positions.index')->with('success', 'Jabatan dihapus.');
    }
}

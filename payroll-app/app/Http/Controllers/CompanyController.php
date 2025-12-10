<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CompanyController extends Controller
{
    public function index()
    {
        $companies = DB::table('companies')
            ->orderBy('name')
            ->paginate(20);

        return view('masters.companies.index', compact('companies'));
    }

    public function create()
    {
        return view('masters.companies.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:191',
            'code' => 'nullable|string|max:50|unique:companies,code',
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:191',
            'npwp' => 'nullable|string|max:64',
            'province_code' => 'nullable|string',
            'province_name' => 'nullable|string',
            'city_code' => 'nullable|string',
            'city_name' => 'nullable|string',
            'district_code' => 'nullable|string',
            'district_name' => 'nullable|string',
            'village_code' => 'nullable|string',
            'village_name' => 'nullable|string',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ]);

        $now = now();
        DB::table('companies')->insert(array_merge($data, [
            'created_at' => $now,
            'updated_at' => $now,
        ]));

        return redirect()->route('companies.index')->with('success', 'Instansi ditambahkan.');
    }

    public function edit(int $id)
    {
        $company = DB::table('companies')->find($id);

        abort_unless($company, 404);

        return view('masters.companies.edit', compact('company'));
    }

    public function update(Request $request, int $id)
    {
        $company = DB::table('companies')->find($id);
        abort_unless($company, 404);

        $data = $request->validate([
            'name' => 'required|string|max:191',
            'code' => 'nullable|string|max:50|unique:companies,code,' . $id,
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:191',
            'npwp' => 'nullable|string|max:64',
            'province_code' => 'nullable|string',
            'province_name' => 'nullable|string',
            'city_code' => 'nullable|string',
            'city_name' => 'nullable|string',
            'district_code' => 'nullable|string',
            'district_name' => 'nullable|string',
            'village_code' => 'nullable|string',
            'village_name' => 'nullable|string',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ]);

        $data['updated_at'] = now();
        DB::table('companies')->where('id', $id)->update($data);

        return redirect()->route('companies.index')->with('success', 'Instansi diperbarui.');
    }

    public function destroy(int $id)
    {
        DB::table('companies')->where('id', $id)->delete();
        return redirect()->route('companies.index')->with('success', 'Instansi dihapus.');
    }
}

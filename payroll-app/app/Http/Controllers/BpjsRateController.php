<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BpjsRateController extends Controller
{
    public function index()
    {
        $rates = DB::table('bpjs_rates')
            ->join('companies', 'companies.id', '=', 'bpjs_rates.company_id')
            ->select('bpjs_rates.*', 'companies.name as company_name')
            ->orderBy('effective_from', 'desc')
            ->paginate(20);

        return view('masters.bpjs_rates.index', compact('rates'));
    }

    public function create()
    {
        $companies = DB::table('companies')->pluck('name', 'id');
        return view('masters.bpjs_rates.create', compact('companies'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'company_id' => 'required|integer|exists:companies,id',
            'program' => 'required|in:bpjs_kesehatan,jht,jkk,jkm,jp',
            'employee_rate' => 'required|numeric|min:0',
            'employer_rate' => 'required|numeric|min:0',
            'salary_cap_min' => 'nullable|numeric|min:0',
            'salary_cap_max' => 'nullable|numeric|min:0',
            'effective_from' => 'required|date',
            'effective_to' => 'nullable|date|after_or_equal:effective_from',
        ]);

        $now = now();
        DB::table('bpjs_rates')->insert(array_merge($data, [
            'created_at' => $now,
            'updated_at' => $now,
        ]));

        return redirect()->route('bpjs-rates.index')->with('success', 'Tarif BPJS ditambahkan.');
    }

    public function edit(int $id)
    {
        $bpjsRate = DB::table('bpjs_rates')->find($id);
        abort_unless($bpjsRate, 404);
        $companies = DB::table('companies')->pluck('name', 'id');

        return view('masters.bpjs_rates.edit', compact('bpjsRate', 'companies'));
    }

    public function update(Request $request, int $id)
    {
        $bpjsRate = DB::table('bpjs_rates')->find($id);
        abort_unless($bpjsRate, 404);

        $data = $request->validate([
            'company_id' => 'required|integer|exists:companies,id',
            'program' => 'required|in:bpjs_kesehatan,jht,jkk,jkm,jp',
            'employee_rate' => 'required|numeric|min:0',
            'employer_rate' => 'required|numeric|min:0',
            'salary_cap_min' => 'nullable|numeric|min:0',
            'salary_cap_max' => 'nullable|numeric|min:0',
            'effective_from' => 'required|date',
            'effective_to' => 'nullable|date|after_or_equal:effective_from',
        ]);

        $data['updated_at'] = now();
        DB::table('bpjs_rates')->where('id', $id)->update($data);

        return redirect()->route('bpjs-rates.index')->with('success', 'Tarif BPJS diperbarui.');
    }

    public function destroy(int $id)
    {
        DB::table('bpjs_rates')->where('id', $id)->delete();
        return redirect()->route('bpjs-rates.index')->with('success', 'Tarif BPJS dihapus.');
    }
}

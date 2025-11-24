<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TaxRateController extends Controller
{
    public function index()
    {
        $taxRates = DB::table('tax_rates')
            ->join('companies', 'companies.id', '=', 'tax_rates.company_id')
            ->select('tax_rates.*', 'companies.name as company_name')
            ->orderBy('year', 'desc')
            ->orderBy('range_min')
            ->paginate(20);

        return view('masters.tax_rates.index', compact('taxRates'));
    }

    public function create()
    {
        $companies = DB::table('companies')->pluck('name', 'id');
        return view('masters.tax_rates.create', compact('companies'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'company_id' => 'required|integer|exists:companies,id',
            'year' => 'required|integer|min:2000',
            'range_min' => 'required|numeric|min:0',
            'range_max' => 'nullable|numeric|min:0',
            'rate_percent' => 'required|numeric|min:0',
        ]);

        $now = now();
        DB::table('tax_rates')->insert(array_merge($data, [
            'created_at' => $now,
            'updated_at' => $now,
        ]));

        return redirect()->route('tax-rates.index')->with('success', 'Tarif pajak ditambahkan.');
    }

    public function edit(int $id)
    {
        $taxRate = DB::table('tax_rates')->find($id);
        abort_unless($taxRate, 404);
        $companies = DB::table('companies')->pluck('name', 'id');

        return view('masters.tax_rates.edit', compact('taxRate', 'companies'));
    }

    public function update(Request $request, int $id)
    {
        $taxRate = DB::table('tax_rates')->find($id);
        abort_unless($taxRate, 404);

        $data = $request->validate([
            'company_id' => 'required|integer|exists:companies,id',
            'year' => 'required|integer|min:2000',
            'range_min' => 'required|numeric|min:0',
            'range_max' => 'nullable|numeric|min:0',
            'rate_percent' => 'required|numeric|min:0',
        ]);

        $data['updated_at'] = now();
        DB::table('tax_rates')->where('id', $id)->update($data);

        return redirect()->route('tax-rates.index')->with('success', 'Tarif pajak diperbarui.');
    }

    public function destroy(int $id)
    {
        DB::table('tax_rates')->where('id', $id)->delete();
        return redirect()->route('tax-rates.index')->with('success', 'Tarif pajak dihapus.');
    }
}

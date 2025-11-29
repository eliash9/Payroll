<?php

namespace App\Http\Controllers;

use App\Models\TaxRate;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class TaxRateController extends Controller
{
    public function index()
    {
        $query = TaxRate::query();
        
        if (Auth::user()->company_id) {
            $query->where('company_id', Auth::user()->company_id);
        }

        $taxRates = $query->orderBy('year', 'desc')
            ->orderBy('range_min')
            ->paginate(20);

        return view('masters.tax_rates.index', compact('taxRates'));
    }

    public function create()
    {
        $companiesQuery = Company::query();
        if (Auth::user()->company_id) {
            $companiesQuery->where('id', Auth::user()->company_id);
        }
        $companies = $companiesQuery->pluck('name', 'id');
        
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

        if (Auth::user()->company_id && $data['company_id'] != Auth::user()->company_id) {
            abort(403, 'Unauthorized company selection.');
        }

        TaxRate::create($data);

        return redirect()->route('tax-rates.index')->with('success', 'Tarif pajak ditambahkan.');
    }

    public function edit(int $id)
    {
        $taxRate = TaxRate::findOrFail($id);
        
        if (Auth::user()->company_id && $taxRate->company_id != Auth::user()->company_id) {
            abort(403, 'Unauthorized access to this tax rate.');
        }

        $companiesQuery = Company::query();
        if (Auth::user()->company_id) {
            $companiesQuery->where('id', Auth::user()->company_id);
        }
        $companies = $companiesQuery->pluck('name', 'id');

        return view('masters.tax_rates.edit', compact('taxRate', 'companies'));
    }

    public function update(Request $request, int $id)
    {
        $taxRate = TaxRate::findOrFail($id);
        
        if (Auth::user()->company_id && $taxRate->company_id != Auth::user()->company_id) {
            abort(403, 'Unauthorized access to this tax rate.');
        }

        $data = $request->validate([
            'company_id' => 'required|integer|exists:companies,id',
            'year' => 'required|integer|min:2000',
            'range_min' => 'required|numeric|min:0',
            'range_max' => 'nullable|numeric|min:0',
            'rate_percent' => 'required|numeric|min:0',
        ]);
        
        if (Auth::user()->company_id && $data['company_id'] != Auth::user()->company_id) {
            abort(403, 'Unauthorized company selection.');
        }

        $taxRate->update($data);

        return redirect()->route('tax-rates.index')->with('success', 'Tarif pajak diperbarui.');
    }

    public function destroy(int $id)
    {
        $taxRate = TaxRate::findOrFail($id);
        
        if (Auth::user()->company_id && $taxRate->company_id != Auth::user()->company_id) {
            abort(403, 'Unauthorized access to this tax rate.');
        }

        $taxRate->delete();
        return redirect()->route('tax-rates.index')->with('success', 'Tarif pajak dihapus.');
    }
}

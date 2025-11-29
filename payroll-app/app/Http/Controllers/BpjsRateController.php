<?php

namespace App\Http\Controllers;

use App\Models\BpjsRate;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class BpjsRateController extends Controller
{
    public function index()
    {
        $query = BpjsRate::query();
        
        if (Auth::user()->company_id) {
            $query->where('company_id', Auth::user()->company_id);
        }

        $rates = $query->orderBy('effective_from', 'desc')->paginate(20);

        return view('masters.bpjs_rates.index', compact('rates'));
    }

    public function create()
    {
        $companiesQuery = Company::query();
        if (Auth::user()->company_id) {
            $companiesQuery->where('id', Auth::user()->company_id);
        }
        $companies = $companiesQuery->pluck('name', 'id');
        
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

        if (Auth::user()->company_id && $data['company_id'] != Auth::user()->company_id) {
            abort(403, 'Unauthorized company selection.');
        }

        BpjsRate::create($data);

        return redirect()->route('bpjs-rates.index')->with('success', 'Tarif BPJS ditambahkan.');
    }

    public function edit(int $id)
    {
        $bpjsRate = BpjsRate::findOrFail($id);
        
        if (Auth::user()->company_id && $bpjsRate->company_id != Auth::user()->company_id) {
            abort(403, 'Unauthorized access to this rate.');
        }

        $companiesQuery = Company::query();
        if (Auth::user()->company_id) {
            $companiesQuery->where('id', Auth::user()->company_id);
        }
        $companies = $companiesQuery->pluck('name', 'id');

        return view('masters.bpjs_rates.edit', compact('bpjsRate', 'companies'));
    }

    public function update(Request $request, int $id)
    {
        $bpjsRate = BpjsRate::findOrFail($id);
        
        if (Auth::user()->company_id && $bpjsRate->company_id != Auth::user()->company_id) {
            abort(403, 'Unauthorized access to this rate.');
        }

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
        
        if (Auth::user()->company_id && $data['company_id'] != Auth::user()->company_id) {
            abort(403, 'Unauthorized company selection.');
        }

        $bpjsRate->update($data);

        return redirect()->route('bpjs-rates.index')->with('success', 'Tarif BPJS diperbarui.');
    }

    public function destroy(int $id)
    {
        $bpjsRate = BpjsRate::findOrFail($id);
        
        if (Auth::user()->company_id && $bpjsRate->company_id != Auth::user()->company_id) {
            abort(403, 'Unauthorized access to this rate.');
        }

        $bpjsRate->delete();
        return redirect()->route('bpjs-rates.index')->with('success', 'Tarif BPJS dihapus.');
    }
}

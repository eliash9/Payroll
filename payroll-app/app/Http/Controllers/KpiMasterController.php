<?php

namespace App\Http\Controllers;

use App\Models\KpiMaster;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class KpiMasterController extends Controller
{
    public function index()
    {
        $query = KpiMaster::query();
        
        if (Auth::user()->company_id) {
            $query->where('company_id', Auth::user()->company_id);
        }

        $kpis = $query->orderBy('name')->paginate(20);

        return view('masters.kpi.index', compact('kpis'));
    }

    public function create()
    {
        $companiesQuery = Company::query();
        if (Auth::user()->company_id) {
            $companiesQuery->where('id', Auth::user()->company_id);
        }
        $companies = $companiesQuery->pluck('name', 'id');
        
        return view('masters.kpi.create', compact('companies'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'company_id' => 'required|integer|exists:companies,id',
            'name' => 'required|string|max:191',
            'code' => 'required|string|max:50|unique:kpi_master,code',
            'type' => 'required|in:numeric,percent,boolean',
            'target_default' => 'nullable|numeric',
            'weight_default' => 'nullable|numeric',
            'period_type' => 'required|in:monthly,weekly,quarterly,yearly',
            'category' => 'required|in:individual,team,division',
            'description' => 'nullable|string',
        ]);

        if (Auth::user()->company_id && $data['company_id'] != Auth::user()->company_id) {
            abort(403, 'Unauthorized company selection.');
        }

        KpiMaster::create($data);

        return redirect()->route('kpi.index')->with('success', 'KPI ditambahkan.');
    }

    public function edit(int $id)
    {
        $kpi = KpiMaster::findOrFail($id);
        
        if (Auth::user()->company_id && $kpi->company_id != Auth::user()->company_id) {
            abort(403, 'Unauthorized access to this KPI.');
        }

        $companiesQuery = Company::query();
        if (Auth::user()->company_id) {
            $companiesQuery->where('id', Auth::user()->company_id);
        }
        $companies = $companiesQuery->pluck('name', 'id');

        return view('masters.kpi.edit', compact('kpi', 'companies'));
    }

    public function update(Request $request, int $id)
    {
        $kpi = KpiMaster::findOrFail($id);
        
        if (Auth::user()->company_id && $kpi->company_id != Auth::user()->company_id) {
            abort(403, 'Unauthorized access to this KPI.');
        }

        $data = $request->validate([
            'company_id' => 'required|integer|exists:companies,id',
            'name' => 'required|string|max:191',
            'code' => 'required|string|max:50|unique:kpi_master,code,' . $id,
            'type' => 'required|in:numeric,percent,boolean',
            'target_default' => 'nullable|numeric',
            'weight_default' => 'nullable|numeric',
            'period_type' => 'required|in:monthly,weekly,quarterly,yearly',
            'category' => 'required|in:individual,team,division',
            'description' => 'nullable|string',
        ]);
        
        if (Auth::user()->company_id && $data['company_id'] != Auth::user()->company_id) {
            abort(403, 'Unauthorized company selection.');
        }

        $kpi->update($data);

        return redirect()->route('kpi.index')->with('success', 'KPI diperbarui.');
    }

    public function destroy(int $id)
    {
        $kpi = KpiMaster::findOrFail($id);
        
        if (Auth::user()->company_id && $kpi->company_id != Auth::user()->company_id) {
            abort(403, 'Unauthorized access to this KPI.');
        }

        $kpi->delete();
        return redirect()->route('kpi.index')->with('success', 'KPI dihapus.');
    }
}

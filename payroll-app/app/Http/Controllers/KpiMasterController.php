<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KpiMasterController extends Controller
{
    public function index()
    {
        $kpis = DB::table('kpi_master')
            ->join('companies', 'companies.id', '=', 'kpi_master.company_id')
            ->select('kpi_master.*', 'companies.name as company_name')
            ->orderBy('name')
            ->paginate(20);

        return view('masters.kpi.index', compact('kpis'));
    }

    public function create()
    {
        $companies = DB::table('companies')->pluck('name', 'id');
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

        $now = now();
        DB::table('kpi_master')->insert(array_merge($data, [
            'created_at' => $now,
            'updated_at' => $now,
        ]));

        return redirect()->route('kpi.index')->with('success', 'KPI ditambahkan.');
    }

    public function edit(int $id)
    {
        $kpi = DB::table('kpi_master')->find($id);
        abort_unless($kpi, 404);
        $companies = DB::table('companies')->pluck('name', 'id');

        return view('masters.kpi.edit', compact('kpi', 'companies'));
    }

    public function update(Request $request, int $id)
    {
        $kpi = DB::table('kpi_master')->find($id);
        abort_unless($kpi, 404);

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

        $data['updated_at'] = now();
        DB::table('kpi_master')->where('id', $id)->update($data);

        return redirect()->route('kpi.index')->with('success', 'KPI diperbarui.');
    }

    public function destroy(int $id)
    {
        DB::table('kpi_master')->where('id', $id)->delete();
        return redirect()->route('kpi.index')->with('success', 'KPI dihapus.');
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\VolunteerPayrollService;
use App\Services\RegularPayrollService;
use RuntimeException;

class PayrollPeriodController extends Controller
{
    public function index()
    {
        $status = request('status');
        $q = request('q');
        $companyId = request('company_id');

        $periods = DB::table('payroll_periods')
            ->select('payroll_periods.*')
            ->selectSub('select count(*) from payroll_headers where payroll_headers.payroll_period_id = payroll_periods.id', 'slip_count')
            ->selectSub('select sum(net_income) from payroll_headers where payroll_headers.payroll_period_id = payroll_periods.id', 'net_total')
            ->when($companyId, fn($query) => $query->where('company_id', $companyId))
            ->when($status, fn($query) => $query->where('status', $status))
            ->when($q, fn($query) => $query->where(function ($sub) use ($q) {
                $sub->where('code', 'like', "%{$q}%")->orWhere('name', 'like', "%{$q}%");
            }))
            ->orderByDesc('start_date')
            ->paginate(20)
            ->withQueryString();

        $companies = DB::table('companies')->pluck('name', 'id');

        return view('payroll.index', compact('periods', 'companies', 'companyId'));
    }

    public function generateVolunteer(int $id, VolunteerPayrollService $service)
    {
        $service->generateForPeriod($id);
        DB::table('payroll_periods')->where('id', $id)->update([
            'status' => 'calculated',
            'locked_at' => now(),
            'updated_at' => now(),
        ]);
        return back()->with('success', 'Payroll relawan digenerate');
    }

    public function generateRegular(int $id, RegularPayrollService $service)
    {
        $service->generateForPeriod($id);
        DB::table('payroll_periods')->where('id', $id)->update([
            'status' => 'calculated',
            'locked_at' => now(),
            'updated_at' => now(),
        ]);
        return back()->with('success', 'Payroll reguler digenerate');
    }

    public function approve(int $id)
    {
        DB::table('payroll_periods')->where('id', $id)->update([
            'status' => 'approved',
            'locked_at' => now(),
            'updated_at' => now(),
        ]);

        return back()->with('success', 'Payroll periode disetujui.');
    }

    public function previewRegular(int $id, RegularPayrollService $service)
    {
        $period = DB::table('payroll_periods')->find($id);
        $data = $service->simulateForPeriod($id);
        return view('payroll.preview', [
            'period' => $period,
            'data' => $data,
            'title' => 'Preview Payroll Reguler',
        ]);
    }

    public function previewVolunteer(int $id, VolunteerPayrollService $service)
    {
        $period = DB::table('payroll_periods')->find($id);
        $data = $service->simulateForPeriod($id);
        return view('payroll.preview', [
            'period' => $period,
            'data' => $data,
            'title' => 'Preview Payroll Relawan',
        ]);
    }

    public function create()
    {
        $companies = DB::table('companies')->pluck('name', 'id');
        return view('payroll.create', compact('companies'));
    }

    public function store()
    {
        $data = request()->validate([
            'company_id' => 'required|integer|exists:companies,id',
            'code' => 'required|string|max:50|unique:payroll_periods,code',
            'name' => 'nullable|string|max:100',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        DB::table('payroll_periods')->insert(array_merge($data, [
            'status' => 'draft',
            'created_at' => now(),
            'updated_at' => now(),
        ]));

        return redirect()->route('payroll.periods.index')->with('success', 'Periode payroll dibuat.');
    }

    public function destroy(int $id)
    {
        $period = DB::table('payroll_periods')->find($id);
        if (!$period) {
            return back()->with('error', 'Periode tidak ditemukan.');
        }

        if (in_array($period->status, ['approved', 'closed'])) {
            return back()->with('error', 'Periode sudah disetujui/ditutup, tidak bisa dihapus.');
        }

        DB::table('payroll_periods')->where('id', $id)->delete();

        return redirect()->route('payroll.periods.index')->with('success', 'Periode payroll dibatalkan/dihapus.');
    }
}

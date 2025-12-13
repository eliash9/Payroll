<?php

namespace App\Http\Controllers;

use App\Models\PayrollPeriod;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
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
        
        // Enforce company scope
        if (Auth::user()->company_id) {
            $companyId = Auth::user()->company_id;
        }

        $query = PayrollPeriod::query()
            ->select('payroll_periods.*')
            ->selectSub('select count(*) from payroll_headers where payroll_headers.payroll_period_id = payroll_periods.id', 'slip_count')
            ->selectSub('select sum(net_income) from payroll_headers where payroll_headers.payroll_period_id = payroll_periods.id', 'net_total');

        if ($companyId) {
            $query->where('company_id', $companyId);
        }

        if ($status) {
            $query->where('status', $status);
        }

        if ($q) {
            $query->where(function ($sub) use ($q) {
                $sub->where('code', 'like', "%{$q}%")->orWhere('name', 'like', "%{$q}%");
            });
        }

        $periods = $query->orderByDesc('start_date')
            ->paginate(20)
            ->withQueryString();

        $companiesQuery = Company::query();
        if (Auth::user()->company_id) {
            $companiesQuery->where('id', Auth::user()->company_id);
        }
        $companies = $companiesQuery->pluck('name', 'id');

        return view('payroll.index', compact('periods', 'companies', 'companyId'));
    }

    public function show($id)
    {
        $period = PayrollPeriod::findOrFail($id);
        
        if (Auth::user()->company_id && $period->company_id != Auth::user()->company_id) {
            abort(403, 'Unauthorized access to this period.');
        }

        // Summary Statistics
        $regularCount = DB::table('employees')
            ->where('company_id', $period->company_id)
            ->where('is_volunteer', false)
            ->where('status', 'active')
            ->count();

        $volunteerCount = DB::table('employees')
            ->where('company_id', $period->company_id)
            ->where('is_volunteer', true)
            ->where('status', 'active')
            ->count();

        $generatedRegular = DB::table('payroll_headers')
            ->join('employees', 'employees.id', '=', 'payroll_headers.employee_id')
            ->where('payroll_headers.payroll_period_id', $id)
            ->where('employees.is_volunteer', false)
            ->count();

        $generatedVolunteer = DB::table('payroll_headers')
            ->join('employees', 'employees.id', '=', 'payroll_headers.employee_id')
            ->where('payroll_headers.payroll_period_id', $id)
            ->where('employees.is_volunteer', true)
            ->count();

        $totalNet = DB::table('payroll_headers')
            ->where('payroll_period_id', $id)
            ->sum('net_income');
            
        // Load recent headers for display
        $headers = \App\Models\PayrollHeader::with('employee')
            ->where('payroll_period_id', $id)
            ->orderByDesc('net_income')
            ->paginate(50);

        return view('payroll.show', compact(
            'period', 
            'regularCount', 
            'volunteerCount', 
            'generatedRegular', 
            'generatedVolunteer', 
            'totalNet',
            'headers'
        ));
    }

    public function generateVolunteer(int $id, VolunteerPayrollService $service)
    {
        $period = PayrollPeriod::findOrFail($id);
        if (Auth::user()->company_id && $period->company_id != Auth::user()->company_id) {
            abort(403, 'Unauthorized access to this period.');
        }

        $service->generateForPeriod($id);
        
        $period->update([
            'status' => 'calculated',
            'locked_at' => now(),
            'updated_at' => now(),
        ]);
        
        return back()->with('success', 'Payroll relawan digenerate');
    }

    public function generateRegular(int $id, RegularPayrollService $service)
    {
        $period = PayrollPeriod::findOrFail($id);
        if (Auth::user()->company_id && $period->company_id != Auth::user()->company_id) {
            abort(403, 'Unauthorized access to this period.');
        }

        $service->generateForPeriod($id);
        
        $period->update([
            'status' => 'calculated',
            'locked_at' => now(),
            'updated_at' => now(),
        ]);
        
        return back()->with('success', 'Payroll reguler digenerate');
    }

    public function approve(int $id)
    {
        $period = PayrollPeriod::findOrFail($id);
        if (Auth::user()->company_id && $period->company_id != Auth::user()->company_id) {
            abort(403, 'Unauthorized access to this period.');
        }

        $period->update([
            'status' => 'approved',
            'locked_at' => now(),
            'updated_at' => now(),
        ]);

        return back()->with('success', 'Payroll periode disetujui.');
    }

    public function previewRegular(int $id, RegularPayrollService $service)
    {
        $period = PayrollPeriod::findOrFail($id);
        if (Auth::user()->company_id && $period->company_id != Auth::user()->company_id) {
            abort(403, 'Unauthorized access to this period.');
        }

        $data = $service->simulateForPeriod($id);
        return view('payroll.preview', [
            'period' => $period,
            'data' => $data,
            'title' => 'Preview Payroll Reguler',
        ]);
    }

    public function previewVolunteer(int $id, VolunteerPayrollService $service)
    {
        $period = PayrollPeriod::findOrFail($id);
        if (Auth::user()->company_id && $period->company_id != Auth::user()->company_id) {
            abort(403, 'Unauthorized access to this period.');
        }

        $data = $service->simulateForPeriod($id);
        return view('payroll.preview', [
            'period' => $period,
            'data' => $data,
            'title' => 'Preview Payroll Relawan',
        ]);
    }

    public function create()
    {
        $companiesQuery = Company::query();
        if (Auth::user()->company_id) {
            $companiesQuery->where('id', Auth::user()->company_id);
        }
        $companies = $companiesQuery->pluck('name', 'id');
        
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

        if (Auth::user()->company_id && $data['company_id'] != Auth::user()->company_id) {
            abort(403, 'Unauthorized company selection.');
        }

        PayrollPeriod::create(array_merge($data, [
            'status' => 'draft',
            'created_at' => now(),
            'updated_at' => now(),
        ]));

        return redirect()->route('payroll.periods.index')->with('success', 'Periode payroll dibuat.');
    }

    public function destroy(int $id)
    {
        $period = PayrollPeriod::findOrFail($id);
        if (Auth::user()->company_id && $period->company_id != Auth::user()->company_id) {
            abort(403, 'Unauthorized access to this period.');
        }

        if (in_array($period->status, ['approved', 'closed'])) {
            return back()->with('error', 'Periode sudah disetujui/ditutup, tidak bisa dihapus.');
        }

        $period->delete();

        return redirect()->route('payroll.periods.index')->with('success', 'Periode payroll dibatalkan/dihapus.');
    }
}

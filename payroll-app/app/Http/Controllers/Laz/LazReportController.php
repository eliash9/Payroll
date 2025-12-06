<?php

namespace App\Http\Controllers\Laz;

use App\Http\Controllers\Controller;
use App\Models\Application;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class LazReportController extends Controller
{
    private function getReportData(): array
    {
        $perProgram = Application::select(
            'program_id',
            DB::raw('count(*) as total_submitted'),
            DB::raw("sum(case when status = 'approved' then 1 else 0 end) as total_approved"),
            DB::raw("sum(case when status = 'rejected' then 1 else 0 end) as total_rejected"),
            DB::raw('sum(requested_amount) as total_requested')
        )
            ->with('program')
            ->groupBy('program_id')
            ->get();

        $approvedAmounts = DB::table('approvals')
            ->select('application_id', DB::raw('max(approved_amount) as approved_amount'))
            ->groupBy('application_id');

        $perProgramApproved = DB::table('applications as a')
            ->select('a.program_id', DB::raw('sum(ap.approved_amount) as approved_sum'))
            ->leftJoinSub($approvedAmounts, 'ap', fn ($join) => $join->on('ap.application_id', '=', 'a.id'))
            ->groupBy('a.program_id')
            ->pluck('approved_sum', 'program_id');

        $disbursedAmounts = DB::table('disbursements')
            ->select('application_id', DB::raw('sum(total_amount) as total_disbursed'))
            ->groupBy('application_id');

        $perProgramDisbursed = DB::table('applications as a')
            ->select('a.program_id', DB::raw('sum(d.total_disbursed) as disbursed_sum'))
            ->leftJoinSub($disbursedAmounts, 'd', fn ($join) => $join->on('d.application_id', '=', 'a.id'))
            ->groupBy('a.program_id')
            ->pluck('disbursed_sum', 'program_id');

        $perMonth = Application::select(
            DB::raw("DATE_FORMAT(created_at, '%Y-%m') as month"),
            DB::raw('count(*) as total'),
            DB::raw('sum(requested_amount) as total_requested')
        )
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $segmentApplicant = Application::select(
            'applicant_type',
            DB::raw('count(*) as total'),
            DB::raw('sum(requested_amount) as total_requested')
        )->groupBy('applicant_type')->get();

        $segmentProvince = Application::select(
            'location_province',
            DB::raw('count(*) as total'),
            DB::raw('sum(requested_amount) as total_requested')
        )->groupBy('location_province')->orderByDesc('total')->limit(20)->get();

        return [
            'perProgram' => $perProgram,
            'perProgramApproved' => $perProgramApproved,
            'perProgramDisbursed' => $perProgramDisbursed,
            'perMonth' => $perMonth,
            'segmentApplicant' => $segmentApplicant,
            'segmentProvince' => $segmentProvince,
        ];
    }

    public function index(): View
    {
        return view('laz.reports.index', $this->getReportData());
    }

    public function exportDetailExcel()
    {
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\Laz\LazDetailExport, 'laporan_detail_laz.xlsx');
    }

    public function exportRekapExcel()
    {
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\Laz\LazRekapExport($this->getReportData()), 'laporan_rekap_laz.xlsx');
    }

    public function exportDetailPdf()
    {
        $applications = Application::with(['program', 'period', 'applicant', 'organization', 'branch'])->get();
        $companyName = \App\Models\Company::first()->name ?? config('app.name');
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('laz.reports.pdf.detail', [
            'applications' => $applications,
            'companyName' => $companyName
        ]);
        return $pdf->download('laporan_detail_laz.pdf');
    }

    public function exportRekapPdf()
    {
        $data = $this->getReportData();
        $data['companyName'] = \App\Models\Company::first()->name ?? config('app.name');
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('laz.reports.pdf.rekap', $data);
        return $pdf->download('laporan_rekap_laz.pdf');
    }
}

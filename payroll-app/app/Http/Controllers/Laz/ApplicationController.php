<?php

namespace App\Http\Controllers\Laz;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Branch;
use App\Models\Program;
use App\Models\ProgramPeriod;
use App\Models\Survey;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ApplicationController extends Controller
{
    public function index(Request $request): View
    {
        $query = Application::with(['program', 'period', 'applicant', 'organization', 'branch'])
            ->orderByDesc('created_at');

        if ($request->user()->hasRole('admin_cabang') && ! $request->user()->hasRole(['admin_pusat', 'super_admin'])) {
            $query->where('branch_id', $request->user()->branch_id);
        }

        $query->when($request->program_id, fn ($q, $id) => $q->where('program_id', $id))
            ->when($request->status, fn ($q, $status) => $q->where('status', $status))
            ->when($request->branch_id, fn ($q, $id) => $q->where('branch_id', $id))
            ->when($request->period_id, fn ($q, $id) => $q->where('program_period_id', $id))
            ->when($request->date_from, fn ($q, $date) => $q->whereDate('created_at', '>=', $date))
            ->when($request->date_to, fn ($q, $date) => $q->whereDate('created_at', '<=', $date));

        $applications = $query->paginate(20)->withQueryString();

        return view('laz.applications.index', [
            'applications' => $applications,
            'programs' => Program::orderBy('name')->get(),
            'periods' => ProgramPeriod::orderBy('open_at', 'desc')->get(),
            'branches' => Branch::orderBy('name')->get(),
        ]);
    }

    public function show(Application $application): View
    {
        $application->load(['program', 'period', 'applicant', 'organization', 'documents', 'surveys.surveyor', 'surveys.photos', 'approvals.approver', 'disbursements.officer', 'disbursements.items', 'disbursements.proofs']);

        if ($requestBranch = request()->user()->branch_id) {
            if (request()->user()->hasRole('admin_cabang') && $application->branch_id !== $requestBranch) {
                abort(403);
            }
        }

        return view('laz.applications.show', [
            'application' => $application,
            'surveyors' => User::whereHas('roles', fn ($q) => $q->where('name', 'surveyor'))->get(),
            'branches' => Branch::orderBy('name')->get(),
        ]);
    }

    public function updateStatus(Request $request, Application $application): RedirectResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:'.implode(',', Application::STATUSES),
            'branch_id' => 'nullable|exists:branches,id',
        ]);

        if ($request->user()->hasRole('admin_cabang') && ! $request->user()->hasRole(['admin_pusat', 'super_admin'])) {
            if ($application->branch_id !== $request->user()->branch_id) {
                abort(403);
            }
        }

        $application->update([
            'status' => $validated['status'],
            'branch_id' => $validated['branch_id'] ?? $application->branch_id,
        ]);

        return back()->with('success', 'Status diperbarui');
    }

    public function assignSurveyor(Request $request, Application $application): RedirectResponse
    {
        $validated = $request->validate([
            'surveyor_id' => 'required|exists:users,id',
        ]);

        if ($request->user()->hasRole('admin_cabang') && ! $request->user()->hasRole(['admin_pusat', 'super_admin'])) {
            if ($application->branch_id !== $request->user()->branch_id) {
                abort(403);
            }
        }

        $survey = Survey::firstOrCreate(
            ['application_id' => $application->id, 'surveyor_id' => $validated['surveyor_id']],
            []
        );

        $application->update(['status' => 'survey_assigned']);

        return back()->with('success', 'Surveyor ditugaskan (ID '.$survey->id.')');
    }
}

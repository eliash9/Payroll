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

use App\Traits\LazWhatsAppSender;

class ApplicationController extends Controller
{
    use LazWhatsAppSender;

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
            ->when($request->search, function ($q, $search) {
                $q->where(function ($sq) use ($search) {
                    $sq->where('code', 'like', "%{$search}%")
                        ->orWhereHas('applicant', fn ($aq) => $aq->where('full_name', 'like', "%{$search}%")->orWhere('national_id', 'like', "%{$search}%"));
                });
            })
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
        $application->load(['program', 'period', 'applicant', 'organization', 'documents', 'surveys.surveyor', 'surveys.photos', 'approvals.approver', 'disbursements.officer', 'disbursements.items', 'disbursements.proofs', 'beneficiaries']);

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

        $this->sendNotifications($application);

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
        
        $this->sendNotifications($application);

        return back()->with('success', 'Surveyor ditugaskan (ID '.$survey->id.')');
    }

    /**
     * Send email and WhatsApp notifications
     */
    private function sendNotifications(Application $application)
    {
        // 1. Email
        if ($application->applicant && $application->applicant->email) {
            try {
                \Illuminate\Support\Facades\Mail::to($application->applicant->email)
                    ->send(new \App\Mail\ApplicationStatusUpdatedMail($application));
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Failed to send status update email: ' . $e->getMessage());
            }
        }

        // 2. WhatsApp
        if ($application->applicant && $application->applicant->phone) {
            try {
                $template = \App\Models\LazSetting::where('key', 'email_status_update_body')->value('value') 
                    ?? "Halo {applicant_name},\n\nStatus permohonan bantuan Anda ({code}) telah diperbarui menjadi: {status}.\n\nSalam,\nTim LAZ";
                
                $message = str_replace(
                    ['{applicant_name}', '{code}', '{status}', '{program_name}'],
                    [
                        $application->applicant->full_name,
                        $application->code,
                        $application->status,
                        $application->program->name ?? '-'
                    ],
                    $template
                );

                $this->sendWhatsAppMessage($application->applicant->phone, $message);
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Failed to send status update WA: ' . $e->getMessage());
            }
        }
    }
}

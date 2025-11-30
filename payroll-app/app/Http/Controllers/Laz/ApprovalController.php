<?php

namespace App\Http\Controllers\Laz;

use App\Http\Controllers\Controller;
use App\Http\Requests\ApprovalRequest;
use App\Models\Application;
use App\Models\Approval;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ApprovalController extends Controller
{
    public function index(Request $request): View
    {
        $applications = Application::with(['program', 'applicant', 'organization', 'surveys'])
            ->where('status', 'waiting_approval')
            ->orderByDesc('updated_at')
            ->paginate(20);

        return view('laz.approvals.index', compact('applications'));
    }

    public function store(ApprovalRequest $request, Application $application): RedirectResponse
    {
        $data = $request->validated();

        $approval = new Approval([
            'application_id' => $application->id,
            'approver_id' => $request->user()->id,
            'decided_at' => now(),
            'decision' => $data['decision'],
            'approved_amount' => $data['approved_amount'] ?? null,
            'approved_aid_type' => $data['approved_aid_type'] ?? null,
            'notes' => $data['notes'] ?? null,
        ]);
        $approval->save();

        $status = match ($data['decision']) {
            'approved' => 'approved',
            'rejected' => 'rejected',
            default => 'waiting_approval',
        };

        $application->update(['status' => $status]);

        return back()->with('success', 'Keputusan disimpan');
    }
}

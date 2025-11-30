<?php

namespace App\Http\Controllers\Laz;

use App\Http\Controllers\Controller;
use App\Http\Requests\DisbursementRequest;
use App\Models\Application;
use App\Models\Disbursement;
use App\Models\DisbursementItem;
use App\Models\DisbursementProof;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DisbursementController extends Controller
{
    public function index(Request $request): View
    {
        $applications = Application::with(['program', 'applicant', 'organization'])
            ->whereIn('status', ['approved', 'disbursement_in_progress'])
            ->orderByDesc('updated_at')
            ->paginate(20);

        return view('laz.disbursements.index', compact('applications'));
    }

    public function store(DisbursementRequest $request, Application $application): RedirectResponse
    {
        $data = $request->validated();

        $disbursement = Disbursement::create([
            'application_id' => $application->id,
            'disbursed_by' => $request->user()->id,
            'disbursed_at' => $data['disbursed_at'],
            'method' => $data['method'],
            'total_amount' => $data['total_amount'],
            'notes' => $data['notes'] ?? null,
        ]);

        foreach ($data['items'] ?? [] as $item) {
            if (empty($item['item_description'])) {
                continue;
            }
            DisbursementItem::create([
                'disbursement_id' => $disbursement->id,
                'item_description' => $item['item_description'],
                'quantity' => $item['quantity'] ?? 1,
                'unit_value' => $item['unit_value'] ?? null,
                'total_value' => $item['total_value'] ?? null,
            ]);
        }

        if ($request->hasFile('proofs')) {
            foreach ($request->file('proofs') as $index => $file) {
                if (!$file) {
                    continue;
                }
                $path = $file->store('disbursements/proofs', 'public');
                DisbursementProof::create([
                    'disbursement_id' => $disbursement->id,
                    'file_path' => $path,
                    'caption' => $request->input('proof_captions')[$index] ?? null,
                ]);
            }
        }

        $application->update(['status' => 'completed']);

        return back()->with('success', 'Penyaluran dicatat');
    }
}

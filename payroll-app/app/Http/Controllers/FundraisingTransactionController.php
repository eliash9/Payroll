<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class FundraisingTransactionController extends Controller
{
    public function store(Request $request)
    {
        $user = $request->user();
        
        // Pre-fill data from authenticated user if not provided
        if ($user) {
            $request->merge([
                'company_id' => $request->input('company_id', $user->company_id ?? 1), // Default to 1 if null
                'fundraiser_id' => $request->input('fundraiser_id', $user->employee?->id),
            ]);
        }

        // Map PWA fields to Backend fields if necessary
        if ($request->has('timestamp') && !$request->has('date_received')) {
            $timestamp = $request->input('timestamp');
            try {
                $formatted = \Carbon\Carbon::parse($timestamp)->setTimezone('Asia/Jakarta')->format('Y-m-d H:i:s');
                $request->merge(['date_received' => $formatted]);
            } catch (\Exception $e) {
                // If parsing fails, leave it as is and let validation handle it
                $request->merge(['date_received' => $timestamp]);
            }
        }
        if ($request->has('type') && !$request->has('category')) {
            $type = $request->input('type');
            if ($type === 'sadaqah') {
                $type = 'shodaqoh';
            }
            $request->merge(['category' => $type]);
        }
        if (!$request->has('source')) {
            $request->merge(['source' => 'offline']); // Default for PWA
        }

        $data = $request->validate([
            'company_id' => ['required', 'integer', 'exists:companies,id'],
            'fundraiser_id' => ['required', 'integer', 'exists:employees,id'],
            'donation_code' => ['nullable', 'string', 'max:50', Rule::unique('fundraising_transactions', 'donation_code')],
            'donor_name' => ['nullable', 'string', 'max:191'],
            'donor_phone' => ['nullable', 'string', 'max:50'],
            'donor_email' => ['nullable', 'string', 'max:191'],
            'amount' => ['required', 'numeric', 'min:0'],
            'currency' => ['nullable', 'string', 'max:10'],
            'source' => ['required', Rule::in(['offline', 'online', 'event', 'qr', 'transfer', 'other'])],
            'campaign_name' => ['nullable', 'string', 'max:191'],
            'category' => ['nullable', Rule::in(['zakat', 'infaq', 'shodaqoh', 'wakaf', 'donation', 'other'])],
            'date_received' => ['required', 'date'],
            'notes' => ['nullable', 'string'],
            'status' => ['nullable', Rule::in(['pending', 'verified', 'rejected'])],
        ]);

        $this->assertCompanyAccess($user?->company_id, $data['company_id']);

        $data['currency'] = $data['currency'] ?? 'IDR';
        $data['status'] = $data['status'] ?? 'pending';
        $data['created_at'] = now();
        $data['updated_at'] = now();

        $id = DB::table('fundraising_transactions')->insertGetId($data);

        // Update Daily Summary
        $this->updateDailySummary($data['fundraiser_id'], $data['company_id'], $data['date_received']);

        return response()->json(['id' => $id, 'status' => 'ok'], 201);
    }

    private function updateDailySummary($fundraiserId, $companyId, $date)
    {
        try {
            $date = \Carbon\Carbon::parse($date)->format('Y-m-d');
            
            $total = DB::table('fundraising_transactions')
                ->where('fundraiser_id', $fundraiserId)
                ->whereDate('date_received', $date)
                ->where('status', '!=', 'rejected')
                ->sum('amount');

            $count = DB::table('fundraising_transactions')
                ->where('fundraiser_id', $fundraiserId)
                ->whereDate('date_received', $date)
                ->where('status', '!=', 'rejected')
                ->count();

            \App\Models\FundraisingDailySummary::updateOrCreate(
                [
                    'fundraiser_id' => $fundraiserId,
                    'summary_date' => $date,
                ],
                [
                    'company_id' => $companyId,
                    'total_amount' => $total,
                    'total_transactions' => $count,
                ]
            );
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Failed to update daily summary: ' . $e->getMessage());
            // We don't rethrow to avoid blocking the transaction response, 
            // but for debugging now we want to know. 
            // Actually, if this fails, the user says "tidak temukan data di backend",
            // which implies the transaction ITSELF wasn't saved?
            // But the transaction insert happens BEFORE this call.
            // Unless the transaction is wrapped in a DB transaction? No, I didn't wrap it.
            // If this throws 500, the response is 500.
            throw $e;
        }
    }

    public function index(Request $request)
    {
        $user = $request->user();
        $query = DB::table('fundraising_transactions');

        $companyId = $user?->company_id ?? $request->integer('company_id');
        if ($companyId) {
            $query->where('company_id', $companyId);
        } elseif ($request->filled('company_id')) {
            $query->where('company_id', $request->integer('company_id'));
        }

        if ($request->filled('fundraiser_id')) {
            $query->where('fundraiser_id', $request->integer('fundraiser_id'));
        }
        if ($request->filled('from')) {
            $query->where('date_received', '>=', $request->date('from'));
        }
        if ($request->filled('to')) {
            $query->where('date_received', '<=', $request->date('to'));
        }

        return response()->json($query->orderByDesc('date_received')->paginate(50));
    }

    private function assertCompanyAccess(?int $userCompanyId, int $payloadCompanyId): void
    {
        // If user has no company_id (e.g. PWA user), we skip this check or assume they are valid for now.
        // In a real app, we should ensure PWA users have a company_id.
        if ($userCompanyId && $userCompanyId !== $payloadCompanyId) {
            abort(403, 'Company scope mismatch.');
        }
    }
}

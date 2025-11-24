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
        ]);

        $this->assertCompanyAccess($user?->company_id, $data['company_id']);

        $data['currency'] = $data['currency'] ?? 'IDR';
        $data['created_at'] = now();
        $data['updated_at'] = now();

        $id = DB::table('fundraising_transactions')->insertGetId($data);

        return response()->json(['id' => $id, 'status' => 'ok'], 201);
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
        if ($userCompanyId && $userCompanyId !== $payloadCompanyId) {
            abort(403, 'Company scope mismatch.');
        }
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FundraisingTransactionUiController extends Controller
{
    public function index(Request $request)
    {
        $query = DB::table('fundraising_transactions')
            ->join('employees', 'employees.id', '=', 'fundraising_transactions.fundraiser_id')
            ->select('fundraising_transactions.*', 'employees.full_name as fundraiser_name', 'employees.employee_code');

        if ($request->filled('fundraiser_id')) {
            $query->where('fundraising_transactions.fundraiser_id', $request->integer('fundraiser_id'));
        }

        $transactions = $query->orderByDesc('date_received')->paginate(20);
        $fundraisers = DB::table('employees')->where('is_volunteer', true)->orderBy('full_name')->pluck('full_name', 'id');

        return view('transactions.fundraising.index', compact('transactions', 'fundraisers'));
    }
    public function create()
    {
        $fundraisers = DB::table('employees')->where('is_volunteer', true)->orderBy('full_name')->get(['id', 'full_name', 'employee_code']);
        return view('transactions.fundraising.create', compact('fundraisers'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'fundraiser_id' => 'required|exists:employees,id',
            'amount' => 'required|numeric|min:0',
            'date_received' => 'required|date',
            'source' => 'required|in:offline,online,event,qr,transfer,other',
            'category' => 'nullable|in:zakat,infaq,shodaqoh,wakaf,donation,other',
            'campaign_name' => 'nullable|string|max:191',
            'donor_name' => 'nullable|string|max:191',
            'donor_phone' => 'nullable|string|max:50',
            'notes' => 'nullable|string',
            'status' => 'required|in:pending,verified,rejected',
        ]);

        $data['company_id'] = $request->user()->company_id;
        $data['currency'] = 'IDR';
        $data['created_at'] = now();
        $data['updated_at'] = now();

        DB::table('fundraising_transactions')->insert($data);

        return redirect()->route('fundraising.transactions.index')->with('success', 'Transaksi berhasil ditambahkan');
    }
}

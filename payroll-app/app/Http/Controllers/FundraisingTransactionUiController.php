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
}

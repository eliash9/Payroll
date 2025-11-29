<?php

namespace App\Http\Controllers;

use App\Models\FundraisingTransaction;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FundraisingTransactionUiController extends Controller
{
    public function index(Request $request)
    {
        // FundraisingTransaction model doesn't exist yet or needs to be created/checked.
        // Assuming I need to create it or use DB table but with company scope.
        // Better to use Eloquent if possible, but let's check if model exists.
        // Based on file list, FundraisingTransaction.php exists in Models.
        
        $query = FundraisingTransaction::with(['fundraiser.branch']);

        if ($request->filled('fundraiser_id')) {
            $query->where('fundraiser_id', $request->integer('fundraiser_id'));
        }

        // CompanyScope should be applied automatically if I add the trait to the model.
        // If not, I must manually filter.
        // Let's assume I will add the trait to the model in a separate step.
        // But for safety, I will add manual check here if trait is missing, 
        // OR better, I will ensure the model has the trait.
        
        // For now, let's use manual where if Auth user has company_id
        if (Auth::user()->company_id) {
            $query->where('company_id', Auth::user()->company_id);
        }

        $transactions = $query->orderByDesc('date_received')->paginate(20);
        
        $fundraisersQuery = Employee::where('is_volunteer', true);
        if (Auth::user()->company_id) {
            $fundraisersQuery->where('company_id', Auth::user()->company_id);
        }
        $fundraisers = $fundraisersQuery->orderBy('full_name')->pluck('full_name', 'id');

        return view('transactions.fundraising.index', compact('transactions', 'fundraisers'));
    }

    public function create()
    {
        $fundraisersQuery = Employee::where('is_volunteer', true);
        if (Auth::user()->company_id) {
            $fundraisersQuery->where('company_id', Auth::user()->company_id);
        }
        $fundraisers = $fundraisersQuery->orderBy('full_name')->get(['id', 'full_name', 'employee_code']);
        
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

        // Verify fundraiser belongs to company
        $fundraiser = Employee::find($data['fundraiser_id']);
        if (Auth::user()->company_id && $fundraiser->company_id != Auth::user()->company_id) {
            abort(403, 'Invalid fundraiser selected.');
        }

        $data['company_id'] = Auth::user()->company_id; // Ensure it's set to user's company
        if (!$data['company_id']) {
             // If super admin without company, maybe require company_id in form? 
             // For now assume super admin acts on behalf of a company or we default to fundraiser's company
             $data['company_id'] = $fundraiser->company_id;
        }
        
        $data['currency'] = 'IDR';
        
        FundraisingTransaction::create($data);

        return redirect()->route('fundraising.transactions.index')->with('success', 'Transaksi berhasil ditambahkan');
    }
}

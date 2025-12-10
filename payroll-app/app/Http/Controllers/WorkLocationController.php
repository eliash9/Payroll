<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\WorkLocation;
use Illuminate\Support\Facades\Auth;

class WorkLocationController extends Controller
{
    public function index()
    {
        $query = WorkLocation::query();
        if (Auth::user()->company_id) {
            $query->where('company_id', Auth::user()->company_id);
        }
        
        if ($search = request('search')) {
            $query->where('name', 'like', "%{$search}%");
        }

        $locations = $query->orderBy('name')->paginate(20)->withQueryString();
        
        return view('masters.work_locations.index', compact('locations'));
    }

    public function create()
    {
        return view('masters.work_locations.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:191',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'radius' => 'required|integer|min:10',
        ]);

        $data['company_id'] = Auth::user()->company_id ?? 1; // Default or manage superadmin

        WorkLocation::create($data);

        return redirect()->route('work-locations.index')->with('success', 'Lokasi kerja ditambahkan.');
    }

    public function edit(WorkLocation $workLocation)
    {
         if (Auth::user()->company_id && $workLocation->company_id != Auth::user()->company_id) {
            abort(403);
        }
        return view('masters.work_locations.edit', compact('workLocation'));
    }

    public function update(Request $request, WorkLocation $workLocation)
    {
        if (Auth::user()->company_id && $workLocation->company_id != Auth::user()->company_id) {
            abort(403);
        }

        $data = $request->validate([
            'name' => 'required|string|max:191',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'radius' => 'required|integer|min:10',
        ]);

        $workLocation->update($data);

        return redirect()->route('work-locations.index')->with('success', 'Lokasi kerja diperbarui.');
    }

    public function destroy(WorkLocation $workLocation)
    {
        if (Auth::user()->company_id && $workLocation->company_id != Auth::user()->company_id) {
            abort(403);
        }
        $workLocation->delete();

        return redirect()->route('work-locations.index')->with('success', 'Lokasi kerja dihapus.');
    }
}

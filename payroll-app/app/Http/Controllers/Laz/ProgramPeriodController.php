<?php

namespace App\Http\Controllers\Laz;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProgramPeriodRequest;
use App\Models\Program;
use App\Models\ProgramPeriod;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ProgramPeriodController extends Controller
{
    public function index(): View
    {
        $periods = ProgramPeriod::with('program')->orderByDesc('open_at')->paginate(15);

        return view('laz.periods.index', compact('periods'));
    }

    public function create(): View
    {
        return view('laz.periods.form', [
            'period' => new ProgramPeriod(),
            'programs' => Program::orderBy('name')->get(),
        ]);
    }

    public function store(ProgramPeriodRequest $request): RedirectResponse
    {
        ProgramPeriod::create($request->validated());

        return redirect()->route('laz.periods.index')->with('success', 'Periode program dibuat');
    }

    public function edit(ProgramPeriod $period): View
    {
        return view('laz.periods.form', [
            'period' => $period,
            'programs' => Program::orderBy('name')->get(),
        ]);
    }

    public function update(ProgramPeriodRequest $request, ProgramPeriod $period): RedirectResponse
    {
        $period->update($request->validated());

        return redirect()->route('laz.periods.index')->with('success', 'Periode diperbarui');
    }

    public function destroy(ProgramPeriod $period): RedirectResponse
    {
        $period->delete();

        return back()->with('success', 'Periode dihapus');
    }
}

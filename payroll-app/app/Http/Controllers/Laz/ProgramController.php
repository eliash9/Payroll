<?php

namespace App\Http\Controllers\Laz;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProgramRequest;
use App\Models\Program;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ProgramController extends Controller
{
    public function index(): View
    {
        $programs = Program::orderBy('name')->paginate(15);

        return view('laz.programs.index', compact('programs'));
    }

    public function create(): View
    {
        return view('laz.programs.form', [
            'program' => new Program(),
        ]);
    }

    public function store(ProgramRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active');

        Program::create($data);

        return redirect()->route('laz.programs.index')->with('success', 'Program berhasil dibuat');
    }

    public function edit(Program $program): View
    {
        return view('laz.programs.form', compact('program'));
    }

    public function update(ProgramRequest $request, Program $program): RedirectResponse
    {
        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active');

        $program->update($data);

        return redirect()->route('laz.programs.index')->with('success', 'Program diperbarui');
    }

    public function destroy(Program $program): RedirectResponse
    {
        $program->delete();

        return back()->with('success', 'Program dihapus');
    }
}

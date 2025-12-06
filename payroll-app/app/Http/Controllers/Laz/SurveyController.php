<?php

namespace App\Http\Controllers\Laz;

use App\Http\Controllers\Controller;
use App\Http\Requests\SurveyRequest;
use App\Models\Application;
use App\Models\Survey;
use App\Models\SurveyPhoto;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SurveyController extends Controller
{
    public function index(Request $request): View
    {
        $query = Survey::with(['application.program', 'application.applicant', 'application.organization', 'photos'])
            ->orderByDesc('created_at');

        if ($request->user()->hasRole('surveyor')) {
            $query->where('surveyor_id', $request->user()->id);
        }

        return view('laz.surveys.index', [
            'surveys' => $query->paginate(20),
        ]);
    }

    public function store(SurveyRequest $request, Application $application): RedirectResponse
    {
        if ($request->user()->hasRole('surveyor') && $application->surveys()->where('surveyor_id', $request->user()->id)->doesntExist()) {
            abort(403);
        }

        $survey = $application->surveys()->firstOrCreate([
            'application_id' => $application->id,
            'surveyor_id' => $request->input('surveyor_id', $request->user()->id),
        ]);

        $survey->update($request->validated());

        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $index => $file) {
                if (!$file) {
                    continue;
                }
                $path = $file->store('surveys/photos', 'public');
                SurveyPhoto::create([
                    'survey_id' => $survey->id,
                    'file_path' => $path,
                    'caption' => $request->input('photo_captions')[$index] ?? null,
                ]);
            }
        }

        $application->update(['status' => 'waiting_approval']);

        return back()->with('success', 'Hasil survey tersimpan');
    }
}

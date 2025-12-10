<?php

namespace App\Http\Controllers\Laz;

use App\Http\Controllers\Controller;
use App\Http\Requests\SurveyRequest;
use App\Models\Application;
use App\Models\Survey;
use App\Models\SurveyPhoto;
use App\Models\SurveyResponse;
use App\Models\SurveyTemplate;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;

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

    public function create(Application $application): View
    {
        $template = SurveyTemplate::where('program_id', $application->program_id)
            ->where('is_active', true)
            ->with('questions')
            ->first();

        return view('laz.surveys.create', compact('application', 'template'));
    }

    public function store(Request $request, Application $application): RedirectResponse
    {
        $isHigherUp = $request->user()->hasRole(['super_admin', 'admin_pusat', 'admin_cabang']);
        
        // Only restrict if user is a surveyor AND NOT a higher-up admin
        if (!$isHigherUp && $request->user()->hasRole('surveyor') && $application->surveys()->where('surveyor_id', $request->user()->id)->doesntExist()) {
            abort(403, 'Unauthorized access to this survey. You are not assigned to this application.');
        }

        // Basic validation
        try {
            $validated = $request->validate([
                'survey_date' => 'required|date',
                'method' => 'required|string',
                'summary' => 'required|string',
                'recommendation' => 'required|string',
                'economic_condition_score' => 'nullable|integer|min:1|max:5',
                'notes' => 'nullable|string',
                'photos.*' => 'nullable|image|max:2048',
                'photo_captions.*' => 'nullable|string',
                'survey_template_id' => 'nullable|exists:survey_templates,id',
                'responses' => 'nullable|array',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Log errors for debugging
            \Illuminate\Support\Facades\Log::error('Survey validation failed', $e->errors());
            throw $e;
        }

        DB::beginTransaction();
        try {
            $survey = $application->surveys()->firstOrCreate(
                [
                    'application_id' => $application->id,
                    'surveyor_id' => $request->input('surveyor_id', $request->user()->id),
                ],
                [
                    // Only used if creating new, but we might be updating an existing assigned shell
                ]
            );

            $survey->update([
                'survey_template_id' => $request->survey_template_id,
                'survey_date' => $validated['survey_date'],
                'method' => $validated['method'],
                'summary' => $validated['summary'],
                'economic_condition_score' => $validated['economic_condition_score'] ?? null,
                'recommendation' => $validated['recommendation'],
                'notes' => $validated['notes'] ?? null,
            ]);

            // Calculate scores and save responses
            $totalSurveyScore = 0;
            
            // Pre-fetch questions with options to calculate scores
            $questions = collect();
            if ($request->survey_template_id) {
                $template = SurveyTemplate::with('questions')->find($request->survey_template_id);
                if ($template) {
                    $questions = $template->questions->keyBy('id');
                }
            }

            if ($request->filled('responses')) {
                foreach ($request->responses as $questionId => $answer) {
                    $score = 0;
                    
                    // Logic to calculate score based on answer and question definition
                    if ($questions->has($questionId)) {
                        $question = $questions->get($questionId);
                        
                        // Handle Checkbox (Array) vs Single Value
                        $selectedValues = is_array($answer) ? $answer : [$answer];
                        
                        if (!empty($question->options) && is_array($question->options)) {
                            foreach ($selectedValues as $val) {
                                // Find option with matching text
                                foreach ($question->options as $opt) {
                                    // $opt can be string or array ['text' => '...', 'score' => ...]
                                    if (is_array($opt)) {
                                        if ($opt['text'] == $val) {
                                            $score += intval($opt['score'] ?? 0);
                                        }
                                    } else {
                                        // Old format or simple string, no score
                                    }
                                }
                            }
                        }
                    }

                    if (is_array($answer)) {
                        $answer = json_encode($answer);
                    }
                    
                    SurveyResponse::updateOrCreate(
                        [
                            'survey_id' => $survey->id,
                            'survey_question_id' => $questionId,
                        ],
                        [
                            'answer' => $answer,
                            'score' => $score,
                        ]
                    );
                    
                    $totalSurveyScore += $score;
                }
            }
            
            // Update total score
            $survey->update(['total_score' => $totalSurveyScore]);

            // Save Global Photos
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
            
            DB::commit();
            return redirect()->route('laz.applications.show', $application)->with('success', 'Hasil survey tersimpan');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Gagal menyimpan survey: ' . $e->getMessage()])->withInput();
        }
    }
    public function show(Survey $survey): View
    {
        $survey->load(['application.program', 'application.applicant', 'photos', 'responses.question']);
        
        return view('laz.surveys.show', compact('survey'));
    }
}

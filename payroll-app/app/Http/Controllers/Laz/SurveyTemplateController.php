<?php

namespace App\Http\Controllers\Laz;

use App\Http\Controllers\Controller;
use App\Models\Program;
use App\Models\SurveyTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SurveyTemplateController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = SurveyTemplate::with('program');

        if ($request->search) {
            $query->where('title', 'like', "%{$request->search}%")
                ->orWhere('code', 'like', "%{$request->search}%");
        }

        if ($request->program_id) {
            $query->where('program_id', $request->program_id);
        }

        $templates = $query->latest()->paginate(10);
        $programs = Program::all();

        return view('laz.survey-templates.index', compact('templates', 'programs'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $programs = Program::all();
        return view('laz.survey-templates.create', compact('programs'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'program_id' => 'required|exists:programs,id',
            'title' => 'required|string|max:255',
            'code' => 'required|string|max:255|unique:survey_templates,code',
            'description' => 'nullable|string',
            'questions' => 'required|array|min:1',
            'questions.*.question' => 'required|string',
            'questions.*.type' => 'required|string|in:text,textarea,number,select,radio,checkbox,date,photo',
            'questions.*.options' => 'nullable|array',
            'questions.*.options.*.text' => 'required_with:questions.*.options|string',
            'questions.*.options.*.score' => 'required_with:questions.*.options|numeric',
            'questions.*.is_required' => 'boolean',
            'questions.*.weight' => 'nullable|numeric|min:0',
        ]);

        $template = SurveyTemplate::create([
            'program_id' => $validated['program_id'],
            'title' => $validated['title'],
            'code' => $validated['code'],
            'description' => $validated['description'],
            'is_active' => true,
        ]);

        foreach ($validated['questions'] as $index => $q) {
            $options = null;
            if (in_array($q['type'], ['select', 'radio', 'checkbox']) && !empty($q['options'])) {
                // Ensure array keys are reset (though usually are from form submission if iterating)
                // We store as array of objects: [['text' => 'A', 'score' => 10], ...]
                $options = array_values($q['options']);
            }

            $template->questions()->create([
                'question' => $q['question'],
                'type' => $q['type'],
                'options' => $options,
                'weight' => $q['weight'] ?? 0,
                'is_required' => isset($q['is_required']) ? $q['is_required'] : false,
                'order' => $index + 1,
            ]);
        }

        return redirect()->route('laz.survey-templates.index')->with('success', 'Template survey berhasil dibuat.');
    }

    /**
     * Display the specified resource.
     */
    public function show(SurveyTemplate $surveyTemplate)
    {
        $surveyTemplate->load('questions');
        return view('laz.survey-templates.show', compact('surveyTemplate'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SurveyTemplate $surveyTemplate)
    {
        $programs = Program::all();
        $surveyTemplate->load('questions');
        return view('laz.survey-templates.edit', compact('surveyTemplate', 'programs'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SurveyTemplate $surveyTemplate)
    {
        $validated = $request->validate([
            'program_id' => 'required|exists:programs,id',
            'title' => 'required|string|max:255',
            'code' => 'required|string|max:255|unique:survey_templates,code,' . $surveyTemplate->id,
            'description' => 'nullable|string',
            'questions' => 'nullable|array',
            'questions.*.id' => 'nullable|exists:survey_questions,id',
            'questions.*.question' => 'required|string',
            'questions.*.type' => 'required|string|in:text,textarea,number,select,radio,checkbox,date,photo',
            'questions.*.options' => 'nullable|array',
            'questions.*.options.*.text' => 'required_with:questions.*.options|string',
            'questions.*.options.*.score' => 'required_with:questions.*.options|numeric',
            'questions.*.is_required' => 'boolean',
            'questions.*.delete_flag' => 'nullable|boolean',
            'questions.*.weight' => 'nullable|numeric|min:0',
        ]);

        $surveyTemplate->update([
            'program_id' => $validated['program_id'],
            'title' => $validated['title'],
            'code' => $validated['code'],
            'description' => $validated['description'],
        ]);

        if (isset($validated['questions'])) {
            foreach ($validated['questions'] as $index => $q) {
                // Handle delete
                if (!empty($q['id']) && !empty($q['delete_flag'])) {
                    $surveyTemplate->questions()->where('id', $q['id'])->delete();
                    continue;
                }
                
                if (!empty($q['delete_flag'])) continue;

                $options = null;
                if (in_array($q['type'], ['select', 'radio', 'checkbox']) && !empty($q['options'])) {
                    $options = array_values($q['options']);
                }

                $data = [
                    'question' => $q['question'],
                    'type' => $q['type'],
                    'options' => $options,
                    'is_required' => isset($q['is_required']) ? $q['is_required'] : false,
                    'weight' => $q['weight'] ?? 0,
                    'order' => $index + 1,
                ];

                if (!empty($q['id'])) {
                    $surveyTemplate->questions()->where('id', $q['id'])->update($data);
                } else {
                    $surveyTemplate->questions()->create($data);
                }
            }
        }

        return redirect()->route('laz.survey-templates.index')->with('success', 'Template survey berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SurveyTemplate $surveyTemplate)
    {
        $surveyTemplate->delete();
        return redirect()->route('laz.survey-templates.index')->with('success', 'Template survey berhasil dihapus.');
    }
}

<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SurveyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'survey_date' => 'required|date',
            'method' => 'required|string',
            'summary' => 'required|string',
            'economic_condition_score' => 'required|integer|min:1|max:5',
            'recommendation' => 'required|string',
            'notes' => 'nullable|string',
            'surveyor_id' => 'nullable|exists:users,id',
            'photos.*' => 'nullable|file|mimes:jpg,jpeg,png|max:4096',
            'photo_captions.*' => 'nullable|string',
        ];
    }
}

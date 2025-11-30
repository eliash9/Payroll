<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProgramPeriodRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'program_id' => 'required|exists:programs,id',
            'name' => 'required|string',
            'open_at' => 'required|date',
            'close_at' => 'required|date|after_or_equal:open_at',
            'application_quota' => 'nullable|integer|min:1',
            'budget_quota' => 'nullable|numeric|min:0',
            'status' => 'required|in:draft,open,closed,archived',
        ];
    }
}

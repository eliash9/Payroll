<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProgramRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string',
            'category' => 'required|string',
            'description' => 'nullable|string',
            'allowed_recipient_type' => 'required|in:individual,organization,both',
            'coverage_scope' => 'nullable|string',
            'is_active' => 'boolean',
        ];
    }
}

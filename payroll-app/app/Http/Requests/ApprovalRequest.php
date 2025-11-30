<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ApprovalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'decision' => 'required|in:approved,rejected,revision',
            'approved_amount' => 'nullable|numeric|min:0',
            'approved_aid_type' => 'nullable|string',
            'notes' => 'nullable|string',
        ];
    }
}

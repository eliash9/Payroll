<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DisbursementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'disbursed_at' => 'required|date',
            'method' => 'required|string',
            'total_amount' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
            'items.*.item_description' => 'nullable|string',
            'items.*.quantity' => 'nullable|integer|min:1',
            'items.*.unit_value' => 'nullable|numeric|min:0',
            'items.*.total_value' => 'nullable|numeric|min:0',
            'proofs.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:4096',
            'proof_captions.*' => 'nullable|string',
        ];
    }
}

<?php

namespace App\Imports;

use App\Models\Position;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class PositionImport implements ToModel, WithHeadingRow, WithValidation
{
    public function model(array $row)
    {
        $companyId = Auth::user()->company_id;

        return new Position([
            'company_id' => $companyId,
            'code' => $row['code'] ?? null,
            'name' => $row['name'],
            'description' => $row['description'] ?? null,
        ]);
    }

    public function rules(): array
    {
        return [
            'name' => ['required'],
            'code' => ['nullable', 'unique:positions,code'],
        ];
    }
}

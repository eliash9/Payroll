<?php

namespace App\Imports;

use App\Models\Branch;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class BranchImport implements ToModel, WithHeadingRow, WithValidation
{
    public function model(array $row)
    {
        $companyId = Auth::user()->company_id;

        return new Branch([
            'company_id' => $companyId,
            'code' => $row['code'] ?? null,
            'name' => $row['name'],
            'address' => $row['address'] ?? null,
            'phone' => $row['phone'] ?? null,
            'latitude' => $row['latitude'] ?? null,
            'longitude' => $row['longitude'] ?? null,
            'grade' => $row['grade'] ?? null,
        ]);
    }

    public function rules(): array
    {
        return [
            'name' => ['required'],
            'code' => ['nullable', 'unique:branches,code'],
            'latitude' => ['nullable', 'numeric'],
            'longitude' => ['nullable', 'numeric'],
        ];
    }
}

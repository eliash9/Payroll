<?php

namespace App\Http\Resources\Laz;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ApplicationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'code' => $this->code,
            'applicant_name' => $this->applicant_name,
            'program_name' => $this->program->name ?? '-',
            'status' => $this->status,
            'status_label' => ucfirst($this->status), // You might want a better mapping here
            'created_at' => $this->created_at->format('Y-m-d H:i'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i'),
        ];
    }
}

<?php

namespace App\Http\Resources\Laz;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProgramResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'category' => $this->category,

            'description' => $this->description,
            'specific_requirements' => $this->specific_requirements,
            'required_documents' => $this->required_documents,
            'allowed_recipient_type' => $this->allowed_recipient_type,
            'coverage_scope' => $this->coverage_scope,
            'active_periods' => ProgramPeriodResource::collection($this->whenLoaded('activePeriods')),
        ];
    }
}

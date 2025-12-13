<?php

namespace App\Http\Resources\Laz;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProgramPeriodResource extends JsonResource
{
    public function toArray(Request $request): array
    {

        return [
            'id' => $this->id,
            'name' => $this->name,
            'open_at' => $this->open_at,
            'close_at' => $this->close_at,
            'start_date' => $this->open_at, // Frontend alias
            'end_date' => $this->close_at,   // Frontend alias
            'quota' => $this->application_quota,
            'status' => $this->status,
            'is_open' => $this->isOpen(),
        ];

    }
}

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
            'status' => $this->status,
            'is_open' => $this->isOpen(),
        ];
    }
}

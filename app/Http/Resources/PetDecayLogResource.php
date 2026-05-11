<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PetDecayLogResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'pet_id' => $this->pet_id,
            'hours_elapsed' => $this->hours_elapsed,
            'changes' => $this->changes,
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PetResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'species' => $this->species,
            'health' => $this->health,
            'energy' => $this->energy,
            'hunger' => $this->hunger,
            'cleanliness' => $this->cleanliness,
            'mood' => $this->mood,
            'is_alive' => $this->is_alive,
            'last_visited_at' => $this->last_visited_at?->toIso8601String(),
            'last_decay_at' => $this->last_decay_at?->toIso8601String(),
            'created_at' => $this->created_at,
        ];
    }
}

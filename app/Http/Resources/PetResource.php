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
            'created_at' => $this->created_at,
        ];
    }
}

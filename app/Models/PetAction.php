<?php

namespace App\Models;

use App\Enums\PetActionType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PetAction extends Model
{
    protected $fillable = [
        'pet_id',
        'type',
        'payload',
        'effects_applied',
    ];

    protected function casts(): array
    {
        return [
            'type' => PetActionType::class,
            'payload' => 'array',
            'effects_applied' => 'array',
        ];
    }

    public function pet(): BelongsTo
    {
        return $this->belongsTo(Pet::class);
    }
}

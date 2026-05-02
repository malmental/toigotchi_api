<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pet extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'species',
        'health',
        'energy',
        'hunger',
        'cleanliness',
        'mood',
        'is_alive',
    ];

    protected function casts(): array
    {
        return [
            'is_alive' => 'boolean',
            'health' => 'integer',
            'energy' => 'integer',
            'hunger' => 'integer',
            'cleanliness' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function actions(): HasMany
    {
        return $this->hasMany(PetAction::class);
    }

    public function memories(): HasMany
    {
        return $this->hasMany(PetMemory::class);
    }
}

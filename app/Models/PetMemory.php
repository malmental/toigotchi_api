<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PetMemory extends Model
{
    protected $fillable = [
        'pet_id',
        'type',
        'content',
        'ai_response',
        'importance',
    ];

    protected function casts(): array
    {
        return [
            'importance' => 'integer',
        ];
    }

    public function pet(): BelongsTo
    {
        return $this->belongsTo(Pet::class);
    }

    public function scopeRecent($query, int $limit = 10)
    {
        return $query->orderByDesc('created_at')->limit($limit);
    }

    public function scopeImportant($query, int $minImportance = 7)
    {
        return $query->where('importance', '>=', $minImportance);
    }
}

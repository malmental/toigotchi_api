<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PetQuota extends Model
{
    use HasFactory;
    protected $fillable = [
        'pet_id',
        'used_count',
        'window_start',
    ];

    protected function casts(): array
    {
        return [
            'used_count' => 'integer',
            'window_start' => 'datetime',
        ];
    }

    public function pet(): BelongsTo
    {
        return $this->belongsTo(Pet::class);
    }

    public function isWindowExpired(): bool
    {
        if (! $this->window_start) {
            return true;
        }

        $windowMinutes = (int) env('PET_ACTION_QUOTA_WINDOW_MINUTES', 60);

        return $this->window_start->addMinutes($windowMinutes)->isPast();
    }

    public function canPerformAction(): bool
    {
        if ($this->isWindowExpired()) {
            return true;
        }

        $limit = (int) env('PET_ACTION_QUOTA_LIMIT', 3);

        return $this->used_count < $limit;
    }

    public function consume(): bool
    {
        if (! $this->canPerformAction()) {
            return false;
        }

        if ($this->isWindowExpired()) {
            $this->reset();
        }

        $this->increment('used_count');

        return true;
    }

    public function reset(): void
    {
        $this->update([
            'used_count' => 0,
            'window_start' => now(),
        ]);
    }

    public function getRemainingAttribute(): int
    {
        $limit = (int) env('PET_ACTION_QUOTA_LIMIT', 3);

        return max(0, $limit - $this->used_count);
    }

    public function getResetsAtAttribute(): ?\DateTime
    {
        if (! $this->window_start) {
            return null;
        }

        $windowMinutes = (int) env('PET_ACTION_QUOTA_WINDOW_MINUTES', 60);

        return $this->window_start->addMinutes($windowMinutes);
    }
}
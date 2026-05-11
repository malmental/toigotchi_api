<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PetDecayLog extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'pet_id',
        'hours_elapsed',
        'changes',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'hours_elapsed' => 'integer',
            'changes' => 'array',
            'created_at' => 'datetime',
        ];
    }

    public function pet(): BelongsTo
    {
        return $this->belongsTo(Pet::class);
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductionQuota extends Model
{
    protected $fillable = [
        'date',
        'max_quota',
        'used_quota',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'max_quota' => 'integer',
            'used_quota' => 'integer',
        ];
    }

    public function getRemainingQuotaAttribute(): int
    {
        return max(0, $this->max_quota - $this->used_quota);
    }
}
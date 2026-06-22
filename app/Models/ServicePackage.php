<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ServicePackage extends Model
{
    public const SERVICE_TYPES = [
        'Edit Foto',
        'Video TikTok / Reels',
        'Copy Writing',
        'Strategi Konten',
    ];

    protected $table = 'packages';

    protected $fillable = [
        'name',
        'service_type',
        'description',
        'includes',
        'price',
        'duration',
        'revision_limit',
        'total_slot',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'includes' => 'array',
            'price' => 'integer',
            'revision_limit' => 'integer',
            'total_slot' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function orders(): HasMany
    {
        return $this->hasMany(
            Order::class,
            'package_id'
        );
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where(
            'is_active',
            true
        );
    }

    public function getServiceTypeLabelAttribute(): string
    {
        return $this->service_type ?: $this->name;
    }
}
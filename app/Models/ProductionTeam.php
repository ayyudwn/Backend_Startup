<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductionTeam extends Model
{
    public const MAX_SKILLS = 2;

    public const MAX_ACTIVE_ORDERS = 5;

    public const ALLOWED_SKILLS = [
        'Edit Foto',
        'Video TikTok / Reels',
        'Copy Writing',
        'Strategi Konten',
    ];

    public const ACTIVE_ORDER_STATUSES = [
        'queue',
        'process',
        'review',
    ];

    protected $fillable = [
        'name',
        'role',
        'skills',
        'status',
    ];

    public function orders(): HasMany
    {
        return $this->hasMany(
            Order::class,
            'production_team_id'
        );
    }

    public function activeOrders(): HasMany
    {
        return $this->orders()->whereIn(
            'status',
            self::ACTIVE_ORDER_STATUSES
        );
    }

    public function completedOrders(): HasMany
    {
        return $this->orders()->where(
            'status',
            'done'
        );
    }

    protected function skills(): Attribute
    {
        return Attribute::make(
            get: function ($value): array {
                if (blank($value)) {
                    return [];
                }

                $decoded = json_decode(
                    (string) $value,
                    true
                );

                $skills = is_array($decoded)
                    ? $decoded
                    : preg_split(
                        '/[,;]+/',
                        (string) $value
                    );

                return collect($skills)
                    ->map(
                        fn ($skill) => trim(
                            (string) $skill
                        )
                    )
                    ->filter()
                    ->unique(
                        fn ($skill) => strtolower(
                            $skill
                        )
                    )
                    ->take(self::MAX_SKILLS)
                    ->values()
                    ->all();
            },

            set: function ($value): string {
                $skills = is_array($value)
                    ? $value
                    : preg_split(
                        '/[,;]+/',
                        (string) $value
                    );

                $skills = collect($skills)
                    ->map(
                        fn ($skill) => trim(
                            (string) $skill
                        )
                    )
                    ->filter()
                    ->unique(
                        fn ($skill) => strtolower(
                            $skill
                        )
                    )
                    ->take(self::MAX_SKILLS)
                    ->values()
                    ->all();

                return json_encode(
                    $skills,
                    JSON_UNESCAPED_UNICODE
                );
            }
        );
    }

    public function getActiveOrderTotal(): int
    {
        if (
            $this->getAttribute(
                'active_orders_count'
            ) !== null
        ) {
            return (int) $this->getAttribute(
                'active_orders_count'
            );
        }

        return $this->activeOrders()->count();
    }

    public function getCompletedOrderTotal(): int
    {
        if (
            $this->getAttribute(
                'completed_orders_count'
            ) !== null
        ) {
            return (int) $this->getAttribute(
                'completed_orders_count'
            );
        }

        return $this->completedOrders()->count();
    }

    public function getDisplayStatusAttribute(): string
    {
        if ($this->status === 'offline') {
            return 'offline';
        }

        if (
            $this->getActiveOrderTotal()
            >= self::MAX_ACTIVE_ORDERS
        ) {
            return 'busy';
        }

        return 'available';
    }

    public function hasSkill(string $skill): bool
    {
        return collect($this->skills)
            ->contains(
                fn ($teamSkill) =>
                    strtolower($teamSkill)
                    === strtolower(trim($skill))
            );
    }

    public function canReceiveOrderFor(
        string $requiredSkill
    ): bool {
        return $this->status !== 'offline'
            && $this->getActiveOrderTotal()
                < self::MAX_ACTIVE_ORDERS
            && $this->hasSkill(
                $requiredSkill
            );
    }
}
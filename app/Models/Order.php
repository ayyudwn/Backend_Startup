<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
    public const STATUS_PENDING = 'pending';
    public const STATUS_QUEUE = 'queue';
    public const STATUS_PROCESS = 'process';
    public const STATUS_REVIEW = 'review';
    public const STATUS_DONE = 'done';

    public const PRODUCTION_STATUSES = [
        self::STATUS_QUEUE,
        self::STATUS_PROCESS,
        self::STATUS_REVIEW,
    ];

    protected $fillable = [
        'order_code',
        'user_id',
        'production_team_id',
        'package_id',
        'voucher_id',
        'title',
        'notes',
        'reference_file',
        'platform',
        'content_size',
        'booking_date',
        'deadline_type',
        'base_price',
        'additional_price',
        'discount',
        'total_price',
        'status',
        'priority',
    ];

    protected function casts(): array
    {
        return [
            'booking_date' => 'date',
            'base_price' => 'integer',
            'additional_price' => 'integer',
            'discount' => 'integer',
            'total_price' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'user_id'
        );
    }

    public function package(): BelongsTo
    {
        return $this->belongsTo(
            ServicePackage::class,
            'package_id'
        );
    }

    public function voucher(): BelongsTo
    {
        return $this->belongsTo(
            Voucher::class,
            'voucher_id'
        );
    }

    public function payment(): HasOne
    {
        return $this->hasOne(
            Payment::class,
            'order_id'
        );
    }

    public function results(): HasMany
    {
        return $this->hasMany(
            OrderResult::class,
            'order_id'
        );
    }

    public function productionTeam(): BelongsTo
    {
        return $this->belongsTo(
            ProductionTeam::class,
            'production_team_id'
        );
    }

    public function scopeInProduction(
        Builder $query
    ): Builder {
        return $query->whereIn(
            'status',
            self::PRODUCTION_STATUSES
        );
    }

    public function scopeCompleted(
        Builder $query
    ): Builder {
        return $query->where(
            'status',
            self::STATUS_DONE
        );
    }

    public function scopeWithVerifiedPayment(
        Builder $query
    ): Builder {
        return $query->whereHas(
            'payment',
            function (Builder $paymentQuery) {
                $paymentQuery->where(
                    'status',
                    'verified'
                );
            }
        );
    }

    public function isPending(): bool
    {
        return $this->status
            === self::STATUS_PENDING;
    }

    public function isInProduction(): bool
    {
        return in_array(
            $this->status,
            self::PRODUCTION_STATUSES,
            true
        );
    }

    public function isCompleted(): bool
    {
        return $this->status
            === self::STATUS_DONE;
    }

    public function getRequiredSkillAttribute(): ?string
    {
        return $this->package?->service_type_label;
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING => 'Menunggu Verifikasi',
            self::STATUS_QUEUE => 'Antrean',
            self::STATUS_PROCESS => 'Diproses',
            self::STATUS_REVIEW => 'Review',
            self::STATUS_DONE => 'Selesai',
            default => ucfirst(
                (string) $this->status
            ),
        };
    }
}
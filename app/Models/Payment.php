<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Values\PaymentStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $sum
 * @property string $currency
 * @property PaymentStatus $status
 */
class Payment extends Model
{
    protected $fillable = [
        'sum',
        'currency',
        'order',
    ];

    protected $casts = [
        'status' => PaymentStatus::class,
    ];

    public function cancel(): void
    {
        if (PaymentStatus::COMPLETED === $this->status) {
            throw new \Exception("Can't cancel completed payment");
        }

        $this->status = PaymentStatus::CANCELED;
        $this->save();
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function scopePending(Builder $query): void
    {
        $query->whereIn('status', [PaymentStatus::PENDING, PaymentStatus::WAITING]);
    }

    public function scopeCompleted(Builder $query): void
    {
        $query->where('status', '=', PaymentStatus::COMPLETED);
    }

    public function scopeFinished(Builder $query): void
    {
        $query->whereIn('status', [PaymentStatus::COMPLETED, PaymentStatus::CANCELED]);
    }
}

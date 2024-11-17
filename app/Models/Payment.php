<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Values\PaymentStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property string $id
 * @property float $sum
 * @property string $currency
 * @property array $payload
 * @property PaymentStatus $status
 */
class Payment extends Model
{
    use HasUuids;

    protected $fillable = [
        'id',
        'sum',
        'currency',
        'token',
        'order',
    ];

    protected $attributes = [
        'status' => PaymentStatus::NEW,
    ];

    public function cancel(): void
    {
        if ($this->status->isFinal()) {
            throw new \Exception("Can't cancel finalized payment");
        }

        $this->status = PaymentStatus::CANCELED;
        $this->save();
    }

    public function complete(): void
    {
        if ($this->status->isFinal()) {
            throw new \Exception("Can't complete finalized payment");
        }

        $this->status = PaymentStatus::COMPLETED;
        $this->save();
    }

    public function updateByResponse(array $response): void
    {
        $this->payload = $response;
        try {
            $value = $response['status'] === 'waiting_for_capture' ? 'waiting' : $response['status'];
            $this->status = PaymentStatus::from($value);
        } catch(\ValueError) {
            // ignore - don't change status
        }
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

    public function jsonSerialize(): mixed
    {
        return [
            'id' => $this->id,
            'sum' => $this->sum,
            'currency' => $this->currency,
            'status' => $this->status,
            'confirmation_url' => $this->payload['confirmation']['confirmation_url'] ?? null,
        ];
    }

    protected function casts(): array
    {
        return [
            'status' => PaymentStatus::class,
            'payload' => 'array',
        ];
    }
}

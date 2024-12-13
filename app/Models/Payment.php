<?php

declare(strict_types=1);

namespace App\Models;

use App\Events\PaymentSucceededEvent;
use App\Models\Values\PaymentStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Log;

/**
 * @property string $id
 * @property float $sum
 * @property string $currency
 * @property string $email Имейл для получения электронного чека
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
        'email',
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

    public function updateByResponse(array $response): void
    {
        $this->payload = $response;
        try {
            $value = $response['status'] === 'waiting_for_capture' ? 'waiting' : $response['status'];
            $this->status = PaymentStatus::from($value);

            if ($this->isDirty('status') && $this->status->isSucceeded()) {
                PaymentSucceededEvent::dispatch($this);
            }
        } catch (\ValueError) {
            Log::warning("Undefined status: $value");
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

    public function scopeSucceeded(Builder $query): void
    {
        $query->where('status', '=', PaymentStatus::SUCCEEDED);
    }

    public function scopeFinished(Builder $query): void
    {
        $query->whereIn('status', [PaymentStatus::SUCCEEDED, PaymentStatus::CANCELED]);
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

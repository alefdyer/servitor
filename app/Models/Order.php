<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @property string $id
 * @property string $item
 * @property array $content
 * @property float $sum
 * @property string $currency
 * @property ?Payment $payment
 */
class Order extends Model implements \JsonSerializable
{
    use HasUuids;

    protected $fillable = [
        'item',
        'content',
        'sum',
        'currency',
    ];

    public function cancel(): void
    {
        $this->payment?->cancel();
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }

    public function scopePending(Builder $query): void
    {
        $query->whereDoesntHave('payment', fn(Builder $payment) => $payment->finished());
    }

    public function scopePaid(Builder $query): void
    {
        $query->whereHas('payment', fn(Builder $payment) => $payment->completed());
    }

    public function jsonSerialize(): mixed
    {
        return [
            'id' => $this->id,
            'item' => $this->item,
            'content' => $this->content,
            'sum' => $this->sum,
            'currency' => $this->currency,
            'payment' => $this->payment,
        ];
    }

    protected function casts(): array
    {
        return [
            'content' => 'array',
        ];
    }
}

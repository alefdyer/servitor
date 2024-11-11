<?php

declare(strict_types=1);

namespace App\Models\Values;

enum PaymentStatus: string
{
    case PENDING = 'pending'; // Созданный платеж на стадии выбора средства оплаты
    case WAITING = 'waiting'; // Ожидание оплаты
    case COMPLETED = 'completed'; // Оплата проведена
    case CANCELED = 'canceled'; // Оптала отмена
    case REFUNDED = 'refunded'; // Оплата позвращена

    public function isFinal(): bool
    {
        return match ($this) {
            self::COMPLETED => true,
            self::CANCELED => true,
            default => false
        };
    }

    public function isPending(): bool
    {
        return self::PENDING === $this;
    }

    public function isWaiting(): bool
    {
        return self::WAITING === $this;
    }

    public function isComplete(): bool
    {
        return self::COMPLETED === $this;
    }
}

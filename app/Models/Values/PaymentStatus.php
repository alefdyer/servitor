<?php

declare(strict_types=1);

namespace App\Models\Values;

enum PaymentStatus: string
{
    case NEW = 'new';
    case PENDING = 'pending'; // Созданный платеж на стадии выбора средства оплаты
    case WAITING = 'waiting'; // Ожидание оплаты
    case SUCCEEDED = 'succeeded'; // Оплата прошла
    case CANCELED = 'canceled'; // Оптала отмена
    case REFUNDED = 'refunded'; // Оплата позвращена

    public function isFinal(): bool
    {
        return match ($this) {
            self::SUCCEEDED => true,
            self::CANCELED => true,
            default => false
        };
    }

    public function isNew(): bool
    {
        return self::NEW === $this;
    }

    public function isPending(): bool
    {
        return self::PENDING === $this;
    }

    public function isWaiting(): bool
    {
        return self::WAITING === $this;
    }

    public function isSucceeded(): bool
    {
        return self::SUCCEEDED === $this;
    }
}

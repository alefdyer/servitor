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
}

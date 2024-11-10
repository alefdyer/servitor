<?php

declare(strict_types=1);

namespace App\Models\Values;

enum SubscriptionPeriod: string
{
    case DAY = 'day';
    case WEEK = 'week';
    case MONTH = 'month';
    case YEAR = 'year';

    public static function availablePeriods(): array
    {
        return array_map(fn($case) => $case->value, self::cases());
    }
}

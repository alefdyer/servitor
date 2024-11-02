<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Configuration for android-app.
 */
readonly class Config
{
    public function __construct(
        public string $url,
        public string $country,
        public string $location,
        public int $breakForAdsInterval,
    ) {}
}

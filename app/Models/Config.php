<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Configuration for android-app.
 */
readonly class Config implements \JsonSerializable
{
    public function __construct(
        public string $url,
        public string $country,
        public string $location,
        public int $breakForAdsInterval,
    ) {}

    public function jsonSerialize(): mixed
    {
        return [
            'url' => $this->url,
            'country' => $this->country,
            'location' => $this->location,
            'breakForAdsInterval' => $this->breakForAdsInterval,
        ];
    }
}

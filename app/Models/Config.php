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
        public ?Subscription $subscription = null,
    ) {}

    public function jsonSerialize(): mixed
    {
        $json = [
            'url' => $this->url,
            'country' => $this->country,
            'location' => $this->location,
        ];

        if ($this->subscription) {
            $json['subscription'] = $this->subscription;
        } else {
            $json['breakForAdsInterval'] = config('api.breakForAdsInterval');
        }

        return $json;
    }
}

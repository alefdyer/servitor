<?php

declare(strict_types=1);

namespace App\Queries;

use App\Exceptions\ConfigNotFound;
use App\Models\Config;
use App\Models\Server;

class GetConfigQuery
{
    public function __invoke(string $deviceId): Config
    {
        $totalCapacity = Server::active()->sum('capacity');
        $capacity = random_int(0, $totalCapacity);
        $server = null;
        foreach (Server::active()->orderBy('RANDOM()')->get()->all() as $server) {
            $capacity -= $server->capacity;
            if ($capacity <= 0) {
                break;
            }
        }

        if (!$server) {
            throw new ConfigNotFound();
        }

        $config = new Config(
            url: $server->url,
            country: $server->country,
            location: $server->location,
            breakForAdsInterval: config('api.breakForAdsInterval'),
        );

        return $config;
    }
}

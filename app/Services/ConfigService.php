<?php

declare(strict_types=1);

namespace App\Services;

use App\Exceptions\ConfigNotFound;
use App\Models\Config;
use App\Models\Device;
use App\Models\Server;
use Illuminate\Support\Facades\Log;

class ConfigService
{
    public function getConfigForDevice(?Device $device = null): Config
    {
        Log::info('Configuration request', compact('device'));

        $subscription = $device?->client?->subscriptions()->active()->first();

        $server = $this->getRandomServer(null !== $subscription);

        Log::info('Configuration selected', compact('device', 'server'));

        $config = new Config(
            url: $server->url,
            country: $server->country,
            location: $server->location ?? 'Unknown',
            subscription: $subscription,
        );

        return $config;
    }

    private function getRandomServer(bool $premium): Server
    {
        $totalCapacity = (int) Server::active()->premium($premium)->sum('capacity');
        $capacity = random_int(0, $totalCapacity);
        $server = null;
        $servers = Server::active()->premium($premium)->get()->all();
        shuffle($servers);
        foreach ($servers as $server) {
            $capacity -= $server->capacity;
            if ($capacity <= 0) {
                break;
            }
        }

        return $server ?? throw new ConfigNotFound();
    }
}

<?php

declare(strict_types=1);

namespace App\Queries;

use App\Exceptions\ConfigNotFound;
use App\Models\Config;
use App\Models\Device;
use App\Models\Server;
use Illuminate\Support\Facades\Log;

class GetConfigQuery
{
    public function __invoke(?string $deviceId = null): Config
    {
        Log::info('Configuration request', compact('deviceId'));
        $device = Device::query()->find($deviceId);

        $isPremium = $device && $device->client->subscriptions()->active()->exists();

        $server = $this->getRandomServer($isPremium);

        Log::info('Configuration selected', compact('deviceId', 'server'));

        $config = new Config(
            url: $server->url,
            country: $server->country,
            location: $server->location ?? 'Unknown',
            breakForAdsInterval: $isPremium ? 0 : config('api.breakForAdsInterval'),
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

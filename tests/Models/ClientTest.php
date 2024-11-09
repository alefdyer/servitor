<?php

use App\Models\Client;
use App\Models\Device;
use App\Models\Subscription;

test('Client without Devices', function () {
    $client = Client::factory()->create();

    expect($client->devices)->toHaveCount(0);
});

test('Client with Devices', function () {
    $client = Client::factory()->create();
    $device = Device::factory()->make();
    $client->devices()->save($device);

    expect($device->id)->toBeUuid();
    expect($client->devices)->toHaveCount(1);
    expect($client->devices->first()->toArray())->toMatchArray([
        'id' => $device->id,
        'model' => $device->model,
    ]);
});

test('Client without Subscription', function () {
    $client = Client::factory()->create();

    expect($client->subscriptions)->toHaveCount(0);
});

test('Client with active Subscription', function () {
    $client = Client::factory()->create();
    $subscription = Subscription::factory()->active()->make();
    $client->subscriptions()->save($subscription);

    expect(Subscription::query()->active()->count())->toBe(1);
});

test('Client with expired Subscription', function () {
    $client = Client::factory()->create();
    $subscription = Subscription::factory()->expired()->make();
    $client->subscriptions()->save($subscription);

    expect(Subscription::query()->active()->count())->toBe(0);
});

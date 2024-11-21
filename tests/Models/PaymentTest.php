<?php

use App\Events\PaymentSucceededEvent;
use App\Models\Client;
use App\Models\Values\PaymentStatus;
use Illuminate\Support\Facades\Event;

test('Succeeded payment should dispatch event', function () {
    $client = Client::factory()->create();
    $order = $client->orders()->create([
        'item' => 'subscription',
        'content' => ['period' => 'day'],
        'sum' => '15.00',
        'currency' => 'RUB',
    ]);
    $payment = $order->payment()->firstOrNew([
        'sum' => $order->sum,
        'currency' => $order->currency,
    ]);

    Event::fake();

    $payment->updateByResponse(['status' => 'succeeded']);

    Event::assertDispatched(PaymentSucceededEvent::class);

    expect($payment->status)->toBe(PaymentStatus::SUCCEEDED);
});

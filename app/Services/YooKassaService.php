<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Payment;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Адаптер для работы с ЮКассой
 */
class YooKassaService
{
    public function send(Payment $payment): void
    {
        Log::debug('YooKassa send', [$payment]);

        $response = $this->request()
            ->withHeader('Idempotence-Key', $payment->order->id)
            ->post('payments', [
                'amount' => [
                    'value' => $payment->sum,
                    'currency' => $payment->currency,
                ],
                'capture' => false,
                'confirmation' => [
                    'type' => 'redirect',
                    'return_url' => 'https://asinosoft.ru/vpn.html', // @TODO
                ],
                'metadata' => [
                    'client_id' => $payment->order->client->id,
                    'order_id' => $payment->order->id,
                ],
                'description' => "Клиент #{$payment->order->client->id} | Заказ #{$payment->order->id}",
            ])
            ->throw()
            ->json();

        if (!$response) {
            throw new \Exception('Bad yookassa response');
        }

        Log::debug('YooKassa response', $response);

        $payment->id = $response['id'];
        $payment->updateByResponse($response);
    }

    public function check(Payment $payment): void
    {
        Log::debug('YooKassa check', [$payment]);

        $response = $this->request()
            ->get("payments/$payment->id")
            ->throw()
            ->json();

        if (!$response) {
            throw new \Exception('Bad yookassa response');
        }

        Log::debug('YooKassa response', $response);

        $payment->updateByResponse($response);
    }

    private function request(): PendingRequest
    {
        $url = config('yookassa.url');
        $shop = config('yookassa.shop');
        $secret = config('yookassa.secret');

        return Http::withBasicAuth($shop, $secret)->baseUrl($url);
    }
}

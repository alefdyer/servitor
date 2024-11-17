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
            ->withHeader('Idempotence-Key', $payment->id)
            ->post('payments', [
                'payment_token' => $payment->token,
                'amount' => [
                    'value' => $payment->sum,
                    'currency' => $payment->currency,
                ],
                'description' => "Клиент #{$payment->order->client->id} | Заказ #{$payment->order->id}",
            ])
            ->throw()
            ->json();

        Log::debug('YooKassa response', $response);

        $payment->updateByResponse($response);
    }

    public function check(Payment $payment): void
    {
        Log::debug('YooKassa check', [$payment]);

        if ($id = $payment->payload['id'] ?? null) {
            $response = $this->request()
                ->get("payments/$id")
                ->throw()
                ->json();

            Log::debug('YooKassa response', $response);

            $payment->updateByResponse($response);
        } else {
            throw new \Exception("Can't check payment without ID");
        }
    }

    private function request(): PendingRequest
    {
        $url = config('yookassa.url');
        $shop = config('yookassa.shop');
        $secret = config('yookassa.secret');

        return Http::withBasicAuth($shop, $secret)->baseUrl($url);
    }
}

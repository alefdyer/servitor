<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Payment;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\RequestException;
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

        try {
            $response = $this->request()
                ->withHeader('Idempotence-Key', $payment->order->id)
                ->post('payments', $this->getPaymentData($payment))
                ->throw()
                ->json();

            if (!$response) {
                throw new \Exception('Bad yookassa response');
            }

            Log::debug('YooKassa response', $response);

            $payment->id = $response['id'];
            $payment->updateByResponse($response);
        } catch (RequestException $ex) {
            Log::error('YooKassa error', $ex->response->json());
            throw new \Exception('YooKassa error');
        }
    }

    public function check(Payment $payment): void
    {
        Log::debug('YooKassa check', [$payment]);

        try {
            $response = $this->request()
                ->get("payments/$payment->id")
                ->throw()
                ->json();

            if (!$response) {
                throw new \Exception('Bad yookassa response');
            }

            Log::debug('YooKassa response', $response);

            $payment->updateByResponse($response);
        } catch (RequestException $ex) {
            Log::error('YooKassa error', $ex->response->json());
            throw new \Exception('YooKassa error');
        }
    }

    private function request(): PendingRequest
    {
        $url = config('yookassa.url');
        $shop = config('yookassa.shop');
        $secret = config('yookassa.secret');

        return Http::withBasicAuth($shop, $secret)->baseUrl($url);
    }

    private function getPaymentData(Payment $payment): array
    {
        $period = match ($payment->order->content['period']) {
            'day' => '1 день',
            'week' => '1 неделю',
            'month' => '1 месяц',
            'year' => '1 год',
            default => throw new \Exception("Unknown subscription period {$payment->order->content['period']}")
        };

        return [
            'amount' => [
                'value' => $payment->sum,
                'currency' => $payment->currency,
            ],
            'capture' => true,
            'confirmation' => [
                'type' => 'redirect',
                'return_url' => 'https://asinosoft.ru/vpn.html', // @TODO
            ],
            'receipt' => [
                'customer' => [
                    'email' => $payment->email,
                ],
                'items' => [[
                    'description' => "Подписка на $period",
                    'amount' => [
                        'value' => $payment->sum,
                        'currency' => $payment->currency,
                    ],
                    'vat_code' => 1, // Без НДС
                    'quantity' => 1,
                    'payment_subject' => 'service',
                    'payment_mode' => 'full_payment',
                ]],
            ],
            'metadata' => [
                'client_id' => $payment->order->client->id,
                'order_id' => $payment->order->id,
            ],
            'description' => "Клиент #{$payment->order->client->id} | Заказ #{$payment->order->id}",
        ];
    }
}

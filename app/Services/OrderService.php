<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Client;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Values\SubscriptionPeriod;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Log;

class OrderService
{
    public function __construct(
        private YooKassaService $yooKassaService,
    ) {}

    public function createOrder(Client $client, SubscriptionPeriod $period): Order
    {
        Log::info('Create order', compact('client', 'period'));

        $order = $this->findExistingOrder($client, $period) ?? $this->createNewOrder($client, $period);

        Log::info('Order created', [$order]);

        return $order;
    }

    public function pay(Order $order, string $token): Payment
    {
        Log::info('Pay order', compact('order', 'token'));

        /** @var Payment $payment */
        $payment = match ($order->payment?->token) {
            null => $order->payment()->create([
                'token' => $token,
                'sum' => $order->sum,
                'currency' => $order->currency,
            ]),

            $token => $order->payment,

            default => throw new \Exception("Order already paid")
        };

        if ($payment->status->isPending()) {
            $this->yooKassaService->send($payment);
        } elseif ($payment->status->isWaiting()) {
            $this->yooKassaService->check($payment);
        } elseif ($payment->status->isFinal()) {
            return $payment;
        }

        // @TODO: Create subscription by OrderPaidEent!
        if ($payment->status->isComplete()) {
            $period = $order->content['period'];
            $order->client->subscriptions()->create([
                'period' => $period,
                'start_at' => now(),
                'end_at' => match ($period) {
                    'day' => now()->addDay(),
                    'week' => Date::parse('tomorrow')->addWeek(),
                    'month' => Date::parse('tomorrow')->addMonth(),
                    'year' => Date::parse('tomorrow')->addYead(),
                    default => throw new \Exception("Unknown subscription period: $period")
                },
            ]);
        }

        return $payment;
    }

    private function findExistingOrder(Client $client, SubscriptionPeriod $period): ?Order
    {
        foreach ($client->orders()->pending()->get() as $order) {
            if ($order->item === 'subscription' && $order->content === ['period' => $period->value]) {
                return $order;
            } else {
                $order->cancel();
            }
        }

        return null;
    }

    private function createNewOrder(Client $client, SubscriptionPeriod $period): Order
    {
        return $client->orders()->create([
            'item' => 'subscription',
            'content' => [
                'period' => $period,
            ],
            'sum' => $this->getPrice($period),
            'currency' => 'RUB',
        ]);
    }

    private function getPrice(SubscriptionPeriod $period): float
    {
        return match ($period) {
            SubscriptionPeriod::DAY => 15.00,
            SubscriptionPeriod::WEEK => 99.00,
            SubscriptionPeriod::MONTH => 299.00,
            SubscriptionPeriod::YEAR => 2400.00,
            default => throw new \Exception("Unknown subscription period: $period"),
        };
    }
}

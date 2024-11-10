<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Client;
use App\Models\Order;
use App\Models\Values\SubscriptionPeriod;

class OrderService
{
    public function createOrder(Client $client, SubscriptionPeriod $period): Order
    {
        return $this->findExistingOrder($client, $period) ?? $this->createNewOrder($client, $period);
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
        $order = $client->orders()->create([
            'item' => 'subscription',
            'content' => [
                'period' => $period,
            ],
            'sum' => $this->getPrice($period),
            'currency' => 'RUB',
        ]);

        $order->payment()->create([
            'sum' => $this->getPrice($period),
            'currency' => 'RUB',
        ]);

        return $order;
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

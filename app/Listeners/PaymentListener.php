<?php

namespace App\Listeners;

use App\Events\PaymentSucceededEvent;
use App\Services\OrderService;

class PaymentListener
{
    public function __construct(
        private OrderService $orderService,
    ) {}

    public function handle(PaymentSucceededEvent $event): void
    {
        $this->orderService->complete($event->payment->order);
    }
}

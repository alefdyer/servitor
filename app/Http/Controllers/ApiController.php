<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\CreateOrderRequest;
use App\Models\Client;
use App\Models\Device;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Values\SubscriptionPeriod;
use App\Services\ConfigService;
use App\Services\OrderService;
use App\Services\YooKassaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ApiController
{
    public function __construct(
        private ConfigService $configService,
        private OrderService $orderService,
        private YooKassaService $yooKassaService,
    ) {}

    public function getVersion(): JsonResponse
    {
        return new JsonResponse([
            'version' => config('api.version'),
        ]);
    }

    public function getConfig(Request $request): JsonResponse
    {
        $device = Device::query()->find($request->deviceId);

        $config = $this->configService->getConfigForDevice($device);

        return new JsonResponse($config);
    }

    public function createOrder(CreateOrderRequest $request): JsonResponse
    {
        $client = Device::find($request->deviceId)?->client
            ?? Client::createByDevice(Device::make(['id' => $request->deviceId, 'model' => $request->deviceModel]));

        if ($client->getActiveSubscription()) {
            return new JsonResponse(['message' => 'Subscription already active'], 400);
        }

        $period = SubscriptionPeriod::from($request->period);

        $order = $this->orderService->createOrder($client, $period);

        return new JsonResponse($order);
    }

    public function createPayment(Order $order): JsonResponse
    {
        $payment = $this->orderService->pay($order);

        return new JsonResponse($payment);
    }

    public function checkPayment(Payment $payment): JsonResponse
    {
        if (!$payment->status->isFinal()) {
            $this->yooKassaService->check($payment);
        }

        return new JsonResponse($payment);
    }

    public function getPayment(Order $order): JsonResponse
    {
        return new JsonResponse($order->payment);
    }
}

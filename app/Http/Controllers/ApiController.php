<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\CreateOrderRequest;
use App\Http\Requests\CreatePaymentRequest;
use App\Models\Client;
use App\Models\Device;
use App\Models\Order;
use App\Models\Values\SubscriptionPeriod;
use App\Queries\GetConfigQuery;
use App\Services\OrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ApiController
{
    public function __construct(
        private GetConfigQuery $getConfigQuery,
        private OrderService $orderService,
    ) {}

    public function getVersion(): JsonResponse
    {
        return new JsonResponse([
            'version' => config('api.version'),
        ]);
    }

    public function getConfig(Request $request): JsonResponse
    {
        $config = ($this->getConfigQuery)($request->deviceId);

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

    public function createPayment(Order $order, CreatePaymentRequest $request): JsonResponse
    {
        $payment = $this->orderService->pay($order, $request->token);

        return new JsonResponse($payment);
    }
}

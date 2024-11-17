<?php

use App\Http\Controllers\ApiController;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', fn(Request $request) => $request->user())
    ->middleware('auth:sanctum');

Route::get('/config', [ApiController::class, 'getConfig']);

Route::post('/order', [ApiController::class, 'createOrder']);

Route::post('/order/{order}/payment', [ApiController::class, 'createPayment']);

Route::get('/order/{order}', fn (Order $order) => $order);

Route::get('/order/{order}/payment', fn (Order $order) => $order->payment);

Route::get('/payment/{payment}', fn (Payment $payment) => $payment);

Route::post('/payment/{payment}/check', [ApiController::class, 'checkPayment']);

Route::get('/version', [ApiController::class, 'getVersion']);

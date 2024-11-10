<?php

use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', fn(Request $request) => $request->user())
    ->middleware('auth:sanctum');

Route::get('/config', [ApiController::class, 'getConfig']);

Route::post('/order', [ApiController::class, 'createOrder']);

Route::get('/version', [ApiController::class, 'getVersion']);

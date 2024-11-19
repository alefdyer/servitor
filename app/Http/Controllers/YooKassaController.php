<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\YooKassaService;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Request;

class YooKassaController {
    public function __construct(
        private YooKassaService $yooKassaService,
    )
    {
    }
    public function notify(Request $request) {
        $event = $request->toArray();
        Log::info('YooKassa event', $event);

        // @TODO: update payment status
    }
}

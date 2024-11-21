<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Services\YooKassaService;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Request;

class YooKassaController
{
    public function __construct(
        private YooKassaService $yooKassaService,
    ) {}
    public function notify(Request $request)
    {
        $notification = $request->toArray();
        Log::info('YooKassa notification', $notification);

        if ('notification' !== $notification['type']) {
            return;
        }

        @[$type] = explode('.', $notification['event'] ?? '');

        switch ($type) {
            case 'payment':
                /** @var Payment $payment */
                if ($payment = Payment::find($notification['object']['id'] ?? null)) {
                    $payment->updateByResponse($notification['object']);
                }
                break;
            default:
                Log::warning("Unknown notification object type: $type");
        }
    }
}

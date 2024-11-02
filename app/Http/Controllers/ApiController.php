<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Queries\GetConfigQuery;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ApiController
{
    public function __construct(
        private GetConfigQuery $getConfigQuery,
    ) {}

    public function getVersion(): JsonResponse
    {
        return new JsonResponse([
            'version' => config('api.version'),
        ]);
    }

    public function getConfig(Request $request): JsonResponse
    {
        $config = ($this->getConfigQuery)($request->deviceId ?? '');

        return new JsonResponse($config);
    }
}

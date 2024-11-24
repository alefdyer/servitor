<?php

return [
    /**
     * Версия API.
     * Должна быть синхронна с версией приложения.
     */
    'version' => env('API_VERSION', '1.1'),

    /**
     * Период времени между показами рекламами в бесплатной версии приложения (минуты)
     */
    'breakForAdsInterval' => 60,
];

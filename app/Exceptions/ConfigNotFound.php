<?php

declare(strict_types=1);

namespace App\Exceptions;

class ConfigNotFound extends DomainException
{
    public function __construct()
    {
        parent::__construct('Конфигурация не найдена');
    }
}

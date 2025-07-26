<?php

declare(strict_types=1);

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \App\Services\CryptoService
 */
final class CryptoService extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \App\Services\CryptoService::class;
    }
}

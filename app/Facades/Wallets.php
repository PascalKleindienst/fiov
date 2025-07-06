<?php

declare(strict_types=1);

namespace App\Facades;

use App\Services\WalletService;
use Illuminate\Support\Facades\Facade;

/**
 * @see WalletService
 */
final class Wallets extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return WalletService::class;
    }
}

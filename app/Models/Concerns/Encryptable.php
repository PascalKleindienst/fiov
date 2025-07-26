<?php

declare(strict_types=1);

namespace App\Models\Concerns;

use App\Facades\CryptoService;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin Model
 */
trait Encryptable
{
    public static function bootEncryptable(): void
    {
        self::$encrypter ??= CryptoService::withDEK();
    }
}

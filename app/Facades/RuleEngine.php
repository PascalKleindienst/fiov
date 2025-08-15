<?php

declare(strict_types=1);

namespace App\Facades;

use App\Services\RuleEngineService;
use Illuminate\Support\Facades\Facade;

/**
 * @see RuleEngineService
 */
final class RuleEngine extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return RuleEngineService::class;
    }
}

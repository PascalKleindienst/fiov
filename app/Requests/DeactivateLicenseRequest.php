<?php

declare(strict_types=1);

namespace App\Requests;

use App\Models\License;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;

/**
 * @url https://docs.lemonsqueezy.com/api/license-api/deactivate-license-key
 */
final readonly class DeactivateLicenseRequest
{
    public static function make(): self
    {
        return app(self::class);
    }

    /**
     * @return array{deactivated?: bool, error?: string}
     *
     * @throws ConnectionException
     */
    public function send(License $license): array
    {
        return Http::license()->post('deactivate', [
            'license_key' => $license->key,
            'instance_id' => $license->instance?->id,
        ])->json();
    }
}

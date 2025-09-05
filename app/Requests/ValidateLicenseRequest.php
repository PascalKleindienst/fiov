<?php

declare(strict_types=1);

namespace App\Requests;

use App\Data\License;
use App\Models\License as LicenseModel;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;

/**
 * @url https://docs.lemonsqueezy.com/api/license-api/validate-license-key
 */
final readonly class ValidateLicenseRequest
{
    public static function make(): self
    {
        return app(self::class);
    }

    /**
     * @throws ConnectionException
     */
    public function send(LicenseModel $license): License
    {
        $response = Http::license()->post('validate', [
            'license_key' => $license->key,
            'instance_id' => $license->instance?->id,
        ]);

        return License::fromRequest($response->json());
    }
}

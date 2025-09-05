<?php

declare(strict_types=1);

namespace App\Requests;

use App\Data\License;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;

/**
 * @url https://docs.lemonsqueezy.com/api/license-api/activate-license-key
 */
final readonly class ActivateLicenseRequest
{
    public static function make(): self
    {
        return app(self::class);
    }

    /**
     * @throws ConnectionException
     */
    public function send(string $licenseKey): License
    {
        $response = Http::license()->post('activate', [
            'license_key' => $licenseKey,
            'instance_name' => 'Fiov Pro',
        ]);

        return License::fromRequest($response->json());
    }
}

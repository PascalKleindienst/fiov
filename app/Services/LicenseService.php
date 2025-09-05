<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\LicenseStatus;
use App\Exceptions\LicenseActivationFailedException;
use App\Models\License;
use App\Requests\ActivateLicenseRequest;
use App\Requests\DeactivateLicenseRequest;
use App\Requests\ValidateLicenseRequest;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Throwable;

final readonly class LicenseService
{
    /**
     * @throws LicenseActivationFailedException
     */
    public function activate(string $key): License
    {
        try {
            $license = ActivateLicenseRequest::make()->send($key);

            if ($license->activated !== true && $license->error) {
                throw new LicenseActivationFailedException($license->error);
            }

            if ($license->meta?->storeId !== config('fiov.license.store_id')) {
                throw new LicenseActivationFailedException('License is not from the official store');
            }
        } catch (Throwable $throwable) {
            Log::error('Failed to activate license', ['key' => $key, 'error' => $throwable->getMessage()]);
            throw new LicenseActivationFailedException($throwable->getMessage());
        }

        return $this->updateLicenseStatus($license);
    }

    /**
     * @throws LicenseActivationFailedException
     * @throws Throwable
     * @throws ConnectionException
     */
    public function deactivate(?License $license = null): void
    {
        $license ??= License::query()->latest()->first();

        if (! $license) {
            throw new LicenseActivationFailedException('No active license found');
        }

        try {
            $response = DeactivateLicenseRequest::make()->send($license);

            if ($response['deactivated'] ?? false) {
                $this->delete($license);
            }

            if ($response['error'] ?? false) {
                throw new LicenseActivationFailedException($response['error']);
            }
        } catch (Throwable $throwable) {
            // License could not be found -> delete it
            if ($throwable instanceof ConnectionException) {
                $this->delete($license);
            }

            Log::error('Failed to deactivate license', ['key' => $license->key, 'error' => $throwable->getMessage()]);
            throw $throwable;
        }
    }

    public function isCommunity(): bool
    {
        return ! $this->isPro();
    }

    public function isPro(): bool
    {
        return $this->status()->isValid();
    }

    public function status(bool $cached = true): LicenseStatus
    {
        if ($cached && Cache::has('license_status')) {
            return Cache::get('license_status');
        }

        $license = License::query()->latest()->first();

        if (! $license) {
            return LicenseStatus::No_License;
        }

        // TODO: Validate License to check
        try {
            $this->updateLicenseStatus(ValidateLicenseRequest::make()->send($license));

            return LicenseStatus::Valid;
        } catch (ConnectionException|DecryptException) {
            Cache::put('license_status', LicenseStatus::Invalid, now()->addWeek());

            return LicenseStatus::Invalid;
        } catch (Throwable $throwable) {
            Log::error('Failed to validate license', ['key' => $license->key, 'error' => $throwable->getMessage()]);
        }

        return LicenseStatus::Unknown;
    }

    private function updateLicenseStatus(\App\Data\License $license): License
    {
        Cache::put('license_status', LicenseStatus::Valid, now()->addWeek());

        return License::query()->updateOrCreate(
            ['hash' => hash('sha256', $license->key->key ?? '')],
            [
                'key' => $license->key?->key,
                'status' => $license->key?->status,
                'instance' => $license->instance,
                'meta' => $license->meta,
                'expires_at' => $license->key?->expiresAt,
            ]
        );
    }

    private function delete(License $license): void
    {
        Cache::forget('license_status');
        $license->delete();
    }
}

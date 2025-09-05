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

        // Validate License to check
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

    public function license(bool $cached = true): ?License
    {
        if ($cached && Cache::has('license')) {
            return Cache::get('license');
        }

        $license = License::query()->latest()->first();

        if (! $license) {
            Cache::forget('license');

            return null;
        }

        // Validate License to check
        try {
            return $this->updateLicenseStatus(ValidateLicenseRequest::make()->send($license));
        } catch (ConnectionException|DecryptException) {
            Cache::put('license', null, now()->addWeek());
        } catch (Throwable $throwable) {
            Log::error('Failed to validate license', ['key' => $license->key, 'error' => $throwable->getMessage()]);
        }

        return null;
    }

    private function updateLicenseStatus(\App\Data\License $data): License
    {
        $license = License::query()->updateOrCreate(
            ['hash' => hash('sha256', $data->key->key ?? '')],
            [
                'key' => $data->key?->key,
                'status' => $data->key?->status,
                'instance' => $data->instance,
                'meta' => $data->meta,
                'expires_at' => $data->key?->expiresAt,
            ]
        );

        Cache::put('license_status', LicenseStatus::Valid, now()->addWeek());
        Cache::put('license', $license, now()->addWeek());

        return $license;
    }

    private function delete(License $license): void
    {
        Cache::forget('license_status');
        Cache::forget('license');
        $license->delete();
    }
}

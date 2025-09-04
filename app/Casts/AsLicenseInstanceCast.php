<?php

declare(strict_types=1);

namespace App\Casts;

use App\Data\LicenseInstance;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;
use JsonException;

/**
 * @implements CastsAttributes<?LicenseInstance, mixed>
 */
final class AsLicenseInstanceCast implements CastsAttributes
{
    /**
     * @throws JsonException
     */
    public function get(Model $model, string $key, mixed $value, array $attributes): ?LicenseInstance
    {
        if (empty($value)) {
            return null;
        }

        return LicenseInstance::fromRequest(json_decode($value, true, flags: JSON_THROW_ON_ERROR));
    }

    /**
     * @throws JsonException
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): ?string
    {
        if (empty($value)) {
            return null;
        }

        if (! $value instanceof LicenseInstance) {
            throw new InvalidArgumentException('The given value is not an LicenseInstance instance.');
        }

        return $value->toJson();
    }
}

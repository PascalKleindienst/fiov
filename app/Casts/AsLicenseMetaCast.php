<?php

declare(strict_types=1);

namespace App\Casts;

use App\Data\LicenseMeta;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;
use JsonException;

/**
 * @implements CastsAttributes<?LicenseMeta, mixed>
 */
final class AsLicenseMetaCast implements CastsAttributes
{
    /**
     * @throws JsonException
     */
    public function get(Model $model, string $key, mixed $value, array $attributes): ?LicenseMeta
    {
        if (empty($value)) {
            return null;
        }

        return LicenseMeta::fromRequest(json_decode($value, true, flags: JSON_THROW_ON_ERROR));
    }

    /**
     * @throws JsonException
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): ?string
    {
        if (empty($value)) {
            return null;
        }

        if (! $value instanceof LicenseMeta) {
            throw new InvalidArgumentException('The given value is not an LicenseMeta instance.');
        }

        return $value->toJson();
    }
}

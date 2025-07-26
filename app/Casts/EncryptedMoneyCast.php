<?php

declare(strict_types=1);

namespace App\Casts;

use Cknow\Money\Casts\MoneyDecimalCast;
use Exception;
use Illuminate\Database\Eloquent\Model;

final class EncryptedMoneyCast extends MoneyDecimalCast
{
    /**
     * Transform the attribute from the underlying model values.
     *
     * @param  Model  $model
     * @param  string  $value
     * @param  array<string, int|string>  $attributes
     */
    public function get($model, string $key, mixed $value, array $attributes): ?\Cknow\Money\Money  // @pest-ignore-type
    {
        try {
            $value = $model::$encrypter?->decrypt($value)['amount'];
        } catch (Exception) {
        }

        return parent::get($model, $key, $value, $attributes);
    }

    /**
     * @param  Model  $model
     * @param  string  $value
     * @param  array{currency?: string}  $attributes
     * @return array{value: string|null, currency: string}|mixed
     */
    public function set($model, string $key, mixed $value, array $attributes): mixed  // @pest-ignore-type
    {
        try {
            return [
                'amount' => $model::$encrypter?->encrypt(parent::set($model, $key, $value, $attributes)),
                'currency' => $attributes['currency'] ?? config('money.defaultCurrency'),
            ];
        } catch (Exception) {
            return parent::set($model, $key, $value, $attributes);
        }
    }
}

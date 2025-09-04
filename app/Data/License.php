<?php

declare(strict_types=1);

namespace App\Data;

use App\Concerns\IsJsonable;
use DragonCode\Contracts\Support\Jsonable;
use Illuminate\Contracts\Support\Arrayable;

/**
 * @implements Arrayable<string, string|int>
 */
final readonly class License implements Arrayable, Jsonable
{
    use IsJsonable;

    public function __construct(
        public ?bool $activated,
        public ?bool $valid,
        public ?string $error,
        public ?LicenseInstance $instance,
        public ?LicenseKey $key,
        public ?LicenseMeta $meta,
    ) {}

    /**
     * @param  array{
     *     activated?: ?bool,
     *     valid?: ?bool,
     *     error: ?string,
     *     instance: ?array{id: string, name: string, created_at: string},
     *     license_key: ?array{id: int, key: string, status: string, activation_limit: int, activation_usage: int, created_at: string, expires_at: ?string},
     *     meta: ?array{store_id: int, customer_id: int, customer_name: string, customer_email: string}
     * }  $request
     */
    public static function fromRequest(array $request): self
    {
        return new self(
            activated: $request['activated'] ?? null,
            valid: $request['valid'] ?? null,
            error: $request['error'] ?? null,
            instance: ($request['instance'] ?? false) ? LicenseInstance::fromRequest($request['instance']) : null,
            key: ($request['license_key'] ?? false) ? LicenseKey::fromRequest($request['license_key']) : null,
            meta: ($request['meta'] ?? false) ? LicenseMeta::fromRequest($request['meta']) : null,
        );
    }

    /**
     * @return array{
     *        activated: ?bool,
     *        valid: ?bool,
     *        error: ?string,
     *        instance: array{id: string, name: string, created_at: string}|null,
     *        license_key: array{id: int, key: string, status: string, activation_limit: int, activation_usage: int, created_at: string, expires_at: ?string}|null,
     *        meta: array{store_id: int, customer_id: int, customer_name: string, customer_email: string}|null
     *        }
     */
    public function toArray(): array
    {
        return [
            'activated' => $this->activated,
            'valid' => $this->valid,
            'error' => $this->error,
            'instance' => $this->instance?->toArray(),
            'license_key' => $this->key?->toArray(),
            'meta' => $this->meta?->toArray(),
        ];
    }
}

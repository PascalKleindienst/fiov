<?php

declare(strict_types=1);

namespace App\Data;

use App\Concerns\IsJsonable;
use Carbon\CarbonImmutable;
use DragonCode\Contracts\Support\Jsonable;
use Illuminate\Contracts\Support\Arrayable;

/**
 * @implements Arrayable<string, string|int>
 */
final readonly class LicenseKey implements Arrayable, Jsonable
{
    use IsJsonable;

    public function __construct(
        public int $id,
        public string $key,
        public string $status,
        public int $activationLimit,
        public int $activationUsage,
        public CarbonImmutable $createdAt,
        public ?CarbonImmutable $expiresAt,
    ) {}

    /**
     * @param  array{id: int, key: string, status: string, activation_limit: int, activation_usage: int, created_at: string, expires_at: ?string}  $request
     */
    public static function fromRequest(array $request): self
    {
        return new self(
            id: $request['id'],
            key: $request['key'],
            status: $request['status'],
            activationLimit: $request['activation_limit'],
            activationUsage: $request['activation_usage'],
            createdAt: CarbonImmutable::parse($request['created_at']),
            expiresAt: $request['expires_at'] ? CarbonImmutable::parse($request['expires_at']) : null,
        );
    }

    /**
     * @return array{id: int, key: string, status: string, activation_limit: int, activation_usage: int, created_at: string, expires_at: ?string}
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'key' => $this->key,
            'status' => $this->status,
            'activation_limit' => $this->activationLimit,
            'activation_usage' => $this->activationUsage,
            'created_at' => $this->createdAt->toDateTimeString(),
            'expires_at' => $this->expiresAt?->toDateTimeString(),
        ];
    }
}

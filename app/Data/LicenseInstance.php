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
final readonly class LicenseInstance implements Arrayable, Jsonable
{
    use IsJsonable;

    public function __construct(
        public string $id,
        public string $name,
        public CarbonImmutable $createdAt
    ) {}

    /**
     * @param  array{id: string, name: string, created_at: string}  $request
     */
    public static function fromRequest(array $request): self
    {
        return new self(
            id: $request['id'],
            name: $request['name'],
            createdAt: CarbonImmutable::parse($request['created_at']),
        );
    }

    /**
     * @return array{id: string, name: string, created_at: string}
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'created_at' => $this->createdAt->toDateTimeString(),
        ];
    }
}

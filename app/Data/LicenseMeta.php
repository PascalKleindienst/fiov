<?php

declare(strict_types=1);

namespace App\Data;

use App\Concerns\IsJsonable;
use DragonCode\Contracts\Support\Jsonable;
use Illuminate\Contracts\Support\Arrayable;

/**
 * @implements Arrayable<string, string|int>
 */
final readonly class LicenseMeta implements Arrayable, Jsonable
{
    use IsJsonable;

    public function __construct(
        public int $storeId,
        public int $customerId,
        public string $customerName,
        public string $customerEmail
    ) {}

    /**
     * @param  array{store_id: int, customer_id: int, customer_name: string, customer_email: string}  $request
     */
    public static function fromRequest(array $request): self
    {
        return new self(
            storeId: $request['store_id'],
            customerId: $request['customer_id'],
            customerName: $request['customer_name'],
            customerEmail: $request['customer_email'],
        );
    }

    /**
     * @return array{store_id: int, customer_id: int, customer_name: string, customer_email: string}
     */
    public function toArray(): array
    {
        return [
            'store_id' => $this->storeId,
            'customer_id' => $this->customerId,
            'customer_name' => $this->customerName,
            'customer_email' => $this->customerEmail,
        ];
    }
}

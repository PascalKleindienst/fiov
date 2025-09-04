<?php

declare(strict_types=1);

namespace Tests\Unit\Data;

use App\Data\LicenseMeta;

it('can be instantiated with all properties', function (): void {
    $meta = new LicenseMeta(1, 1, 'customer', 'customer@test.com');

    expect($meta->storeId)->toBe(1)
        ->and($meta->customerId)->toBe(1)
        ->and($meta->customerName)->toBe('customer')
        ->and($meta->customerEmail)->toBe('customer@test.com');
});

it('can be created from a request', function (): void {
    $request = [
        'store_id' => 1,
        'customer_id' => 1,
        'customer_name' => 'customer',
        'customer_email' => 'customer@test.com',
    ];

    $meta = LicenseMeta::fromRequest($request);

    expect($meta->storeId)->toBe(1)
        ->and($meta->customerId)->toBe(1)
        ->and($meta->customerName)->toBe('customer')
        ->and($meta->customerEmail)->toBe('customer@test.com');
});

it('can be converted to an array', function (): void {
    $meta = new LicenseMeta(1, 1, 'customer', 'customer@test.com');

    $expected = [
        'store_id' => 1,
        'customer_id' => 1,
        'customer_name' => 'customer',
        'customer_email' => 'customer@test.com',
    ];

    expect($meta->toArray())->toBe($expected);
});

it('can be converted to a json string', function (): void {
    $meta = new LicenseMeta(1, 1, 'customer', 'customer@test.com');

    $expected = json_encode([
        'store_id' => 1,
        'customer_id' => 1,
        'customer_name' => 'customer',
        'customer_email' => 'customer@test.com',
    ]);

    expect($meta->toJson())->toBeJson()->toBe($expected);
});

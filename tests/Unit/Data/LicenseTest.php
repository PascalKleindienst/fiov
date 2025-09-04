<?php

declare(strict_types=1);

namespace Tests\Unit\Data;

use App\Data\License;
use App\Data\LicenseInstance;
use App\Data\LicenseKey;
use App\Data\LicenseMeta;
use Carbon\CarbonImmutable;

it('can be instantiated with all properties', function (): void {
    $instance = new LicenseInstance('1', 'test.com', new CarbonImmutable());
    $key = new LicenseKey(1, 'key', 'active', 10, 5, new CarbonImmutable(), null);
    $meta = new LicenseMeta(1, 1, 'customer', 'customer@test.com');

    $license = new License(true, true, null, $instance, $key, $meta);

    expect($license->activated)->toBeTrue()
        ->and($license->valid)->toBeTrue()
        ->and($license->error)->toBeNull()
        ->and($license->instance)->toBe($instance)
        ->and($license->key)->toBe($key)
        ->and($license->meta)->toBe($meta);
});

it('can be created from a request', function (): void {
    $request = [
        'activated' => true,
        'valid' => true,
        'error' => null,
        'instance' => ['id' => '1', 'name' => 'test.com', 'created_at' => now()->toDateTimeString()],
        'license_key' => ['id' => 1, 'key' => 'key', 'status' => 'active', 'activation_limit' => 10, 'activation_usage' => 5, 'created_at' => now()->toDateTimeString(), 'expires_at' => null],
        'meta' => ['store_id' => 1, 'customer_id' => 1, 'customer_name' => 'customer', 'customer_email' => 'customer@test.com'],
    ];

    $license = License::fromRequest($request);

    expect($license->activated)->toBeTrue()
        ->and($license->valid)->toBeTrue()
        ->and($license->error)->toBeNull()
        ->and($license->instance)->toBeInstanceOf(LicenseInstance::class)
        ->and($license->key)->toBeInstanceOf(LicenseKey::class)
        ->and($license->meta)->toBeInstanceOf(LicenseMeta::class);
});

it('can be converted to an array', function (): void {
    $instance = new LicenseInstance('1', 'test.com', new CarbonImmutable());
    $key = new LicenseKey(1, 'key', 'active', 10, 5, new CarbonImmutable(), null);
    $meta = new LicenseMeta(1, 1, 'customer', 'customer@test.com');

    $license = new License(true, true, null, $instance, $key, $meta);

    $expected = [
        'activated' => true,
        'valid' => true,
        'error' => null,
        'instance' => $instance->toArray(),
        'license_key' => $key->toArray(),
        'meta' => $meta->toArray(),
    ];

    expect($license->toArray())->toBe($expected);
});

it('can be converted to a json string', function (): void {
    $instance = new LicenseInstance('1', 'test.com', new CarbonImmutable());
    $key = new LicenseKey(1, 'key', 'active', 10, 5, new CarbonImmutable(), null);
    $meta = new LicenseMeta(1, 1, 'customer', 'customer@test.com');

    $license = new License(true, true, null, $instance, $key, $meta);

    $expected = json_encode([
        'activated' => true,
        'valid' => true,
        'error' => null,
        'instance' => $instance->toArray(),
        'license_key' => $key->toArray(),
        'meta' => $meta->toArray(),
    ]);

    expect($license->toJson())->toBeJson()->toBe($expected);
});

it('can handle missing optional data from request', function (): void {
    $request = [
        'error' => 'Some error',
    ];

    $license = License::fromRequest($request);

    expect($license->activated)->toBeNull()
        ->and($license->valid)->toBeNull()
        ->and($license->error)->toBe('Some error')
        ->and($license->instance)->toBeNull()
        ->and($license->key)->toBeNull()
        ->and($license->meta)->toBeNull();
});

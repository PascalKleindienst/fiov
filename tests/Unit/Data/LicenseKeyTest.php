<?php

declare(strict_types=1);

namespace Tests\Unit\Data;

use App\Data\LicenseKey;
use Carbon\CarbonImmutable;

it('can be instantiated with all properties', function (): void {
    $now = new CarbonImmutable();
    $key = new LicenseKey(1, 'key', 'active', 10, 5, $now, null);

    expect($key->id)->toBe(1)
        ->and($key->key)->toBe('key')
        ->and($key->status)->toBe('active')
        ->and($key->activationLimit)->toBe(10)
        ->and($key->activationUsage)->toBe(5)
        ->and($key->createdAt)->toBe($now)
        ->and($key->expiresAt)->toBeNull();
});

it('can be created from a request', function (): void {
    $now = new CarbonImmutable();
    $request = [
        'id' => 1,
        'key' => 'key',
        'status' => 'active',
        'activation_limit' => 10,
        'activation_usage' => 5,
        'created_at' => $now->toDateTimeString(),
        'expires_at' => null,
    ];

    $key = LicenseKey::fromRequest($request);

    expect($key->id)->toBe(1)
        ->and($key->key)->toBe('key')
        ->and($key->status)->toBe('active')
        ->and($key->activationLimit)->toBe(10)
        ->and($key->activationUsage)->toBe(5)
        ->and($key->createdAt->toDateTimeString())->toBe($now->toDateTimeString())
        ->and($key->expiresAt)->toBeNull();
});

it('can be converted to an array', function (): void {
    $now = new CarbonImmutable();
    $key = new LicenseKey(1, 'key', 'active', 10, 5, $now, null);

    $expected = [
        'id' => 1,
        'key' => 'key',
        'status' => 'active',
        'activation_limit' => 10,
        'activation_usage' => 5,
        'created_at' => $now->toDateTimeString(),
        'expires_at' => null,
    ];

    expect($key->toArray())->toBe($expected);
});

it('can be converted to a json string', function (): void {
    $now = new CarbonImmutable();
    $key = new LicenseKey(1, 'key', 'active', 10, 5, $now, null);

    $expected = json_encode([
        'id' => 1,
        'key' => 'key',
        'status' => 'active',
        'activation_limit' => 10,
        'activation_usage' => 5,
        'created_at' => $now->toDateTimeString(),
        'expires_at' => null,
    ]);

    expect($key->toJson())->toBeJson()->toBe($expected);
});

it('can handle nullable expires at', function (): void {
    $now = new CarbonImmutable();
    $key = new LicenseKey(1, 'key', 'active', 10, 5, $now, $now);

    expect($key->expiresAt)->toBe($now);

    $request = [
        'id' => 1,
        'key' => 'key',
        'status' => 'active',
        'activation_limit' => 10,
        'activation_usage' => 5,
        'created_at' => $now->toDateTimeString(),
        'expires_at' => $now->toDateTimeString(),
    ];

    $keyFromRequest = LicenseKey::fromRequest($request);

    expect($keyFromRequest->expiresAt->toDateTimeString())->toBe($now->toDateTimeString());
});

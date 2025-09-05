<?php

declare(strict_types=1);

namespace Tests\Unit\Data;

use App\Data\LicenseInstance;
use Carbon\CarbonImmutable;

it('can be instantiated with all properties', function (): void {
    $now = new CarbonImmutable();
    $instance = new LicenseInstance('1', 'test.com', $now);

    expect($instance->id)->toBe('1')
        ->and($instance->name)->toBe('test.com')
        ->and($instance->createdAt)->toBe($now);
});

it('can be created from a request', function (): void {
    $now = new CarbonImmutable();
    $request = ['id' => '1', 'name' => 'test.com', 'created_at' => $now->toDateTimeString()];

    $instance = LicenseInstance::fromRequest($request);

    expect($instance->id)->toBe('1')
        ->and($instance->name)->toBe('test.com')
        ->and($instance->createdAt->toDateTimeString())->toBe($now->toDateTimeString());
});

it('can be converted to an array', function (): void {
    $now = new CarbonImmutable();
    $instance = new LicenseInstance('1', 'test.com', $now);

    $expected = [
        'id' => '1',
        'name' => 'test.com',
        'created_at' => $now->toDateTimeString(),
    ];

    expect($instance->toArray())->toBe($expected);
});

it('can be converted to a json string', function (): void {
    $now = new CarbonImmutable();
    $instance = new LicenseInstance('1', 'test.com', $now);

    $expected = json_encode([
        'id' => '1',
        'name' => 'test.com',
        'created_at' => $now->toDateTimeString(),
    ]);

    expect($instance->toJson())->toBeJson()->toEqual($expected);
});

<?php

declare(strict_types=1);

use App\Enums\LicenseStatus;
use App\Exceptions\LicenseActivationFailedException;
use App\Models\License;
use App\Services\LicenseService;
use Carbon\CarbonImmutable;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

use function Pest\Laravel\assertDatabaseCount;
use function Pest\Laravel\assertDatabaseMissing;

beforeEach(function (): void {
    $this->service = new LicenseService();
});

// Activate
it('activates a license', function (): void {
    // Arrange
    $licenseData = [
        'activated' => true,
        'instance' => [
            'id' => '1',
            'name' => 'test',
            'created_at' => CarbonImmutable::now(),
        ],
        'license_key' => [
            'id' => 1,
            'status' => 'active',
            'key' => 'key',
            'activation_limit' => 1,
            'activation_usage' => 1,
            'created_at' => CarbonImmutable::now(),
            'expires_at' => CarbonImmutable::now(),
        ],
        'meta' => [
            'store_id' => 1,
            'customer_id' => 1,
            'customer_name' => 'name',
            'customer_email' => 'email',
        ],
    ];

    Http::fake([
        '*' => Http::response($licenseData),
    ]);

    config(['fiov.license.store_id' => 1]);

    // Act
    $result = $this->service->activate('test-key');

    // Assert
    assertDatabaseCount('licenses', 1);
    expect($result)->toBeInstanceOf(License::class);
});

it('throws exception on activation error', function (): void {
    // Arrange
    Http::fake([
        '*' => Http::response(['error' => 'Invalid key']),
    ]);

    // Act & Assert
    expect(fn () => $this->service->activate('test-key'))
        ->toThrow(LicenseActivationFailedException::class, 'Invalid key');
});

it('throws exception on store id mismatch', function (): void {
    // Arrange
    $licenseData = [
        'activated' => true,
        'instance' => [
            'id' => '1',
            'name' => 'test',
            'created_at' => CarbonImmutable::now(),
        ],
        'license_key' => [
            'id' => 1,
            'status' => 'active',
            'key' => 'key',
            'activation_limit' => 1,
            'activation_usage' => 1,
            'created_at' => CarbonImmutable::now(),
            'expires_at' => CarbonImmutable::now(),
        ],
        'meta' => [
            'store_id' => 2,
            'customer_id' => 1,
            'customer_name' => 'name',
            'customer_email' => 'email',
        ],
    ];

    Http::fake([
        '*' => Http::response($licenseData),
    ]);

    config(['fiov.license.store_id' => 1]);

    // Act & Assert
    expect(fn () => $this->service->activate('test-key'))
        ->toThrow(LicenseActivationFailedException::class, 'License is not from the official store');
});

// Deactivate
it('deactivates a license', function (): void {
    // Arrange
    $license = License::create(['key' => 'test-key', 'status' => 'active']);
    Http::fake([
        '*' => Http::response(['deactivated' => true]),
    ]);

    // Act
    $this->service->deactivate($license);

    // Assert
    assertDatabaseMissing('licenses', ['id' => $license->id]);
    expect(Cache::has('license_status'))->toBeFalse();
});

it('throws exception on deactivation with no license', function (): void {
    // Act & Assert
    expect(fn () => $this->service->deactivate())
        ->toThrow(LicenseActivationFailedException::class, 'No active license found');
});

it('deletes license on connection error during deactivation', function (): void {
    // Arrange
    $license = License::create(['key' => 'test-key', 'status' => 'active']);
    Http::fake([
        '*' => fn () => throw new ConnectionException(),
    ]);

    // Act & Assert
    expect(fn () => $this->service->deactivate($license))
        ->toThrow(ConnectionException::class);

    assertDatabaseMissing('licenses', ['id' => $license->id]);
    expect(Cache::has('license_status'))->toBeFalse();
});

// Status
it('returns cached status', function (): void {
    // Arrange
    Cache::put('license_status', LicenseStatus::Valid, 10);

    // Act
    $status = $this->service->status();

    // Assert
    expect($status)->toBe(LicenseStatus::Valid);
    Http::assertNothingSent();
});

it('returns no license status', function (): void {
    // Act
    $status = $this->service->status(cached: false);

    // Assert
    expect($status)->toBe(LicenseStatus::No_License);
});

it('returns valid status for valid license', function (): void {
    // Arrange
    $license = License::create(['key' => 'test-key', 'status' => 'active']);
    Http::fake([
        '*' => Http::response([
            'valid' => true,
            'instance' => [
                'id' => '1',
                'name' => 'test',
                'created_at' => CarbonImmutable::now(),
            ],
            'license_key' => [
                'id' => 1,
                'status' => 'active',
                'key' => 'key',
                'activation_limit' => 1,
                'activation_usage' => 1,
                'created_at' => CarbonImmutable::now(),
                'expires_at' => CarbonImmutable::now(),
            ],
            'meta' => [
                'store_id' => 1,
                'customer_id' => 1,
                'customer_name' => 'name',
                'customer_email' => 'email',
            ],
        ]),
    ]);

    // Act
    $status = $this->service->status(cached: false);

    // Assert
    expect($status)->toBe(LicenseStatus::Valid);
});

it('returns invalid status on connection error', function (): void {
    // Arrange
    $license = License::create(['key' => 'test-key', 'status' => 'active']);
    Http::fake([
        '*' => fn () => throw new ConnectionException(),
    ]);

    // Act
    $status = $this->service->status(cached: false);

    // Assert
    expect($status)->toBe(LicenseStatus::Invalid)
        ->and(Cache::get('license_status'))->toBe(LicenseStatus::Invalid);
});

it('returns isPro as true when license status is valid in cache', function (): void {
    Cache::put('license_status', LicenseStatus::Valid, 10);
    $service = new LicenseService();

    expect($service->isPro())->toBeTrue()
        ->and($service->isCommunity())->toBeFalse();
});

it('returns isPro as false when license status is invalid in cache', function (): void {
    Cache::put('license_status', LicenseStatus::Invalid, 10);
    $service = new LicenseService();

    expect($service->isPro())->toBeFalse()
        ->and($service->isCommunity())->toBeTrue();
});

it('returns isPro as false when license status is no_license in cache', function (): void {
    Cache::put('license_status', LicenseStatus::No_License, 10);
    $service = new LicenseService();

    expect($service->isPro())->toBeFalse()
        ->and($service->isCommunity())->toBeTrue();
});

it('returns isPro as false when license status is unknown in cache', function (): void {
    Cache::put('license_status', LicenseStatus::Unknown, 10);
    $service = new LicenseService();

    expect($service->isPro())->toBeFalse()
        ->and($service->isCommunity())->toBeTrue();
});

// license
it('returns cached license', function (): void {
    // Arrange
    $license = new License(['key' => 'test-key']);
    Cache::put('license', $license, 10);

    // Act
    $result = $this->service->license();

    // Assert
    expect($result)->toEqual($license);
    Http::assertNothingSent();
});

it('returns null if no license in database', function (): void {
    // Act
    $result = $this->service->license(cached: false);

    // Assert
    expect($result)->toBeNull()
        ->and(Cache::has('license'))->toBeFalse();
});

it('returns and caches license on successful validation', function (): void {
    // Arrange
    License::create(['key' => 'test-key', 'status' => 'active']);
    $licenseData = [
        'valid' => true,
        'instance' => [
            'id' => '1',
            'name' => 'test',
            'created_at' => CarbonImmutable::now(),
        ],
        'license_key' => [
            'id' => 1,
            'status' => 'active',
            'key' => 'new-key',
            'activation_limit' => 1,
            'activation_usage' => 1,
            'created_at' => CarbonImmutable::now(),
            'expires_at' => CarbonImmutable::now(),
        ],
        'meta' => [
            'store_id' => 1,
            'customer_id' => 1,
            'customer_name' => 'name',
            'customer_email' => 'email',
        ],
    ];
    Http::fake(['*' => Http::response($licenseData)]);

    // Act
    $result = $this->service->license(cached: false);

    // Assert
    expect($result)->toBeInstanceOf(License::class)
        ->and($result->key)->toBe('new-key')
        ->and(Cache::get('license'))->toEqual($result);
});

it('returns null and caches null on connection exception', function (): void {
    // Arrange
    License::create(['key' => 'test-key', 'status' => 'active']);
    Http::fake(['*' => fn () => throw new ConnectionException()]);

    // Act
    $result = $this->service->license(cached: false);

    // Assert
    expect($result)->toBeNull()
        ->and(Cache::get('license'))->toBeNull();
});

it('returns null on generic exception during validation', function (): void {
    // Arrange
    License::create(['key' => 'test-key', 'status' => 'active']);
    Http::fake(['*' => fn () => throw new Exception('Something went wrong')]);

    // Act
    $result = $this->service->license(cached: false);

    // Assert
    expect($result)->toBeNull();
});

it('bypasses cache when specified', function (): void {
    // Arrange
    $cachedLicense = new License(['key' => 'cached-key']);
    Cache::put('license', $cachedLicense, 10);

    License::create(['key' => 'real-key', 'status' => 'active']);
    $licenseData = [
        'valid' => true,
        'instance' => ['id' => '1', 'name' => 'test', 'created_at' => CarbonImmutable::now()],
        'license_key' => ['id' => 1, 'status' => 'active', 'key' => 'validated-key', 'activation_limit' => 1, 'activation_usage' => 1, 'created_at' => CarbonImmutable::now(), 'expires_at' => CarbonImmutable::now()],
        'meta' => ['store_id' => 1, 'customer_id' => 1, 'customer_name' => 'name', 'customer_email' => 'email'],
    ];
    Http::fake(['*' => Http::response($licenseData)]);

    // Act
    $result = $this->service->license(cached: false);

    // Assert
    expect($result)->toBeInstanceOf(License::class)
        ->and($result->key)->toBe('validated-key')
        ->and($result->key)->not->toBe('cached-key');
    Http::assertSentCount(1);
});

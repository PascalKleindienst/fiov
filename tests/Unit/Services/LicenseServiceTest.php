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

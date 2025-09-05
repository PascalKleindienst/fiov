<?php

declare(strict_types=1);

use App\Data\LicenseInstance;
use App\Models\License;
use App\Requests\DeactivateLicenseRequest;
use Carbon\CarbonImmutable;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;

it('sends a deactivate license request', function (): void {
    // Arrange
    Http::fake([
        'api.lemonsqueezy.com/v1/*' => Http::response([
            'deactivated' => true,
        ]),
    ]);

    $license = License::create([
        'key' => 'test-license-key',
        'status' => 'active',
        'instance' => new LicenseInstance(
            id: '39746a71-7a31-4263-8328-6b404b84a463',
            name: 'Test Instance',
            createdAt: CarbonImmutable::now(),
        ),
    ]);

    // Act
    $response = DeactivateLicenseRequest::make()->send($license);

    // Assert
    expect($response)->toBe([
        'deactivated' => true,
    ]);

    Http::assertSent(fn (Request $request): bool => $request->url() === 'https://api.lemonsqueezy.com/v1/licenses/deactivate' &&
        $request['license_key'] === $license->key &&
        $request['instance_id'] === $license->instance->id);
});

it('handles connection errors', function (): void {
    // Arrange
    Http::fake([
        'api.lemonsqueezy.com/v1/*' => fn () => throw new ConnectionException('Could not connect to license server'),
    ]);

    $license = License::create([
        'key' => 'test-license-key',
        'status' => 'active',
        'instance' => new LicenseInstance(
            id: '39746a71-7a31-4263-8328-6b404b84a463',
            name: 'Test Instance',
            createdAt: CarbonImmutable::now(),
        ),
    ]);

    // Act & Assert
    expect(fn (): array => DeactivateLicenseRequest::make()->send($license))
        ->toThrow(ConnectionException::class, 'Could not connect to license server');
});

it('handles invalid response', function (): void {
    // Arrange
    Http::fake([
        'api.lemonsqueezy.com/v1/*' => Http::response(['error' => 'Invalid license key'], 400),
    ]);

    $license = License::create([
        'key' => 'invalid-license-key',
        'status' => 'active',
        'instance' => new LicenseInstance(
            id: '39746a71-7a31-4263-8328-6b404b84a463',
            name: 'Test Instance',
            createdAt: CarbonImmutable::now(),
        ),
    ]);

    // Act
    $response = DeactivateLicenseRequest::make()->send($license);

    // Assert
    expect($response)->toBe([
        'error' => 'Invalid license key',
    ]);
});

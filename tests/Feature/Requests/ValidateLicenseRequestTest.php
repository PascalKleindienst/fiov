<?php

declare(strict_types=1);

use App\Data\License;
use App\Data\LicenseInstance;
use App\Models\License as LicenseModel;
use App\Requests\ValidateLicenseRequest;
use Carbon\CarbonImmutable;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;

it('sends a validate license request', function (): void {
    // Arrange
    $mockResponse = [
        'valid' => true,
        'instance' => [
            'id' => 'instance-123',
            'name' => 'Test Instance',
            'created_at' => now()->toDateTimeString(),
        ],
        'license_key' => [
            'id' => 1,
            'key' => 'test-license-key',
            'status' => 'active',
            'activation_limit' => 1,
            'activation_usage' => 1,
            'created_at' => now()->toDateTimeString(),
            'expires_at' => now()->addYear()->toDateTimeString(),
        ],
        'meta' => [
            'store_id' => 12345,
            'customer_id' => 1,
            'customer_name' => 'Test User',
            'customer_email' => 'test@example.com',
        ],
    ];

    Http::fake([
        'api.lemonsqueezy.com/v1/*' => Http::response($mockResponse),
    ]);

    $license = LicenseModel::create([
        'key' => 'test-license-key',
        'status' => 'active',
        'instance' => new LicenseInstance(
            id: '39746a71-7a31-4263-8328-6b404b84a463',
            name: 'Test Instance',
            createdAt: CarbonImmutable::now(),
        ),
    ]);

    // Act
    $response = ValidateLicenseRequest::make()->send($license);

    // Assert
    expect($response)
        ->toBeInstanceOf(License::class)
        ->valid->toBeTrue()
        ->key->key->toBe('test-license-key')
        ->meta->customerName->toBe('Test User')
        ->meta->customerEmail->toBe('test@example.com');

    Http::assertSent(fn (Request $request): bool => $request->url() === 'https://api.lemonsqueezy.com/v1/licenses/validate' &&
        $request['license_key'] === $license->key &&
        $request['instance_id'] === $license->instance->id);
});

it('handles connection errors', function (): void {
    // Arrange
    Http::fake([
        'api.lemonsqueezy.com/v1/*' => fn () => throw new ConnectionException('Could not connect to license server'),
    ]);

    $license = LicenseModel::create([
        'key' => 'test-license-key',
        'status' => 'active',
        'instance' => new LicenseInstance(
            id: '39746a71-7a31-4263-8328-6b404b84a463',
            name: 'Test Instance',
            createdAt: CarbonImmutable::now(),
        ),
    ]);

    // Act & Assert
    expect(fn (): \App\Data\License => ValidateLicenseRequest::make()->send($license))
        ->toThrow(ConnectionException::class, 'Could not connect to license server');
});

it('handles invalid response', function (): void {
    // Arrange
    Http::fake([
        'api.lemonsqueezy.com/v1/*' => Http::response(['error' => 'Invalid license key'], 400),
    ]);

    $license = LicenseModel::create([
        'key' => 'invalid-license-key',
        'status' => 'active',
        'instance' => new LicenseInstance(
            id: '39746a71-7a31-4263-8328-6b404b84a463',
            name: 'Test Instance',
            createdAt: CarbonImmutable::now(),
        ),
    ]);

    // Act
    $response = ValidateLicenseRequest::make()->send($license);

    // Assert
    expect($response)
        ->toBeInstanceOf(License::class)
        ->valid->toBeNull()
        ->error->toBe('Invalid license key');
});

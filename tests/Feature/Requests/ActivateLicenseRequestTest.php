<?php

declare(strict_types=1);

use App\Data\License;
use App\Requests\ActivateLicenseRequest;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;

beforeEach(function (): void {
    $this->licenseKey = 'test-license-key-123';
    $this->endpoint = 'https://api.lemonsqueezy.com/v1/licenses/activate';
});

it('sends activation request correctly', function (): void {
    $mockResponse = [
        'activated' => true,
        'instance' => [
            'id' => 'instance-123',
            'name' => 'Test Instance',
            'created_at' => now()->toDateTimeString(),
        ],
        'license_key' => [
            'id' => 1,
            'key' => $this->licenseKey,
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
        $this->endpoint => Http::response($mockResponse, 200),
    ]);

    $result = ActivateLicenseRequest::make()->send($this->licenseKey);

    Http::assertSent(fn (\Illuminate\Http\Client\Request $request): bool => $request->url() === $this->endpoint &&
        $request['license_key'] === $this->licenseKey &&
        $request['instance_name'] === 'Fiov Pro'
    );

    expect($result)
        ->toBeInstanceOf(License::class)
        ->activated->toBeTrue()
        ->key->key->toBe($this->licenseKey)
        ->meta->customerName->toBe('Test User')
        ->meta->customerEmail->toBe('test@example.com');
});

it('handles connection errors', function (): void {
    Http::fake([
        $this->endpoint => fn () => throw new ConnectionException('Could not connect to license server'),
    ]);

    expect(fn (): \App\Data\License => ActivateLicenseRequest::make()->send($this->licenseKey))
        ->toThrow(ConnectionException::class, 'Could not connect to license server');
});

it('handles invalid response', function (): void {
    Http::fake([
        $this->endpoint => Http::response(['error' => 'Invalid license key'], 400),
    ]);

    $result = ActivateLicenseRequest::make()->send('invalid-license-key');

    expect($result)
        ->toBeInstanceOf(License::class)
        ->activated->toBeNull()
        ->valid->toBeNull()
        ->error->toBe('Invalid license key');
});

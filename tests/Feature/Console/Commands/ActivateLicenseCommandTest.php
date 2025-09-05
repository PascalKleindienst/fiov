<?php

declare(strict_types=1);

use App\Models\License;
use Carbon\CarbonImmutable;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

use function Pest\Laravel\artisan;
use function Pest\Laravel\assertDatabaseCount;

it('can activate a license', function (): void {
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
            'key' => 'test-license-key',
            'activation_limit' => 1,
            'activation_usage' => 1,
            'created_at' => CarbonImmutable::now(),
            'expires_at' => CarbonImmutable::now()->addYear(),
        ],
        'meta' => [
            'store_id' => 1,
            'customer_id' => 1,
            'customer_name' => 'Pascal',
            'customer_email' => 'pascal@example.com',
        ],
    ];

    Http::fake([
        '*' => Http::response($licenseData),
    ]);

    config(['fiov.license.store_id' => 1]);

    // Act & Assert
    artisan('fiov:license:activate', ['key' => 'test-license-key'])
        ->expectsOutputToContain('License activated successfully.')
        ->expectsOutputToContain('****-key')
        ->expectsOutputToContain('Pascal <pascal@example.com>')
        ->expectsOutputToContain(CarbonImmutable::now()->addYear()->toDateString())
        ->assertExitCode(Command::SUCCESS);

    assertDatabaseCount('licenses', 1);
    $license = License::query()->first();
    expect($license->key)->toBe('test-license-key');
});

it('handles activation failure', function (): void {
    // Arrange
    Http::fake([
        '*' => Http::response(['error' => 'Invalid license key'], 400),
    ]);

    // Act & Assert
    artisan('fiov:license:activate', ['key' => 'invalid-license-key'])
        ->expectsOutputToContain('Invalid license key')
        ->assertExitCode(Command::FAILURE);

    assertDatabaseCount('licenses', 0);
});

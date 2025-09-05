<?php

declare(strict_types=1);

use App\Models\License;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use function Pest\Laravel\artisan;
use function Pest\Laravel\assertDatabaseCount;

it('can deactivate a license', function (): void {
    // Arrange
    License::create(['key' => 'test-key', 'status' => 'active']);
    Http::fake([
        'https://api.lemonsqueezy.com/v1/licenses/validate' => Http::response(['valid' => true]),
        'https://api.lemonsqueezy.com/v1/licenses/deactivate' => Http::response(['deactivated' => true]),
    ]);

    // Act & Assert
    artisan('fiov:license:deactivate')
        ->expectsConfirmation('Do you want to deactivate your fiov license?', 'yes')
        ->expectsOutputToContain('Deactivating license...')
        ->expectsOutputToContain('License has been deactivated. Pro features are now disabled.')
        ->assertExitCode(Command::SUCCESS);

    assertDatabaseCount('licenses', 0);
});

it('handles no active license', function (): void {
    // Act & Assert
    artisan('fiov:license:deactivate')
        ->expectsOutputToContain('No active license found.')
        ->assertExitCode(Command::SUCCESS);
});

it('handles user cancellation', function (): void {
    // Arrange
    License::create(['key' => 'test-key', 'status' => 'active']);
    Http::fake([
        'https://api.lemonsqueezy.com/v1/licenses/validate' => Http::response(['valid' => true]),
    ]);

    // Act & Assert
    artisan('fiov:license:deactivate')
        ->expectsConfirmation('Do you want to deactivate your fiov license?', 'no')
        ->expectsOutputToContain('License was not deactivated.')
        ->assertExitCode(Command::SUCCESS);

    assertDatabaseCount('licenses', 1);
});

it('handles deactivation failure', function (): void {
    // Arrange
    License::create(['key' => 'test-key', 'status' => 'active']);
    Http::fake([
        'https://api.lemonsqueezy.com/v1/licenses/validate' => Http::response(['valid' => true]),
        'https://api.lemonsqueezy.com/v1/licenses/deactivate' => Http::response(['error' => 'Invalid license key'], 400),
    ]);

    // Act & Assert
    artisan('fiov:license:deactivate')
        ->expectsConfirmation('Do you want to deactivate your fiov license?', 'yes')
        ->expectsOutputToContain('Deactivating license...')
        ->expectsOutputToContain('Failed to deactivate license')
        ->assertExitCode(Command::FAILURE);

    assertDatabaseCount('licenses', 1);
});

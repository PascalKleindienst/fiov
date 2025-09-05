<?php

declare(strict_types=1);

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Process;

use function Pest\Laravel\artisan;

beforeEach(function (): void {
    // Mock File facade for directory checks
    File::shouldReceive('isWritable')
        ->andReturn(true)
        ->byDefault();

    File::shouldReceive('isReadable')
        ->andReturn(true)
        ->byDefault();

    // Mock Process facade for version checks
    Process::fake([
        'node -v' => Process::result('v22.1.0'),
        'npm -v' => Process::result('10.0.0'),
    ]);

    // Mock Mail facade
    Mail::fake();
});

it('shows success message when all checks pass', function (): void {
    artisan('fiov:status')
        ->assertSuccessful()
        ->expectsOutputToContain('Your Fiov setup should be good to go!');
});

it('logs error message when there are validation errors', function (): void {
    Process::shouldReceive('run')->andThrow(new RuntimeException('Command not found'));

    artisan('fiov:status')
        ->assertFailed()
        ->expectsOutputToContain('There are errors in your Fiov setup');
});

it('shows error message when there are validation errors', function (): void {
    File::shouldReceive('isWritable')
        ->andReturn(false)
        ->byDefault();

    Process::shouldReceive('run')->andThrow(new RuntimeException('Command not found'));

    artisan('fiov:status')
        ->assertFailed()
        ->expectsOutputToContain('The list of errors is as follows:')
        ->expectsOutputToContain('There are errors in your Fiov setup');
});

it('shows error when directory permissions are invalid', function (): void {
    File::shouldReceive('isWritable')
        ->andReturn(false)
        ->byDefault();

    artisan('fiov:status')
        ->assertFailed()
        ->expectsOutputToContain('is not readable/writable');
});

it('shows warning when mail configuration is set to log', function (): void {
    Config::set('mail.default', 'log');

    artisan('fiov:status')
        ->assertSuccessful()
        ->expectsOutputToContain('Mailer configuration');
});

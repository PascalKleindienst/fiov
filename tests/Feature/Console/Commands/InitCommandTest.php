<?php

declare(strict_types=1);

namespace Tests\Feature\Console\Commands;

use App\Models\User;
use Exception;
use Illuminate\Auth\Events\Registered;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Storage;

use function Pest\Laravel\artisan;

beforeEach(function (): void {
    Storage::fake();
    Process::fake();
    Event::fake();
    File::partialMock();

    // 1. Mock .env file existing
    File::shouldReceive('exists')->once()->with(base_path('.env'))->andReturn(true);
    File::shouldReceive('copy')->never();

    expect(config('app.key'))->not->toBeEmpty();
});

it('skips steps that are already completed', function (): void {
    // Arrange
    User::factory()->admin()->create(); // Admin user exists
    expect(User::query()->admin()->count())->toBe(1);

    // Act & Assert
    artisan('fiov:init')
        ->expectsOutputToContain('.env file exists -- skipping')
        ->expectsOutputToContain('Using app key')
        ->doesntExpectOutput('Creating database at')
        ->expectsOutputToContain('Data already seeded -- skipping')
        ->assertExitCode(Command::SUCCESS);

    // Assert side-effects
    Process::assertRan('npm install');
    Process::assertRan('npm run build');
});

it('creates an admin if needed', function (): void {
    // Arrange
    expect(User::query()->admin()->count())->toBe(0);

    // Act & Assert
    artisan('fiov:init')
        ->expectsOutputToContain('.env file exists -- skipping')
        ->expectsOutputToContain('Using app key')
        ->doesntExpectOutput('Creating database at')
        ->doesntExpectOutput('Data already seeded -- skipping')
        ->expectsQuestion('Username', 'testuser')
        ->expectsQuestion('Email', 'test@example.com')
        ->expectsQuestion('Password', 'password')
        ->assertExitCode(Command::SUCCESS);

    Event::assertDispatched(Registered::class, static fn (Registered $event): \Pest\Expectation => expect($event->user)
        ->toBeInstanceOf(User::class)
        ->and($event->user->name)->toBe('testuser')
        ->and($event->user->email)->toBe('test@example.com')
        ->and($event->user->level->isAdmin())->toBeTrue()
    );
});

it('respects the --no-assets flag', function (): void {
    // Arrange
    User::factory()->admin()->create(); // Admin user exists
    expect(User::query()->admin()->count())->toBe(1);

    // Act & Assert
    artisan('fiov:init', ['--no-assets' => true])
        ->doesntExpectOutput('Installing npm dependencies')
        ->doesntExpectOutput('Compiling frontend assets')
        ->assertExitCode(Command::SUCCESS);

    // Assert side-effects
    Process::assertNothingRan();
});

//
it('returns a failure code when an error occurs', function (): void {
    // Force an error by making the .env copy fail
    File::shouldReceive('exists')->with(base_path('.env'))->andReturn(false);
    File::shouldReceive('copy')->with(base_path('.env.example'), base_path('.env'))->andThrow(new Exception('Permission denied'));

    // Act & Assert
    artisan('fiov:init')
        ->expectsOutputToContain('Oops! Something went wrong.')
        ->expectsOutputToContain('Fiov installation or upgrade did not complete successfully.')
        ->assertExitCode(Command::FAILURE);
});

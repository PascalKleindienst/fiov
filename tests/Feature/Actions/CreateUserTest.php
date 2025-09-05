<?php

declare(strict_types=1);

use App\Actions\CreateUser;
use App\Enums\UserLevel;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;

use function Pest\Laravel\assertDatabaseHas;

it('creates a new user and a default wallet', function (): void {
    Event::fake();

    $action = app(CreateUser::class);

    $data = [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password',
        'level' => UserLevel::User->value,
    ];

    $user = $action->handle($data);

    // Assert user was created
    assertDatabaseHas('users', [
        'name' => 'Test User',
        'email' => 'test@example.com',
    ]);

    $createdUser = User::where('email', 'test@example.com')->first();
    expect($createdUser)->not->toBeNull();
    expect(Hash::check('password', $createdUser->password))->toBeTrue();
    expect($createdUser->level->value)->toBe(UserLevel::User->value);

    // Assert default wallet was created
    expect(\App\Models\Wallet::class)->databaseToHaveEncrypted([
        'user_id' => $createdUser->id,
        'title' => 'Default',
    ]);

    // Assert event was dispatched
    Event::assertDispatched(Registered::class, fn ($event): bool => $event->user->id === $createdUser->id);
});

it('logs in the user if specified', function (): void {
    $action = app(CreateUser::class);

    $data = [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password',
        'level' => UserLevel::User->value,
    ];

    $user = $action->handle($data, login: true);

    $this->assertAuthenticatedAs($user);
    expect(session(\App\Services\CryptoService::DEK_SESSION_KEY))->not->toBeNull();
});

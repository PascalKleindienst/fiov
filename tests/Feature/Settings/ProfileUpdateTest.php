<?php

declare(strict_types=1);

use App\Livewire\Settings\Profile;
use App\Models\User;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;

test('profile page is displayed', function (): void {
    // Arrange
    $this->actingAs($user = User::factory()->create());

    // Act & Assert
    $this->get('/settings/profile')->assertOk();
});

test('profile information can be updated', function (): void {
    // Arrange
    $user = User::factory()->create();

    $this->actingAs($user);

    // Act
    $response = Livewire::test(Profile::class)
        ->set('name', 'Test User')
        ->set('email', 'test@example.com')
        ->call('updateProfileInformation');

    // Assert
    $response->assertHasNoErrors();

    $user->refresh();

    expect($user->name)->toEqual('Test User');
    expect($user->email)->toEqual('test@example.com');
    expect($user->email_verified_at)->toBeNull();
});

test('email verification status is unchanged when email address is unchanged', function (): void {
    $user = User::factory()->create();

    $this->actingAs($user);

    $response = Livewire::test(Profile::class)
        ->set('name', 'Test User')
        ->set('email', $user->email)
        ->call('updateProfileInformation');

    $response->assertHasNoErrors();

    expect($user->refresh()->email_verified_at)->not->toBeNull();
});

test('user can delete their account', function (): void {
    // Arrange
    $user = User::factory()->create();

    $this->actingAs($user);

    // Act
    $response = Livewire::test('settings.delete-user-form')
        ->set('password', 'password')
        ->call('deleteUser');

    // Assert
    $response
        ->assertHasNoErrors()
        ->assertRedirect('/');

    expect($user->fresh())->toBeNull();
    expect(auth()->check())->toBeFalse();
});

test('correct password must be provided to delete account', function (): void {
    // Arrange
    $user = User::factory()->create();

    $this->actingAs($user);

    // Act
    $response = Livewire::test('settings.delete-user-form')
        ->set('password', 'wrong-password')
        ->call('deleteUser');

    // Assert
    $response->assertHasErrors(['password']);

    expect($user->fresh())->not->toBeNull();
});

test('verified user is redirected when requesting verification', function (): void {
    // Arrange
    $user = User::factory()->create([
        'email_verified_at' => now(),
    ]);

    $this->actingAs($user);

    // Act
    $response = Livewire::test(Profile::class)
        ->call('resendVerificationNotification');

    // Assert
    $response->assertRedirect(route('dashboard'));
});

test('unverified user can request verification email', function (): void {
    // Arrange
    Notification::fake();

    $user = User::factory()->unverified()->create();

    $this->actingAs($user);

    // Act
    $response = Livewire::test(Profile::class)
        ->call('resendVerificationNotification');

    // Assert
    Notification::assertSentTo($user, VerifyEmail::class);
});

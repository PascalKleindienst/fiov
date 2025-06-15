<?php

declare(strict_types=1);

use App\Livewire\Actions\Logout;
use App\Livewire\Auth\VerifyEmail;
use App\Models\User;
use Illuminate\Auth\Notifications\VerifyEmail as VerifyEmailNotification;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;

test('verified user is redirected to dashboard', function (): void {
    // Arrange
    $user = User::factory()->create([
        'email_verified_at' => now(),
    ]);

    $this->actingAs($user);

    // Act & Assert
    Livewire::test(VerifyEmail::class)
        ->call('sendVerification')
        ->assertRedirect(route('dashboard'));
});

test('unverified user receives verification email', function (): void {
    // Arrange
    Notification::fake();

    $user = User::factory()->unverified()->create();
    $this->actingAs($user);

    // Act
    Livewire::test(VerifyEmail::class)
        ->call('sendVerification');

    // Assert
    Notification::assertSentTo($user, VerifyEmailNotification::class);

    // $this->assertTrue(Session::has('status'));
    // $this->assertEquals('verification-link-sent', Session::get('status'));
});

test('logout action is called and redirects to home', function (): void {
    // Arrange
    $user = User::factory()->create();
    $this->actingAs($user);

    // Act & Assert
    Livewire::test(VerifyEmail::class)
        ->call('logout', $this->app->get(Logout::class))
        ->assertRedirect('/');

    $this->assertGuest();
});

<?php

declare(strict_types=1);

use App\Enums\UserLevel;
use App\Livewire\Users\InviteUser;
use App\Models\User;
use App\Notifications\UserInvitedNotification;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;

beforeEach(function (): void {
    withProLicense();
});

it('renders successfully', function (): void {
    $adminUser = User::factory()->create(['level' => UserLevel::Admin]);

    Livewire::actingAs($adminUser)
        ->test(InviteUser::class)
        ->assertStatus(200);
});

it('requires email and level', function (): void {
    $adminUser = User::factory()->create(['level' => UserLevel::Admin]);

    Livewire::actingAs($adminUser)
        ->test(InviteUser::class)
        ->set('email', '')
        ->set('level', '')
        ->call('inviteUser')
        ->assertHasErrors(['email', 'level']);
});

it('validates email format', function (): void {
    $adminUser = User::factory()->create(['level' => UserLevel::Admin]);

    Livewire::actingAs($adminUser)
        ->test(InviteUser::class)
        ->set('email', 'invalid-email')
        ->call('inviteUser')
        ->assertHasErrors(['email']);
});

it('validates unique email', function (): void {
    $adminUser = User::factory()->create(['level' => UserLevel::Admin]);
    $existingUser = User::factory()->create();

    Livewire::actingAs($adminUser)
        ->test(InviteUser::class)
        ->set('email', $existingUser->email)
        ->call('inviteUser')
        ->assertHasErrors(['email']);
});

it('validates user level enum', function (): void {
    $adminUser = User::factory()->create(['level' => UserLevel::Admin]);

    Livewire::actingAs($adminUser)
        ->test(InviteUser::class)
        ->set('level', 'invalid-level')
        ->call('inviteUser')
        ->assertHasErrors(['level']);
});

it('can invite a user', function (): void {
    Notification::fake();

    $adminUser = User::factory()->create(['level' => UserLevel::Admin]);
    $newEmail = 'newuser@example.com';
    $newLevel = UserLevel::User->value;

    Livewire::actingAs($adminUser)
        ->test(InviteUser::class)
        ->set('email', $newEmail)
        ->set('level', $newLevel)
        ->call('inviteUser')
        ->assertDispatched('toast-show')
        ->assertDispatched('modal-close');

    Notification::assertSentTo(new \Illuminate\Notifications\AnonymousNotifiable(), UserInvitedNotification::class, fn ($notification, $channels): bool => $notification->userLevel->value === $newLevel);
});

it('cannot invite a user if not an admin', function (): void {
    $nonAdminUser = User::factory()->create();

    Livewire::actingAs($nonAdminUser)
        ->test(InviteUser::class)
        ->set('email', 'anotheruser@example.com')
        ->set('level', UserLevel::User->value)
        ->call('inviteUser')
        ->assertForbidden();
});

it('handles notification sending failure', function (): void {
    Notification::fake();
    Notification::shouldReceive('route')->andThrow(new Exception('Failed to send notification'));

    $adminUser = User::factory()->create(['level' => UserLevel::Admin]);

    Livewire::actingAs($adminUser)
        ->test(InviteUser::class)
        ->set('email', 'fail@example.com')
        ->set('level', UserLevel::User->value)
        ->call('inviteUser')
        ->assertDispatched('toast-show', fn ($name, array $data): bool => $data['dataset']['variant'] === 'danger');

    // Notification::assertNothingSent();
});

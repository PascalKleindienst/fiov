<?php

declare(strict_types=1);

use App\Enums\UserLevel;
use App\Livewire\Users\EditUser;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function (): void {
    withProLicense();
});

it('renders successfully and pre-fills form', function (): void {
    $userToEdit = User::factory()->create();
    $adminUser = User::factory()->admin()->create();

    Livewire::actingAs($adminUser)
        ->test(EditUser::class, ['user' => $userToEdit])
        ->assertStatus(200)
        ->assertSet('email', $userToEdit->email)
        ->assertSet('name', $userToEdit->name)
        ->assertSet('level', $userToEdit->level->value);
});

it('requires email', function (): void {
    $userToEdit = User::factory()->create();
    $adminUser = User::factory()->admin()->create();

    Livewire::actingAs($adminUser)
        ->test(EditUser::class, ['user' => $userToEdit])
        ->set('email', '')
        ->call('save')
        ->assertHasErrors('email')
        ->assertNoRedirect();
});

it('requires name', function (): void {
    $userToEdit = User::factory()->create();
    $adminUser = User::factory()->admin()->create();

    Livewire::actingAs($adminUser)
        ->test(EditUser::class, ['user' => $userToEdit])
        ->set('name', '')
        ->call('save')
        ->assertHasErrors(['name'])
        ->assertNoRedirect();
});

it('requires level', function (): void {
    $userToEdit = User::factory()->create();
    $adminUser = User::factory()->admin()->create();

    Livewire::actingAs($adminUser)
        ->test(EditUser::class, ['user' => $userToEdit])
        ->set('level', '')
        ->call('save')
        ->assertHasErrors(['level'])
        ->assertNoRedirect();
});

it('validates email format', function (): void {
    $userToEdit = User::factory()->create();
    $adminUser = User::factory()->admin()->create();

    Livewire::actingAs($adminUser)
        ->test(EditUser::class, ['user' => $userToEdit])
        ->set('email', 'invalid-email')
        ->call('save')
        ->assertHasErrors(['email'])
        ->assertNoRedirect();
});

it('validates unique email (ignoring current user)', function (): void {
    $userToEdit = User::factory()->create();
    $existingUser = User::factory()->create();
    $adminUser = User::factory()->admin()->create();

    Livewire::actingAs($adminUser)
        ->test(EditUser::class, ['user' => $userToEdit])
        ->set('email', $existingUser->email)
        ->call('save')
        ->assertHasErrors(['email'])
        ->assertNoRedirect();

    Livewire::actingAs($adminUser)
        ->test(EditUser::class, ['user' => $userToEdit])
        ->set('email', $userToEdit->email) // Should pass as it's the same user
        ->call('save')
        ->assertHasNoErrors(['email'])
        ->assertRedirect();
});

it('validates user level enum', function (): void {
    $userToEdit = User::factory()->create();
    $adminUser = User::factory()->admin()->create();

    Livewire::actingAs($adminUser)
        ->test(EditUser::class, ['user' => $userToEdit])
        ->set('level', 'invalid-level')
        ->call('save')
        ->assertHasErrors(['level'])
        ->assertNoRedirect();
});

it('can update a user', function (): void {
    $userToEdit = User::factory()->create();
    $adminUser = User::factory()->admin()->create();

    $newName = 'Updated Name';
    $newEmail = 'updated@example.com';
    $newLevel = UserLevel::Admin->value;

    Livewire::actingAs($adminUser)
        ->test(EditUser::class, ['user' => $userToEdit])
        ->set('name', $newName)
        ->set('email', $newEmail)
        ->set('level', $newLevel)
        ->call('save')
        ->assertDispatched('toast-show')
        ->assertDispatched('modal-close')
        ->assertRedirect(route('admin.users'));

    $userToEdit->refresh();

    expect($userToEdit->name)->toBe($newName)
        ->and($userToEdit->email)->toBe($newEmail)
        ->and($userToEdit->level->value)->toBe($newLevel);
});

it('cannot update a user if not an admin', function (): void {
    $userToEdit = User::factory()->create();
    $nonAdminUser = User::factory()->create();

    Livewire::actingAs($nonAdminUser)
        ->test(EditUser::class, ['user' => $userToEdit])
        ->call('save')
        ->assertForbidden();
});

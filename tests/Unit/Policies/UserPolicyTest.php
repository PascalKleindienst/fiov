<?php

declare(strict_types=1);

namespace Tests\Unit\Policies;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Laravel\actingAs;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->user = User::factory()->create();
    actingAs($this->user);
});

it('bypasses everything for admins', function (string $permission): void {
    actingAs($admin = User::factory()->admin()->create());
    expect($admin->can($permission, User::class))->toBeTrue();
})->with([
    'viewAny', 'view', 'create', 'update', 'delete', 'restore', 'forceDelete',
]);

it('denies view any only for users', function (): void {
    expect($this->user->can('viewAny', User::class))->toBeFalse();
});

it('allows view when user is itself', function (): void {
    expect($this->user->can('view', $this->user))->toBeTrue();
});

it('denies view when user is not itself', function (): void {
    expect($this->user->can('view', User::factory()->create()))->toBeFalse();
});

it('denies create', function (): void {
    expect($this->user->can('create', User::class))->toBeFalse();
});

it('allows update when user is itself', function (): void {
    expect($this->user->can('update', $this->user))->toBeTrue();
});

it('denies update when user does not own budget', function (): void {
    expect($this->user->can('update', User::factory()->create()))->toBeFalse();
});

it('allows delete when user is itself', function (): void {
    expect($this->user->can('delete', $this->user))->toBeTrue();
});

it('denies delete when user does not own budget', function (): void {
    expect($this->user->can('delete', User::factory()->create()))->toBeFalse();
});

it('allows restore when user is itself', function (): void {
    expect($this->user->can('restore', $this->user))->toBeTrue();
});

it('denies restore when user does not own budget', function (): void {
    expect($this->user->can('restore', User::factory()->create()))->toBeFalse();
});

it('allows force delete when user is itself', function (): void {
    expect($this->user->can('forceDelete', $this->user))->toBeTrue();
});

it('denies force delete when user does not own budget', function (): void {
    expect($this->user->can('forceDelete', User::factory()->create()))->toBeFalse();
});

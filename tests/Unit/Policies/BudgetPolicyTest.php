<?php

declare(strict_types=1);

namespace Tests\Unit\Policies;

use App\Models\Budget;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use function Pest\Laravel\actingAs;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->user = User::factory()->create();
    $this->budget = Budget::factory()->for($this->user)->create();
    actingAs($this->user);
});

it('allows view any', function (): void {
    expect($this->user->can('viewAny', Budget::class))->toBeTrue();
});

it('allows view when user owns budget', function (): void {
    expect($this->user->can('view', $this->budget))->toBeTrue();
});

it('denies view when user does not own budget', function (): void {
    expect($this->user->can('view', Budget::factory()->create()))->toBeFalse();
});

it('allows create', function (): void {
    expect($this->user->can('create', Budget::class))->toBeTrue();
});

it('allows update when user owns budget', function (): void {
    expect($this->user->can('update', $this->budget))->toBeTrue();
});

it('denies update when user does not own budget', function (): void {
    expect($this->user->can('update', Budget::factory()->create()))->toBeFalse();
});

it('allows delete when user owns budget', function (): void {
    expect($this->user->can('delete', $this->budget))->toBeTrue();
});

it('denies delete when user does not own budget', function (): void {
    expect($this->user->can('delete', Budget::factory()->create()))->toBeFalse();
});

it('allows restore when user owns budget', function (): void {
    expect($this->user->can('restore', $this->budget))->toBeTrue();
});

it('denies restore when user does not own budget', function (): void {
    expect($this->user->can('restore', Budget::factory()->create()))->toBeFalse();
});

it('allows force delete when user owns budget', function (): void {
    expect($this->user->can('forceDelete', $this->budget))->toBeTrue();
});

it('denies force delete when user does not own budget', function (): void {
    expect($this->user->can('forceDelete', Budget::factory()->create()))->toBeFalse();
});

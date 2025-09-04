<?php

declare(strict_types=1);

namespace Tests\Unit\Policies;

use App\Models\RecurringTransaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use function Pest\Laravel\actingAs;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->user = User::factory()->create();
    $this->recurringTransaction = RecurringTransaction::factory()->for($this->user)->create();
    actingAs($this->user);
});

it('allows view any', function (): void {
    expect($this->user->can('viewAny', RecurringTransaction::class))->toBeTrue();
});

it('allows view when user owns recurring transaction', function (): void {
    expect($this->user->can('view', $this->recurringTransaction))->toBeTrue();
});

it('denies view when user does not own recurring transaction', function (): void {
    $recurringTransaction = RecurringTransaction::factory()->for(User::factory()->create())->create();

    expect($this->user->can('view', $recurringTransaction))->toBeFalse();
});

it('allows create', function (): void {
    expect($this->user->can('create', RecurringTransaction::class))->toBeTrue();
});

it('allows update when user owns recurring transaction', function (): void {
    expect($this->user->can('update', $this->recurringTransaction))->toBeTrue();
});

it('denies update when user does not own recurring transaction', function (): void {
    $recurringTransaction = RecurringTransaction::factory()->for(User::factory()->create())->create();

    expect($this->user->can('update', $recurringTransaction))->toBeFalse();
});

it('allows delete when user owns recurring transaction', function (): void {
    expect($this->user->can('delete', $this->recurringTransaction))->toBeTrue();
});

it('denies delete when user does not own recurring transaction', function (): void {
    $recurringTransaction = RecurringTransaction::factory()->for(User::factory()->create())->create();

    expect($this->user->can('delete', $recurringTransaction))->toBeFalse();
});

it('allows restore when user owns recurring transaction', function (): void {
    expect($this->user->can('restore', $this->recurringTransaction))->toBeTrue();
});

it('denies restore when user does not own recurring transaction', function (): void {
    $recurringTransaction = RecurringTransaction::factory()->for(User::factory()->create())->create();

    expect($this->user->can('restore', $recurringTransaction))->toBeFalse();
});

it('allows force delete when user owns recurring transaction', function (): void {
    expect($this->user->can('forceDelete', $this->recurringTransaction))->toBeTrue();
});

it('denies force delete when user does not own recurring transaction', function (): void {
    $recurringTransaction = RecurringTransaction::factory()->for(User::factory()->create())->create();

    expect($this->user->can('forceDelete', $recurringTransaction))->toBeFalse();
});

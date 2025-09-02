<?php

declare(strict_types=1);

namespace Tests\Unit\Policies;

use App\Models\RecurringTransaction;
use App\Models\User;
use App\Policies\RecurringTransactionPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->policy = new RecurringTransactionPolicy();
});

it('allows view any', function (): void {
    expect($this->policy->viewAny())->toBeTrue();
});

it('allows view when user owns recurring transaction', function (): void {
    $user = User::factory()->create();
    $recurringTransaction = RecurringTransaction::factory()->for($user)->create();

    expect($this->policy->view($user, $recurringTransaction))->toBeTrue();
});

it('denies view when user does not own recurring transaction', function (): void {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $recurringTransaction = RecurringTransaction::factory()->for($otherUser)->create();

    expect($this->policy->view($user, $recurringTransaction))->toBeFalse();
});

it('allows create', function (): void {
    expect($this->policy->create())->toBeTrue();
});

it('allows update when user owns recurring transaction', function (): void {
    $user = User::factory()->create();
    $recurringTransaction = RecurringTransaction::factory()->for($user)->create();

    expect($this->policy->update($user, $recurringTransaction))->toBeTrue();
});

it('denies update when user does not own recurring transaction', function (): void {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $recurringTransaction = RecurringTransaction::factory()->for($otherUser)->create();

    expect($this->policy->update($user, $recurringTransaction))->toBeFalse();
});

it('allows delete when user owns recurring transaction', function (): void {
    $user = User::factory()->create();
    $recurringTransaction = RecurringTransaction::factory()->for($user)->create();

    expect($this->policy->delete($user, $recurringTransaction))->toBeTrue();
});

it('denies delete when user does not own recurring transaction', function (): void {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $recurringTransaction = RecurringTransaction::factory()->for($otherUser)->create();

    expect($this->policy->delete($user, $recurringTransaction))->toBeFalse();
});

it('allows restore when user owns recurring transaction', function (): void {
    $user = User::factory()->create();
    $recurringTransaction = RecurringTransaction::factory()->for($user)->create();

    expect($this->policy->restore($user, $recurringTransaction))->toBeTrue();
});

it('denies restore when user does not own recurring transaction', function (): void {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $recurringTransaction = RecurringTransaction::factory()->for($otherUser)->create();

    expect($this->policy->restore($user, $recurringTransaction))->toBeFalse();
});

it('allows force delete when user owns recurring transaction', function (): void {
    $user = User::factory()->create();
    $recurringTransaction = RecurringTransaction::factory()->for($user)->create();

    expect($this->policy->forceDelete($user, $recurringTransaction))->toBeTrue();
});

it('denies force delete when user does not own recurring transaction', function (): void {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $recurringTransaction = RecurringTransaction::factory()->for($otherUser)->create();

    expect($this->policy->forceDelete($user, $recurringTransaction))->toBeFalse();
});

<?php

declare(strict_types=1);

namespace Tests\Unit\Policies;

use App\Models\Budget;
use App\Models\User;
use App\Policies\BudgetPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->policy = new BudgetPolicy();
});

it('allows view any', function (): void {
    expect($this->policy->viewAny())->toBeTrue();
});

it('allows view when user owns budget', function (): void {
    $user = User::factory()->create();
    $budget = Budget::factory()->for($user)->create();

    expect($this->policy->view($user, $budget))->toBeTrue();
});

it('denies view when user does not own budget', function (): void {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $budget = Budget::factory()->for($otherUser)->create();

    expect($this->policy->view($user, $budget))->toBeFalse();
});

it('allows create', function (): void {
    expect($this->policy->create())->toBeTrue();
});

it('allows update when user owns budget', function (): void {
    $user = User::factory()->create();
    $budget = Budget::factory()->for($user)->create();

    expect($this->policy->update($user, $budget))->toBeTrue();
});

it('denies update when user does not own budget', function (): void {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $budget = Budget::factory()->for($otherUser)->create();

    expect($this->policy->update($user, $budget))->toBeFalse();
});

it('allows delete when user owns budget', function (): void {
    $user = User::factory()->create();
    $budget = Budget::factory()->for($user)->create();

    expect($this->policy->delete($user, $budget))->toBeTrue();
});

it('denies delete when user does not own budget', function (): void {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $budget = Budget::factory()->for($otherUser)->create();

    expect($this->policy->delete($user, $budget))->toBeFalse();
});

it('allows restore when user owns budget', function (): void {
    $user = User::factory()->create();
    $budget = Budget::factory()->for($user)->create();

    expect($this->policy->restore($user, $budget))->toBeTrue();
});

it('denies restore when user does not own budget', function (): void {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $budget = Budget::factory()->for($otherUser)->create();

    expect($this->policy->restore($user, $budget))->toBeFalse();
});

it('allows force delete when user owns budget', function (): void {
    $user = User::factory()->create();
    $budget = Budget::factory()->for($user)->create();

    expect($this->policy->forceDelete($user, $budget))->toBeTrue();
});

it('denies force delete when user does not own budget', function (): void {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $budget = Budget::factory()->for($otherUser)->create();

    expect($this->policy->forceDelete($user, $budget))->toBeFalse();
});

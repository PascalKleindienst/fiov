<?php

declare(strict_types=1);

namespace Tests\Unit\Policies;

use App\Models\User;
use App\Models\WalletCategory;
use App\Policies\WalletCategoryPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->policy = new WalletCategoryPolicy();
});

it('allows view any', function (): void {
    expect($this->policy->viewAny())->toBeTrue();
});

it('allows create', function (): void {
    expect($this->policy->create())->toBeTrue();
});

it('allows update when user owns wallet category', function (): void {
    $user = User::factory()->create();
    $walletCategory = WalletCategory::factory()->for($user)->create();

    expect($this->policy->update($user, $walletCategory))->toBeTrue();
});

it('denies update when user does not own wallet category', function (): void {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $walletCategory = WalletCategory::factory()->for($otherUser)->create();

    expect($this->policy->update($user, $walletCategory))->toBeFalse();
});

it('allows delete when user owns wallet category', function (): void {
    $user = User::factory()->create();
    $walletCategory = WalletCategory::factory()->for($user)->create();

    expect($this->policy->delete($user, $walletCategory))->toBeTrue();
});

it('denies delete when user does not own wallet category', function (): void {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $walletCategory = WalletCategory::factory()->for($otherUser)->create();

    expect($this->policy->delete($user, $walletCategory))->toBeFalse();
});

<?php

declare(strict_types=1);

namespace Tests\Unit\Policies;

use App\Models\User;
use App\Models\Wallet;
use App\Policies\WalletPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->policy = new WalletPolicy();
});

it('allows view any', function (): void {
    expect($this->policy->viewAny())->toBeTrue();
});

it('allows create', function (): void {
    expect($this->policy->create())->toBeTrue();
});

it('allows update when user owns wallet', function (): void {
    $user = User::factory()->create();
    $wallet = Wallet::factory()->for($user)->create();

    expect($this->policy->update($user, $wallet))->toBeTrue();
});

it('denies update when user does not own wallet', function (): void {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $wallet = Wallet::factory()->for($otherUser)->create();

    expect($this->policy->update($user, $wallet))->toBeFalse();
});

it('allows delete when user owns wallet', function (): void {
    $user = User::factory()->create();
    $wallet = Wallet::factory()->for($user)->create();

    expect($this->policy->delete($user, $wallet))->toBeTrue();
});

it('denies delete when user does not own wallet', function (): void {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $wallet = Wallet::factory()->for($otherUser)->create();

    expect($this->policy->delete($user, $wallet))->toBeFalse();
});

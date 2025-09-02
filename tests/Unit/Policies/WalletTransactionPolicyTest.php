<?php

declare(strict_types=1);

namespace Tests\Unit\Policies;

use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use App\Policies\WalletTransactionPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->policy = new WalletTransactionPolicy();
});

it('allows view any', function (): void {
    expect($this->policy->viewAny())->toBeTrue();
});

it('allows create', function (): void {
    expect($this->policy->create())->toBeTrue();
});

it('allows update when user owns wallet transaction', function (): void {
    $user = User::factory()->create();
    $wallet = Wallet::factory()->for($user)->create();
    $walletTransaction = WalletTransaction::factory()->for($wallet)->create();

    expect($this->policy->update($user, $walletTransaction))->toBeTrue();
});

it('denies update when user does not own wallet transaction', function (): void {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $otherWallet = Wallet::factory()->for($otherUser)->create();
    $walletTransaction = WalletTransaction::factory()->for($otherWallet)->create();

    expect($this->policy->update($user, $walletTransaction))->toBeFalse();
});

it('allows delete when user owns wallet transaction', function (): void {
    $user = User::factory()->create();
    $wallet = Wallet::factory()->for($user)->create();
    $walletTransaction = WalletTransaction::factory()->for($wallet)->create();

    expect($this->policy->delete($user, $walletTransaction))->toBeTrue();
});

it('denies delete when user does not own wallet transaction', function (): void {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $otherWallet = Wallet::factory()->for($otherUser)->create();
    $walletTransaction = WalletTransaction::factory()->for($otherWallet)->create();

    expect($this->policy->delete($user, $walletTransaction))->toBeFalse();
});

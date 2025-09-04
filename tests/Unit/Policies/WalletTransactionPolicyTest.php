<?php

declare(strict_types=1);

namespace Tests\Unit\Policies;

use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use function Pest\Laravel\actingAs;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->user = User::factory()->create();
    $this->wallet = Wallet::factory()->for($this->user)->create();
    $this->transaction = WalletTransaction::factory()->for($this->wallet)->create();
    actingAs($this->user);
});

it('allows view any', function (): void {
    expect($this->user->can('viewAny', WalletTransaction::class))->toBeTrue();
});

it('allows view when user owns wallet transaction', function (): void {
    expect($this->user->can('view', $this->transaction))->toBeTrue();
});

it('denies view when user does not own wallet transaction', function (): void {
    $user = User::factory()->create();
    $wallet = Wallet::factory()->for($user)->create();
    $walletTransaction = WalletTransaction::factory()->for($wallet, 'wallet')->create();

    expect($this->user->can('view', $walletTransaction))->toBeFalse();
});

it('allows create', function (): void {
    expect($this->user->can('create', WalletTransaction::class))->toBeTrue();
});

it('allows update when user owns wallet transaction', function (): void {
    expect($this->user->can('update', $this->transaction))->toBeTrue();
});

it('denies update when user does not own wallet transaction', function (): void {
    $user = User::factory()->create();
    $wallet = Wallet::factory()->for($user)->create();
    $walletTransaction = WalletTransaction::factory()->for($wallet, 'wallet')->create();

    expect($this->user->can('update', $walletTransaction))->toBeFalse();
});

it('allows delete when user owns wallet transaction', function (): void {
    expect($this->user->can('delete', $this->transaction))->toBeTrue();
});

it('denies delete when user does not own wallet transaction', function (): void {
    $user = User::factory()->create();
    $wallet = Wallet::factory()->for($user)->create();
    $walletTransaction = WalletTransaction::factory()->for($wallet, 'wallet')->create();

    expect($this->user->can('delete', $walletTransaction))->toBeFalse();
});

it('allows restore when user owns wallet transaction', function (): void {
    expect($this->user->can('restore', $this->transaction))->toBeTrue();
});

it('denies restore when user does not own wallet transaction', function (): void {
    $user = User::factory()->create();
    $wallet = Wallet::factory()->for($user)->create();
    $walletTransaction = WalletTransaction::factory()->for($wallet, 'wallet')->create();

    expect($this->user->can('restore', $walletTransaction))->toBeFalse();
});

it('allows force delete when user owns wallet transaction', function (): void {
    expect($this->user->can('forceDelete', $this->transaction))->toBeTrue();
});

it('denies force delete when user does not own wallet transaction', function (): void {
    $user = User::factory()->create();
    $wallet = Wallet::factory()->for($user)->create();
    $walletTransaction = WalletTransaction::factory()->for($wallet, 'wallet')->create();

    expect($this->user->can('forceDelete', $walletTransaction))->toBeFalse();
});


<?php

declare(strict_types=1);

namespace Tests\Unit\Policies;

use App\Models\User;
use App\Models\Wallet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use function Pest\Laravel\actingAs;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->user = User::factory()->create();
    $this->wallet = Wallet::factory()->for($this->user)->create();
    actingAs($this->user);
});

it('allows view any', function (): void {
    expect($this->user->can('viewAny', Wallet::class))->toBeTrue();
});

it('allows create', function (): void {
    expect($this->user->can('create', Wallet::class))->toBeTrue();
});

it('allows update when user owns wallet', function (): void {
    expect($this->user->can('update', $this->wallet))->toBeTrue();
});

it('denies update when user does not own wallet', function (): void {
    expect($this->user->can('update', Wallet::factory()->create()))->toBeFalse();
});

it('allows delete when user owns wallet', function (): void {
    expect($this->user->can('delete', $this->wallet))->toBeTrue();
});

it('denies delete when user does not own wallet', function (): void {
    expect($this->user->can('delete', Wallet::factory()->create()))->toBeFalse();
});

it('allows restore when user owns wallet', function (): void {
    expect($this->user->can('restore', $this->wallet))->toBeTrue();
});

it('denies restore when user does not own wallet', function (): void {
    expect($this->user->can('restore', Wallet::factory()->create()))->toBeFalse();
});

it('allows force delete when user owns wallet', function (): void {
    expect($this->user->can('forceDelete', $this->wallet))->toBeTrue();
});

it('denies force delete when user does not own wallet', function (): void {
    expect($this->user->can('forceDelete', Wallet::factory()->create()))->toBeFalse();
});

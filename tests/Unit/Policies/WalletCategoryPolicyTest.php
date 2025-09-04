<?php

declare(strict_types=1);

namespace Tests\Unit\Policies;

use App\Models\User;
use App\Models\WalletCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use function Pest\Laravel\actingAs;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->user = User::factory()->create();
    $this->category = WalletCategory::factory()->for($this->user)->create();
    actingAs($this->user);
});

it('allows view any', function (): void {
    expect($this->user->can('viewAny', WalletCategory::class))->toBeTrue();
});

it('allows create', function (): void {
    expect($this->user->can('create', WalletCategory::class))->toBeTrue();
});

it('allows update when user owns wallet category', function (): void {
    expect($this->user->can('update', $this->category))->toBeTrue();
});

it('denies update when user does not own wallet category', function (): void {
    $walletCategory = WalletCategory::factory()->for(User::factory()->create())->create();

    expect($this->user->can('update', $walletCategory))->toBeFalse();
});

it('allows delete when user owns wallet category', function (): void {
    expect($this->user->can('delete', $this->category))->toBeTrue();
});

it('denies delete when user does not own wallet category', function (): void {
    $walletCategory = WalletCategory::factory()->for(User::factory()->create())->create();

    expect($this->user->can('delete', $walletCategory))->toBeFalse();
});

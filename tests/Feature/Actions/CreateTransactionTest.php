<?php

declare(strict_types=1);

use App\Actions\CreateTransaction;
use App\Models\RecurringTransaction;
use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletCategory;
use App\Models\WalletTransaction;

use function Pest\Laravel\actingAs;

it('creates a wallet transaction', function (): void {
    // Arrange
    $user = User::factory()->create();
    $wallet = Wallet::factory()->for($user, 'user')->create();
    $category = WalletCategory::factory()->for($user, 'user')->create();

    actingAs($user);

    // Act
    resolve(CreateTransaction::class)->handle([
        'wallet_id' => $wallet->id,
        'wallet_category_id' => $category->id,
        'title' => 'Test Transaction',
        'icon' => null,
        'amount' => 100,
        'currency' => 'USD',
        'is_investment' => false,
    ]);

    // Assert
    $database = WalletTransaction::latest()->firstOrFail();
    expect($database->title)->toEqual('Test Transaction')
        ->and($database->amount->getAmount())->toEqual(100)
        ->and($database->currency)->toEqual('USD')
        ->and($database->is_investment)->toBeFalse()
        ->and($database->icon)->toBeNull()
        ->and($database->wallet_id)->toEqual($wallet->id)
        ->and($database->wallet_category_id)->toEqual($category->id);
});

it('creates a recurring wallet transaction', closure: function (): void {
    // Arrange
    $user = User::factory()->create();
    $wallet = Wallet::factory()->for($user, 'user')->create();
    $category = WalletCategory::factory()->for($user, 'user')->create();

    actingAs($user);

    // Act
    resolve(CreateTransaction::class)->handle([
        'wallet_id' => $wallet->id,
        'wallet_category_id' => $category->id,
        'title' => 'Test Transaction',
        'icon' => null,
        'amount' => 100,
        'currency' => 'USD',
        'is_investment' => false,
    ], [
        'is_recurring' => true,
        'recurring_frequency' => \App\Enums\RecurringFrequency::DAILY->value,
        'recurring_end_date' => '2023-12-31',
    ]);

    // Assert
    $database = WalletTransaction::latest()->firstOrFail();
    $recurring = RecurringTransaction::latest()->firstOrFail();
    expect($database->title)->toEqual('Test Transaction')
        ->and($database->amount->getAmount())->toEqual(100)
        ->and($database->currency)->toEqual('USD')
        ->and($database->is_investment)->toBeFalse()
        ->and($database->icon)->toBeNull()
        ->and($database->wallet_id)->toEqual($wallet->id)
        ->and($database->wallet_category_id)->toEqual($category->id)
        ->and($recurring->title)->toEqual('Test Transaction')
        ->and($recurring->amount->getAmount())->toEqual(100)
        ->and($recurring->currency)->toEqual('USD')
        ->and($recurring->is_investment)->toBeFalse()
        ->and($recurring->icon)->toBeNull()
        ->and($recurring->wallet_id)->toEqual($wallet->id)
        ->and($recurring->wallet_category_id)->toEqual($category->id)
        ->and($recurring->frequency)->toEqual(\App\Enums\RecurringFrequency::DAILY)
        ->and($recurring->end_date?->toDateString())->toEqual('2023-12-31');
});

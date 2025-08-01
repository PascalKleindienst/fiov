<?php

declare(strict_types=1);

use App\Actions\CreateRecurringTransaction;
use App\Models\RecurringTransaction;
use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletTransaction;

use function Pest\Laravel\actingAs;

it('creates a wallet transaction and updates last_processed_at', function (): void {
    // Arrange
    $user = User::factory()->create();
    $wallet = Wallet::factory()->for($user, 'user')->create();

    actingAs($user);

    $recurring = RecurringTransaction::factory()->for($wallet, 'wallet')->for($user, 'user')->create([
        'last_processed_at' => null,
    ]);

    expect(WalletTransaction::count())->toBe(0)
        ->and($recurring->last_processed_at)->toBeNull();

    // Act
    resolve(CreateRecurringTransaction::class)->handle($recurring);

    $recurring->refresh();

    // Assert
    $database = WalletTransaction::latest()->firstOrFail();
    expect($database->title)->toEqual($recurring->title)
        ->and($database->amount)->toEqual($recurring->amount)
        ->and($database->currency)->toEqual($recurring->currency)
        ->and($database->is_investment)->toEqual($recurring->is_investment)
        ->and($database->icon)->toEqual($recurring->icon)
        ->and($database->wallet_id)->toEqual($recurring->wallet_id)
        ->and($database->wallet_category_id)->toEqual($recurring->wallet_category_id)
        ->and($recurring->last_processed_at)->not()->toBeNull();
});

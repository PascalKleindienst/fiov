<?php

declare(strict_types=1);

use App\Models\RecurringTransaction;
use App\Models\User;
use App\Models\WalletTransaction;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\artisan;

beforeEach(function () {
    $this->seed(\Database\Seeders\DemoDataSeeder::class);
    WalletTransaction::query()->delete();
    $this->user = User::first();
    actingAs($this->user);
});

it('processes eligible recurring transactions', function () {
    // Arrange
    $recurring = RecurringTransaction::factory()->for($this->user, 'user')->create([
        'is_active' => true,
        'start_date' => now()->subDay(),
        'end_date' => null,
        'last_processed_at' => null,
    ]);

    expect(WalletTransaction::count())->toBe(0);

    // Act
    artisan('transactions:process:recurring')->assertExitCode(0);

    // Assert
    $recurring->refresh();
    $database = WalletTransaction::latest()->firstOrFail();
    expect(WalletTransaction::count())->toBe(1)
        ->and($database->title)->toEqual($recurring->title)
        ->and($database->amount)->toEqual($recurring->amount)
        ->and($database->currency)->toEqual($recurring->currency)
        ->and($database->wallet_id)->toEqual($recurring->wallet_id)
        ->and($database->wallet_category_id)->toEqual($recurring->wallet_category_id)
        ->and($recurring->last_processed_at)->not()->toBeNull();
});

it('does not process inactive recurring transactions', function () {
    // Arrange
    $recurring = RecurringTransaction::factory()->for($this->user, 'user')->create([
        'is_active' => false,
        'start_date' => now()->subDay(),
        'end_date' => null,
        'last_processed_at' => null,
    ]);

    // Act
    artisan('transactions:process:recurring')->assertExitCode(0);

    // Assert
    $recurring->refresh();
    expect($recurring->last_processed_at)->toBeNull()
        ->and(WalletTransaction::count())->toBe(0);
});

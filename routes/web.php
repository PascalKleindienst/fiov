<?php

declare(strict_types=1);

use App\Livewire\Budgets\BudgetList;
use App\Livewire\Budgets\CreateOrEditBudget;
use App\Livewire\Categories\Create;
use App\Livewire\Categories\Edit;
use App\Livewire\RecurringTransactions\Index;
use App\Livewire\Settings\Appearance;
use App\Livewire\Settings\Password;
use App\Livewire\Settings\Profile;
use App\Models\Wallet;
use App\Models\WalletCategory;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => view('welcome'))->name('home');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function (): void {
    Route::redirect('settings', 'settings/profile');

    Route::name('wallets.')->prefix('wallets')->group(function (): void {
        Route::get('/', \App\Livewire\Wallets\Index::class)->name('index')->can('viewAny', Wallet::class);
        Route::get('/create', \App\Livewire\Wallets\Create::class)->name('create')->can('create', Wallet::class);
        Route::get('/{wallet}/edit', \App\Livewire\Wallets\Edit::class)->name('edit')->can('update', 'wallet');
    });

    Route::name('categories.')->prefix('categories')->group(function (): void {
        Route::get('/', \App\Livewire\Categories\Index::class)->name('index')->can('viewAny', WalletCategory::class);
        Route::get('/create', Create::class)->name('create')->can('create', WalletCategory::class);
        Route::get('/{walletCategory}/edit', Edit::class)->name('edit')
            ->can('update', 'walletCategory');
    });

    Route::name('recurring-transactions.')->prefix('recurring-transactions')->group(function (): void {
        Route::get('/', Index::class)
            ->name('index');
    });

    Route::get('settings/profile', Profile::class)->name('settings.profile');
    Route::get('settings/password', Password::class)->name('settings.password');
    Route::get('settings/appearance', Appearance::class)->name('settings.appearance');

    // Budgets
    Route::name('budgets.')->prefix('budgets')->group(function (): void {
        Route::get('/', BudgetList::class)
            ->name('index')
            ->can('viewAny', \App\Models\Budget::class);

        Route::get('/create', CreateOrEditBudget::class)
            ->name('create')
            ->can('create', \App\Models\Budget::class);

        Route::get('/{budget}/edit', CreateOrEditBudget::class)
            ->name('edit')
            ->can('update', 'budget');
    });

    // Admin Stuff
    Route::name('admin.')
        ->prefix('admin')
        ->middleware('can:viewAdmin')
        ->group(function (): void {
            Route::get('/system', \App\Livewire\Admin\System::class)->name('system');
        });
});

require __DIR__.'/auth.php';

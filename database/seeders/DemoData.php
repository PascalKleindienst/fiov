<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletCategory;
use Illuminate\Support\Collection;

final readonly class DemoData
{
    public User $user;

    public Wallet $wallet;

    /** @var Collection<string, WalletCategory> */
    public Collection $categories;

    public function __construct(?User $user, ?Wallet $wallet)
    {
        $this->user = $user ?? User::factory()->create();
        $this->wallet = $wallet ?? Wallet::factory()->for($this->user)->create();

        $categories = WalletCategory::query()->get()->mapWithKeys(fn (WalletCategory $category) => [$category->title => $category]);

        $this->categories = collect([
            'financial' => $categories->get('Financial') ?? WalletCategory::factory()->for($this->user)->create(['title' => 'Financial']),
            'gaming' => $categories->get('Gaming') ?? WalletCategory::factory()->for($this->user)->create(['title' => 'Gaming']),
            'entertainment' => $categories->get('Entertainment') ?? WalletCategory::factory()->for($this->user)->create(['title' => 'Entertainment']),
            'household' => $categories->get('Household') ?? WalletCategory::factory()->for($this->user)->create(['title' => 'Household']),
            'groceries' => $categories->get('Groceries') ?? WalletCategory::factory()->for($this->user)->create(['title' => 'Groceries']),
            'travel' => $categories->get('Travel') ?? WalletCategory::factory()->for($this->user)->create(['title' => 'Travel']),
        ]);
    }
}

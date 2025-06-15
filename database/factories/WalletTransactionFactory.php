<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Wallet;
use App\Models\WalletCategory;
use App\Models\WalletTransaction;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * @extends Factory<WalletTransaction>
 */
final class WalletTransactionFactory extends Factory
{
    protected $model = WalletTransaction::class;

    public function definition(): array
    {
        return [
            'title' => $this->faker->word(),
            'icon' => $this->faker->randomElement(['star', 'cog']),
            'amount' => $this->faker->randomNumber(4),
            'currency' => null,
            'is_investment' => $this->faker->boolean(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),

            'wallet_id' => Wallet::factory(),
            'wallet_category_id' => WalletCategory::factory(),
        ];
    }
}

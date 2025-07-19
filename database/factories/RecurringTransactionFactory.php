<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\Icon;
use App\Enums\RecurringFrequency;
use App\Models\RecurringTransaction;
use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletCategory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * @extends Factory<RecurringTransaction>
 */
final class RecurringTransactionFactory extends Factory
{
    protected $model = RecurringTransaction::class;

    public function definition(): array
    {
        return [
            'title' => $this->faker->word(),
            'icon' => $this->faker->randomElement(Icon::values()),
            'amount' => $this->faker->randomNumber(4),
            'currency' => $this->faker->currencyCode(),
            'is_investment' => $this->faker->boolean(),
            'frequency' => $this->faker->randomElement(RecurringFrequency::values()),
            'start_date' => Carbon::now(),
            'end_date' => Carbon::now(),
            'last_processed_at' => Carbon::now(),
            'is_active' => $this->faker->boolean(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),

            'user_id' => User::factory(),
            'wallet_id' => Wallet::factory(),
            'wallet_category_id' => WalletCategory::factory(),
        ];
    }
}

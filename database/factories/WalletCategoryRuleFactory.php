<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\RuleOperator;
use App\Models\WalletCategory;
use App\Models\WalletCategoryRule;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * @extends Factory<WalletCategoryRule>
 */
final class WalletCategoryRuleFactory extends Factory
{
    protected $model = WalletCategoryRule::class;

    public function definition(): array
    {
        return [
            'field' => $this->faker->randomElement(['title', 'icon', 'amount']),
            'operator' => $this->faker->randomElement(RuleOperator::values()),
            'value' => $this->faker->word(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),

            'wallet_category_id' => WalletCategory::factory(),
        ];
    }
}

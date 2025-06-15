<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\User;
use App\Models\WalletCategory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * @extends Factory<WalletCategory>
 */
final class WalletCategoryFactory extends Factory
{
    protected $model = WalletCategory::class;

    public function definition(): array
    {
        return [
            'title' => $this->faker->word(),
            'color' => $this->faker->randomElement(['red', 'green', 'blue', 'yellow', 'orange', 'purple']),
            'icon' => $this->faker->randomElement(['star', 'cog']),
            'user_id' => User::factory(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}

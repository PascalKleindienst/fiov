<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\Color;
use App\Enums\Currency;
use App\Enums\Icon;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * @extends Factory<Wallet>
 */
final class WalletFactory extends Factory
{
    protected $model = Wallet::class;

    public function definition(): array
    {
        return [
            'title' => $this->faker->word(),
            'description' => $this->faker->text(),
            'color' => $this->faker->randomElement(Color::cases()),
            'icon' => $this->faker->randomElement(Icon::cases()),
            'currency' => $this->faker->randomElement(Currency::cases()),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),

            'user_id' => User::factory(),
        ];
    }

    public function archived(): self
    {
        return $this->state(fn (array $attributes): array => [
            'deleted_at' => now(),
        ]);
    }
}

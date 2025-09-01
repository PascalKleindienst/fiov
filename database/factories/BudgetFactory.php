<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\BudgetStatus;
use App\Enums\BudgetType;
use App\Enums\Priority;
use App\Models\Budget;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * @extends Factory<Budget>
 */
final class BudgetFactory extends Factory
{
    protected $model = Budget::class;

    public function definition(): array
    {
        $type = $this->faker->randomElement(BudgetType::cases());

        $startDate = $this->faker->dateTimeBetween('-3 month', 'now');
        $endDate = match ($type) {
            BudgetType::Weekly => Carbon::parse($startDate)->addWeek(),
            BudgetType::Monthly => Carbon::parse($startDate)->addMonth(),
            BudgetType::Yearly => Carbon::parse($startDate)->addYear(),
            default => Carbon::parse($startDate)->addMonths(3),
        };

        return [
            'title' => $this->faker->name(),
            'description' => $this->faker->text(),
            'milestones' => [
                [
                    'amount' => random_int(100, 1000),
                    'description' => 'First milestone',
                    'achieved' => $this->faker->boolean(),
                ],
            ],
            'type' => $type,
            'priority' => $this->faker->randomElement(Priority::cases()),
            'status' => $this->faker->randomElement(BudgetStatus::cases())->value,

            'start_date' => $startDate,
            'end_date' => $endDate,

            'user_id' => User::factory(),
            'wallet_id' => Wallet::factory(),
        ];
    }

    public function goalBased(): BudgetFactory
    {
        return $this->state(fn (array $attributes): array => [
            'type' => $this->faker->randomElement(BudgetType::goalBasedStates()),
        ]);
    }
}

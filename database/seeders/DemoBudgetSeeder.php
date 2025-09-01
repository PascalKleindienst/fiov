<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\BudgetType;
use App\Models\Budget;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Seeder;

final class DemoBudgetSeeder extends Seeder
{
    public static string $currency = 'EUR';

    public function run(DemoData $demo): void
    {
        $this->createGroceriesBudget($demo);
        $this->createVacationBudget($demo);
        $this->createStudentLoanBudget($demo);
        $this->createEntertainmentBudget($demo);
    }

    private function createGroceriesBudget(DemoData $demo): void
    {
        // @phpstan-ignore-next-line
        $this->factory($demo)
            ->create([
                'title' => 'Groceries',
                'type' => BudgetType::Monthly,
            ])
            ->categories()->sync([
                [
                    'wallet_category_id' => $demo->categories->get('groceries')?->id,
                    'allocated_amount' => 30000,
                    'used_amount' => 10000,
                    'currency' => self::$currency,
                ],
            ]);
    }

    /**
     * @return Factory<Budget>
     */
    private function factory(DemoData $demo): Factory
    {
        return Budget::factory()->for($demo->user, 'user')->for($demo->wallet, 'wallet');
    }

    private function createVacationBudget(DemoData $demo): void
    {
        // @phpstan-ignore-next-line
        $this->factory($demo)
            ->create([
                'title' => 'Vacation',
                'type' => BudgetType::EventPlanning,
            ])
            ->categories()->sync([
                [
                    'wallet_category_id' => $demo->categories->get('travel')?->id,
                    'allocated_amount' => 100000,
                    'used_amount' => 25000,
                    'currency' => self::$currency,
                ],
            ]);
    }

    private function createStudentLoanBudget(DemoData $demo): void
    {
        // @phpstan-ignore-next-line
        $this->factory($demo)
            ->create([
                'title' => 'Student Loan',
                'type' => BudgetType::DebtPayment,
                'end_date' => now()->addYears(10),
            ])
            ->categories()->sync([
                [
                    'wallet_category_id' => $demo->categories->get('financial')?->id,
                    'allocated_amount' => 123000,
                    'used_amount' => 1000000,
                    'currency' => self::$currency,
                ],
            ]);
    }

    private function createEntertainmentBudget(DemoData $demo): void
    {
        // @phpstan-ignore-next-line
        $this->factory($demo)
            ->create([
                'title' => 'Entertainment',
                'type' => BudgetType::Weekly,
            ])
            ->categories()->sync([
                [
                    'wallet_category_id' => $demo->categories->get('entertainment')?->id,
                    'allocated_amount' => 3000,
                    'used_amount' => 2000,
                    'currency' => self::$currency,
                ],
                [
                    'wallet_category_id' => $demo->categories->get('gaming')?->id,
                    'allocated_amount' => 7000,
                    'used_amount' => 3000,
                    'currency' => self::$currency,
                ],
            ]);
    }
}

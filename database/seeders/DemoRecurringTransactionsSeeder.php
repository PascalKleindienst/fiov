<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\Icon;
use App\Enums\RecurringFrequency;
use App\Models\RecurringTransaction;
use Database\Factories\RecurringTransactionFactory;
use Illuminate\Database\Seeder;
use Random\RandomException;

final class DemoRecurringTransactionsSeeder extends Seeder
{
    public static string $currency = 'EUR';

    /**
     * @throws RandomException
     */
    public function run(DemoData $demo): void
    {
        $this->factory($demo, 'financial')->create([
            'title' => 'Salary',
            'amount' => random_int(240000, 300000),
            'icon' => Icon::Money->value,
        ]);
        $this->factory($demo, 'household')->create([
            'title' => 'Rent',
            'amount' => -1 * random_int(60000, 80000),
            'icon' => Icon::Home->value,
        ]);
        $this->factory($demo, 'financial')->create([
            'title' => 'Savings Account',
            'amount' => -1 * random_int(10000, 20000),
            'icon' => Icon::Bank->value,
            'is_investment' => true,
        ]);
        $this->factory($demo, 'entertainment')->create([
            'title' => 'Video Streaming',
            'amount' => -1 * random_int(10000, 12000),
            'icon' => Icon::Gamepad->value,
            'frequency' => RecurringFrequency::YEARLY,
            'end_date' => now()->addYear(),
        ]);
    }

    private function factory(DemoData $demo, string $category): RecurringTransactionFactory
    {
        $factory = RecurringTransaction::factory()->for($demo->user)->for($demo->wallet)->state([
            'currency' => self::$currency,
            'frequency' => RecurringFrequency::MONTHLY,
            'start_date' => now()->subMonth(),
            'end_date' => null,
            'is_active' => true,
            'is_investment' => false,
        ]);

        if ($relation = $demo->categories->get($category)) {
            return $factory->for($relation, 'category');
        }

        return $factory;
    }
}

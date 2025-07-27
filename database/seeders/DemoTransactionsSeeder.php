<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\Icon;
use App\Models\WalletTransaction;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Random\RandomException;

final class DemoTransactionsSeeder extends Seeder
{
    public static string $currency = 'EUR';

    private readonly int $salary;

    private readonly int $savings;

    private readonly int $rent;

    /**
     * @throws RandomException
     */
    public function __construct()
    {
        $this->rent = -1 * random_int(60000, 80000);
        $this->salary = random_int(150000, 250000);
        $this->savings = -1 * random_int(25000, 35000);
    }

    /**
     * @throws RandomException
     */
    public function run(DemoData $demo): void
    {
        collect(range(0, 12))->each(fn (int $month) => $this->perMonth($month, $demo));
        collect(range(0, 56))->each(fn (int $week) => $this->perWeek($week, $demo));

        $this->factory($demo, 'travel')->create([
            'title' => 'Vacation',
            'icon' => Icon::Train->value,
            'amount' => -1 * random_int(100000, 120000),
            'created_at' => now()->month(now()->month < 8 ? now()->month : random_int(6, 8))->day(random_int(10, 20)),
        ]);

        $this->factory($demo, 'entertainment')->create([
            'title' => 'Birthday Party',
            'icon' => Icon::Pizza->value,
            'amount' => -1 * random_int(10000, 12000),
            'created_at' => now()->month(random_int(1, now()->month))->day(random_int(1, 25)),
        ]);
    }

    /**
     * @throws RandomException
     */
    private function perMonth(int $month, DemoData $demo): void
    {
        $this->factory($demo, 'entertainment')->createMany([
            [
                'title' => 'TV & Streaming',
                'icon' => Icon::TV->value,
                'amount' => -1 * random_int(2000, 3000),
                'created_at' => now()->startOfMonth()->subMonths($month)->day(random_int(2, 4)),
            ],
            [
                'title' => 'My Awesome Game',
                'icon' => Icon::Gamepad->value,
                'amount' => -1 * random_int(2000, 3000),
                'created_at' => now()->startOfMonth()->subMonths($month)->day(random_int(15, 20)),
            ],
        ]);

        $this->factory($demo, 'household')->create([
            'title' => 'Rent',
            'icon' => Icon::Home->value,
            'amount' => $this->rent,
            'created_at' => now()->startOfMonth()->subMonths($month)->startOfMonth(),
        ]);

        $this->factory($demo, 'financial')->state(['icon' => Icon::Money->value])->createMany([
            [
                'title' => 'Savings Account',
                'amount' => $this->savings,
                'created_at' => now()->startOfMonth()->subMonths($month)->endOfMonth()->subDay(),
                'is_investment' => true,
            ],
            [
                'title' => 'Salary',
                'amount' => $this->salary,
                'created_at' => now()->startOfMonth()->subMonths($month)->startOfMonth(),
            ],
        ]);
    }

    /**
     * @return Factory<WalletTransaction>
     */
    private function factory(DemoData $demo, ?string $category = null): Factory
    {
        $factory = WalletTransaction::factory()
            ->for($demo->wallet)
            ->state([
                'currency' => self::$currency,
                'is_investment' => false,
            ]);

        if ($category && $relation = $demo->categories->get($category)) {
            return $factory->for($relation, 'category');
        }

        return $factory;
    }

    /**
     * @throws RandomException
     */
    private function perWeek(int $week, DemoData $demo): void
    {
        $shop = Arr::random(['Aldi', 'Lidl', '7-Eleven']);

        $this->factory($demo, 'groceries')->createMany([
            [
                'title' => $shop,
                'icon' => Icon::Shopping->value,
                'amount' => -1 * random_int(3000, 6000),
                'created_at' => now()->startOfWeek()->subWeeks($week)->weekday(random_int(2, 4)),
            ],
            [
                'title' => 'Takeout',
                'icon' => Icon::Pizza->value,
                'amount' => -1 * random_int(3000, 6000),
                'created_at' => now()->startOfWeek()->subWeeks($week)->weekday(random_int(0, 2)),
            ],
        ]);

        $this->factory($demo, 'travel')->createMany([
            [
                'title' => 'Public Transport',
                'icon' => Icon::Train->value,
                'amount' => -1 * random_int(1000, 2000),
                'created_at' => now()->startOfWeek()->subWeeks($week)->weekday(random_int(0, 3)),
            ],
            [
                'title' => 'Public Transport',
                'icon' => Icon::Train->value,
                'amount' => -1 * random_int(1000, 2000),
                'created_at' => now()->startOfWeek()->subWeeks($week)->weekday(random_int(4, 6)),
            ],
        ]);
    }
}

<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use App\Models\Wallet;
use Illuminate\Database\Seeder;

final class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $user = $this->createDemoUser();
        $this->callWith(DemoWalletSeeder::class, ['user' => $user]);
        $this->callWith(DemoWalletCategorySeeder::class, ['user' => $user]);

        $demoData = new DemoData($user, Wallet::firstOrFail());

        $this->callWith(DemoRecurringTransactionsSeeder::class, ['demo' => $demoData]);
        $this->callWith(DemoTransactionsSeeder::class, ['demo' => $demoData]);

        $this->callWith(DemoBudgetSeeder::class, ['demo' => $demoData]);
    }

    private function createDemoUser(): User
    {
        $this->call(DemoUserSeeder::class);

        return User::firstOrFail();
    }
}

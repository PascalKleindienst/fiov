<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\Color;
use App\Enums\Icon;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Database\Seeder;

final class DemoWalletSeeder extends Seeder
{
    public static string $currency = 'EUR';

    /**
     * @param  User|null  $user
     */
    public function run(mixed $user = null): void
    {
        Wallet::factory()->for($user ?? User::factory()->create())->create([
            'title' => 'Main Wallet',
            'description' => 'The main wallet for the demo user.',
            'color' => Color::Blue->value,
            'icon' => Icon::Wallet->value,
            'currency' => self::$currency,
        ]);
    }
}

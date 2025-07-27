<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\Color;
use App\Enums\Icon;
use App\Models\User;
use App\Models\WalletCategory;
use Illuminate\Database\Seeder;

final class DemoWalletCategorySeeder extends Seeder
{
    /**
     * @param  User|null  $user
     */
    public function run(mixed $user = null): void
    {
        WalletCategory::factory()->for($user ?? User::factory()->create())->createMany([
            [
                'title' => 'Groceries',
                'color' => Color::Green->value,
                'icon' => Icon::Shopping->value,
            ], [
                'title' => 'Entertainment',
                'color' => Color::Orange->value,
                'icon' => Icon::Gamepad->value,
            ], [
                'title' => 'Travel',
                'color' => Color::Purple->value,
                'icon' => Icon::Train->value,
            ], [
                'title' => 'Household',
                'color' => Color::Blue->value,
                'icon' => Icon::Home->value,
            ], [
                'title' => 'Financial',
                'color' => Color::Yellow->value,
                'icon' => Icon::Bank->value,
            ],
        ]);
    }
}

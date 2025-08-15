<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\WalletCategory;
use App\Models\WalletCategoryRule;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Throwable;

final readonly class CreateOrUpdateCategory
{
    /**
     * @param  array<string, mixed>  $data
     * @param  ?Collection<int, WalletCategoryRule>  $rules
     *
     * @throws Throwable
     */
    public function handle(array $data, ?Collection $rules = null): void
    {
        DB::transaction(static function () use ($data, $rules): void {
            $model = WalletCategory::updateOrCreate(['id' => $data['id'] ?? null], $data);

            if (! $rules instanceof \Illuminate\Support\Collection) {
                return;
            }

            $model->loadMissing('rules')->rules->diff($rules)->each(fn (WalletCategoryRule $rule) => $rule->delete());

            $rules->each(function (WalletCategoryRule $rule) use ($model): void {
                $rule->wallet_category_id = $model->id;
                $rule->updateOrCreate(['id' => $rule->id], $rule->toArray());
            });
        });
    }
}

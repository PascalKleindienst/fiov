<?php

declare(strict_types=1);

namespace App\Models;

use App\Casts\EncryptedMoneyCast;
use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * @property int $budget_id
 * @property int $wallet_category_id
 * @property \Cknow\Money\Money $allocated_amount
 * @property \Cknow\Money\Money|null $used_amount
 * @property string $currency
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BudgetCategory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BudgetCategory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BudgetCategory query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BudgetCategory whereAllocatedAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BudgetCategory whereBudgetId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BudgetCategory whereCurrency($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BudgetCategory whereUsedAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BudgetCategory whereWalletCategoryId($value)
 *
 * @mixin \Eloquent
 */
final class BudgetCategory extends Pivot
{
    protected function casts(): array
    {
        return [
            'allocated_amount' => EncryptedMoneyCast::class,
            'used_amount' => EncryptedMoneyCast::class,
        ];
    }
}

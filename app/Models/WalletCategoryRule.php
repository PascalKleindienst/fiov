<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\RuleOperator;
use App\Models\Concerns\Encryptable;
use Database\Factories\WalletCategoryRuleFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property string $field
 * @property RuleOperator $operator
 * @property mixed $value
 * @property int $wallet_category_id
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property-read \App\Models\WalletCategory $category
 *
 * @method static \Database\Factories\WalletCategoryRuleFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WalletCategoryRule newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WalletCategoryRule newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WalletCategoryRule query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WalletCategoryRule whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WalletCategoryRule whereField($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WalletCategoryRule whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WalletCategoryRule whereOperator($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WalletCategoryRule whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WalletCategoryRule whereValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WalletCategoryRule whereWalletCategoryId($value)
 *
 * @mixin \Eloquent
 */
final class WalletCategoryRule extends Model
{
    use Encryptable;

    /** @use HasFactory<WalletCategoryRuleFactory> */
    use HasFactory;

    protected $fillable = [
        'field',
        'operator',
        'value',
        'wallet_category_id',
    ];

    /**
     * @return BelongsTo<WalletCategory, $this>
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(WalletCategory::class, 'wallet_category_id');
    }

    protected function casts(): array
    {
        return [
            'value' => 'encrypted',
            'operator' => RuleOperator::class,
        ];
    }
}

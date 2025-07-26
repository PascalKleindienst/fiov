<?php

declare(strict_types=1);

namespace App\Models;

use App\Casts\EncryptedMoneyCast;
use App\Enums\Icon;
use App\Enums\RecurringFrequency;
use App\Models\Concerns\Encryptable;
use App\Models\Scopes\OwnerScope;
use Database\Factories\RecurringTransactionFactory;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property string $title
 * @property Icon|null $icon
 * @property \Cknow\Money\Money $amount
 * @property string $currency
 * @property bool $is_investment
 * @property RecurringFrequency $frequency
 * @property \Carbon\CarbonImmutable $start_date
 * @property \Carbon\CarbonImmutable|null $end_date
 * @property \Carbon\CarbonImmutable|null $last_processed_at
 * @property bool $is_active
 * @property int $user_id
 * @property int $wallet_id
 * @property int $wallet_category_id
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property-read \App\Models\WalletCategory $category
 * @property-read \App\Models\User $user
 * @property-read \App\Models\Wallet $wallet
 *
 * @method static \Database\Factories\RecurringTransactionFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RecurringTransaction newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RecurringTransaction newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RecurringTransaction query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RecurringTransaction whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RecurringTransaction whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RecurringTransaction whereCurrency($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RecurringTransaction whereEndDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RecurringTransaction whereFrequency($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RecurringTransaction whereIcon($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RecurringTransaction whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RecurringTransaction whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RecurringTransaction whereIsInvestment($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RecurringTransaction whereLastProcessedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RecurringTransaction whereStartDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RecurringTransaction whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RecurringTransaction whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RecurringTransaction whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RecurringTransaction whereWalletCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RecurringTransaction whereWalletId($value)
 *
 * @mixin \Eloquent
 */
#[ScopedBy(OwnerScope::class)]
final class RecurringTransaction extends Model
{
    use Encryptable;

    /** @use HasFactory<RecurringTransactionFactory> */
    use HasFactory;

    protected $fillable = [
        'title',
        'icon',
        'amount',
        'currency',
        'is_investment',
        'frequency',
        'start_date',
        'end_date',
        'last_processed_at',
        'is_active',
        'user_id',
        'wallet_id',
        'wallet_category_id',
    ];

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsTo<Wallet, $this>
     */
    public function wallet(): BelongsTo
    {
        return $this->belongsTo(Wallet::class);
    }

    /**
     * @return BelongsTo<WalletCategory, $this>
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(WalletCategory::class, foreignKey: 'wallet_category_id');
    }

    protected function casts(): array
    {
        return [
            'is_investment' => 'boolean',
            'is_active' => 'boolean',
            'start_date' => 'date',
            'end_date' => 'date',
            'last_processed_at' => 'datetime',
            'icon' => Icon::class,
            'frequency' => RecurringFrequency::class,
            'title' => 'encrypted',
            'amount' => EncryptedMoneyCast::class.':currency',
        ];
    }
}

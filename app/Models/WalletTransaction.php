<?php

declare(strict_types=1);

namespace App\Models;

use Cknow\Money\Casts\MoneyDecimalCast;
use Database\Factories\WalletTransactionFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

/**
 * @property int $id
 * @property string $title
 * @property string|null $icon
 * @property \Cknow\Money\Money $amount
 * @property string|null $currency
 * @property bool $is_investment
 * @property int $wallet_id
 * @property int $wallet_category_id
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property string $transaction_id
 * @property-read \App\Models\WalletCategory $category
 * @property-read bool $is_spending
 * @property-read \App\Models\Wallet $wallet
 *
 * @method static \Database\Factories\WalletTransactionFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WalletTransaction newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WalletTransaction newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WalletTransaction query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WalletTransaction whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WalletTransaction whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WalletTransaction whereCurrency($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WalletTransaction whereIcon($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WalletTransaction whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WalletTransaction whereIsInvestment($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WalletTransaction whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WalletTransaction whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WalletTransaction whereWalletCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WalletTransaction whereWalletId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WalletTransaction whereTransactionId($value)
 *
 * @mixin \Eloquent
 */
final class WalletTransaction extends Model
{
    /** @use HasFactory<WalletTransactionFactory> */
    use HasFactory;

    protected $fillable = [
        'title',
        'icon',
        'amount',
        'currency',
        'is_investment',
        'wallet_category_id',
    ];

    public function getRouteKeyName(): string
    {
        return 'transaction_id';
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

    /**
     * @return Attribute<bool, $this>
     */
    public function isSpending(): Attribute
    {
        return Attribute::make(
            get: fn (): bool => $this->amount->getAmount() < 0,
        );
    }

    protected static function boot(): void
    {
        parent::boot();

        self::creating(static function (WalletTransaction $transaction): void {
            $transaction->transaction_id ??= strtolower(Str::ulid()->toString());
        });
    }

    protected function casts(): array
    {
        return [
            'is_investment' => 'boolean',
            'amount' => MoneyDecimalCast::class.':currency',
        ];
    }
}

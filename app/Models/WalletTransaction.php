<?php

declare(strict_types=1);

namespace App\Models;

use Cknow\Money\Casts\MoneyDecimalCast;
use Database\Factories\WalletTransactionFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
        'user_id',
        'wallet_category_id',
    ];

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
    public function isSpeding(): Attribute
    {
        return Attribute::make(
            get: fn (): bool => $this->amount < 0,
        );
    }

    protected function casts(): array
    {
        return [
            'is_investment' => 'boolean',
            'amount' => MoneyDecimalCast::class.':currency',
        ];
    }
}

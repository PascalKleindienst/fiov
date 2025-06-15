<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\WalletCategoryFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class WalletCategory extends Model
{
    /** @use HasFactory<WalletCategoryFactory> */
    use HasFactory;

    protected $fillable = [
        'title',
        'icon',
        'color',
        'user_id',
    ];

    /**
     * @return HasMany<WalletTransaction, $this>
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(WalletTransaction::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

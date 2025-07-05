<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\Color;
use App\Enums\Icon;
use App\Models\Scopes\OwnerScope;
use Database\Factories\WalletCategoryFactory;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $title
 * @property Icon|null $icon
 * @property Color|null $color
 * @property int $user_id
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\WalletTransaction> $transactions
 * @property-read int|null $transactions_count
 * @property-read \App\Models\User $user
 *
 * @method static \Database\Factories\WalletCategoryFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WalletCategory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WalletCategory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WalletCategory query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WalletCategory whereColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WalletCategory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WalletCategory whereIcon($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WalletCategory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WalletCategory whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WalletCategory whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WalletCategory whereUserId($value)
 *
 * @mixin \Eloquent
 */
#[ScopedBy(OwnerScope::class)]
final class WalletCategory extends Model
{
    /** @use HasFactory<WalletCategoryFactory> */
    use HasFactory;

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

    /**
     * @return string[]
     */
    protected function casts(): array
    {
        return [
            'color' => Color::class,
            'icon' => Icon::class,
        ];
    }
}

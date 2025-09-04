<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\UserLevel;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property \Carbon\CarbonImmutable|null $email_verified_at
 * @property string $password
 * @property UserLevel $level
 * @property string|null $remember_token
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property string $encrypted_dek
 * @property string $encryption_salt
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\WalletCategory> $walletCategories
 * @property-read int|null $wallet_categories_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\WalletCategoryRule> $walletCategoryRules
 * @property-read int|null $wallet_category_rules_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\WalletTransaction> $walletTransactions
 * @property-read int|null $wallet_transactions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Wallet> $wallets
 * @property-read int|null $wallets_count
 *
 * @method static Builder<static>|User admin()
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static Builder<static>|User newModelQuery()
 * @method static Builder<static>|User newQuery()
 * @method static Builder<static>|User query()
 * @method static Builder<static>|User whereCreatedAt($value)
 * @method static Builder<static>|User whereEmail($value)
 * @method static Builder<static>|User whereEmailVerifiedAt($value)
 * @method static Builder<static>|User whereEncryptedDek($value)
 * @method static Builder<static>|User whereEncryptionSalt($value)
 * @method static Builder<static>|User whereId($value)
 * @method static Builder<static>|User whereLevel($value)
 * @method static Builder<static>|User whereName($value)
 * @method static Builder<static>|User wherePassword($value)
 * @method static Builder<static>|User whereRememberToken($value)
 * @method static Builder<static>|User whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
final class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory;

    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->take(2)
            ->map(fn (string $word) => Str::substr($word, 0, 1))
            ->implode('');
    }

    /**
     * @return HasMany<Wallet, $this>
     */
    public function wallets(): HasMany
    {
        return $this->hasMany(Wallet::class);
    }

    /**
     * @return HasMany<WalletCategory, $this>
     */
    public function walletCategories(): HasMany
    {
        return $this->hasMany(WalletCategory::class);
    }

    /**
     * @return HasManyThrough<WalletTransaction, Wallet, $this>
     */
    public function walletTransactions(): HasManyThrough
    {
        return $this->hasManyThrough(WalletTransaction::class, Wallet::class)->with(['wallet', 'category'])->latest();
    }

    /**
     * @return HasManyThrough<WalletCategoryRule, WalletCategory, $this>
     */
    public function walletCategoryRules(): HasManyThrough
    {
        return $this->hasManyThrough(WalletCategoryRule::class, WalletCategory::class)->with(['category']);
    }

    /**
     * @param  Builder<$this>  $query
     */
    #[Scope]
    public function admin(Builder $query): void
    {
        $query->where('level', UserLevel::Admin);
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'level' => UserLevel::class,
        ];
    }
}

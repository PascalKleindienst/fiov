<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\BudgetStatus;
use App\Enums\BudgetType;
use App\Enums\Priority;
use App\Models\Concerns\Encryptable;
use App\Models\Scopes\OwnerScope;
use App\Queries\Builders\BudgetBuilder;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Attributes\UseEloquentBuilder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Money\Money;

/**
 * @property int $id
 * @property int $user_id
 * @property int $wallet_id
 * @property string $title
 * @property string|null $description
 * @property array<array-key, mixed>|null $milestones
 * @property BudgetType $type
 * @property Priority $priority
 * @property BudgetStatus $status
 * @property \Carbon\CarbonImmutable $start_date
 * @property \Carbon\CarbonImmutable|null $end_date
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property-read Money $amount
 * @property-read \App\Models\BudgetCategory|null $pivot
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\WalletCategory> $categories
 * @property-read int|null $categories_count
 * @property-read Money $current_amount
 * @property-read float $days_remaining
 * @property-read bool $is_over_budget
 * @property-read Money $remaining_amount
 * @property-read float|null $progress_percentage
 * @property-read \App\Models\User $user
 * @property-read \App\Models\Wallet $wallet
 *
 * @method static BudgetBuilder<static>|Budget active()
 * @method static BudgetBuilder<static>|Budget default()
 * @method static \Database\Factories\BudgetFactory factory($count = null, $state = [])
 * @method static BudgetBuilder<static>|Budget goalBased()
 * @method static BudgetBuilder<static>|Budget newModelQuery()
 * @method static BudgetBuilder<static>|Budget newQuery()
 * @method static BudgetBuilder<static>|Budget priority(\App\Enums\Priority $priority)
 * @method static BudgetBuilder<static>|Budget query()
 * @method static BudgetBuilder<static>|Budget whereCreatedAt($value)
 * @method static BudgetBuilder<static>|Budget whereDescription($value)
 * @method static BudgetBuilder<static>|Budget whereEndDate($value)
 * @method static BudgetBuilder<static>|Budget whereId($value)
 * @method static BudgetBuilder<static>|Budget whereMilestones($value)
 * @method static BudgetBuilder<static>|Budget wherePriority($value)
 * @method static BudgetBuilder<static>|Budget whereStartDate($value)
 * @method static BudgetBuilder<static>|Budget whereStatus($value)
 * @method static BudgetBuilder<static>|Budget whereTitle($value)
 * @method static BudgetBuilder<static>|Budget whereType($value)
 * @method static BudgetBuilder<static>|Budget whereUpdatedAt($value)
 * @method static BudgetBuilder<static>|Budget whereUserId($value)
 * @method static BudgetBuilder<static>|Budget whereWalletId($value)
 *
 * @mixin \Eloquent
 */
#[UseEloquentBuilder(BudgetBuilder::class)]
#[ScopedBy(OwnerScope::class)]
final class Budget extends Model
{
    use Encryptable;

    /** @use HasFactory<\Database\Factories\BudgetFactory> */
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'milestones',
        'type',
        'priority',
        'status',
        'start_date',
        'end_date',
        'user_id',
        'wallet_id',
    ];

    protected $casts = [
        'name' => 'encrypted',
        'description' => 'encrypted',
        'milestones' => 'array',
        'priority' => Priority::class,
        'type' => BudgetType::class,
        'status' => BudgetStatus::class,
        'start_date' => 'date',
        'end_date' => 'date',
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

    // TODO: maybe?
    // /**
    //  * Calculate the end date based on the budget type and start date
    //  */
    // public function calculateEndDate(): \Carbon\CarbonImmutable
    // {
    //     return match ($this->type) {
    //         BudgetType::WEEKLY => $this->start_date->addWeek(),
    //         BudgetType::MONTHLY => $this->start_date->addMonth(),
    //         BudgetType::YEARLY => $this->start_date->addYear(),
    //         default => $this->end_date ?? $this->start_date->addMonth(),
    //     };
    // }

    /**
     * @return BelongsToMany<WalletCategory, $this, BudgetCategory>
     */
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(
            WalletCategory::class,
            'budget_category',
            'budget_id',
            'wallet_category_id'
        )
            ->using(BudgetCategory::class)
            ->withPivot([
                'allocated_amount',
                'used_amount',
            ]);
    }

    // TODO: Maybe better to calculate this on the fly?
    // /**
    //  * Get the current amount spent in this budget period
    //  */
    // public function getSpentAmount(): float
    // {
    //     return (float) $this->categories()
    //         ->withSum(['transactions' => function ($query) {
    //             $query->whereBetween('created_at', [
    //                 $this->start_date->startOfDay(),
    //                 $this->end_date ? $this->end_date->endOfDay() : now(),
    //             ]);
    //         }], 'amount')
    //         ->get()
    //         ->sum(function ($category) {
    //             return abs((float) $category->transactions_sum_amount);
    //         });
    // }

    /**
     * @return Attribute<Money, $this>
     */
    public function currentAmount(): Attribute
    {
        return Attribute::make(
            get: fn () => $this
                ->loadMissing('categories')
                ->categories
                ->filter(static fn (WalletCategory $category): bool => $category->pivot?->used_amount?->isPositive() ?? false)
                ->reduce(
                    static fn (\Cknow\Money\Money $carry, WalletCategory $category): \Cknow\Money\Money => $carry->add($category->pivot->used_amount ?? money(0)),
                    money(0)
                )
        );
    }

    /**
     * @return Attribute<Money, $this>
     */
    public function amount(): Attribute
    {
        return Attribute::make(
            get: fn () => $this
                ->loadMissing('categories')
                ->categories
                ->reduce(
                    static fn (\Cknow\Money\Money $carry, WalletCategory $category): \Cknow\Money\Money => $carry->add($category->pivot->allocated_amount ?? money(0)),
                    money(0)
                )
        );
    }

    /**
     * @return Attribute<Money, $this>
     */
    public function remainingAmount(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->amount->subtract($this->current_amount),
        );
    }

    /**
     * @return Attribute<bool, $this>
     */
    public function isOverBudget(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->current_amount->greaterThan($this->amount),
        );
    }

    /**
     * @return Attribute<float, $this>
     */
    public function daysRemaining(): Attribute
    {
        return Attribute::make(
            get: fn () => now()->startOfDay()->diffInDays($this->end_date?->startOfDay()),
        );
    }

    /**
     * @return Attribute<float, $this>
     */
    public function progressPercentage(): Attribute
    {
        return Attribute::make(
            get: fn (): mixed => $this->amount->isPositive() ? min((float) $this->current_amount->ratioOf($this->amount) * 100, 100) : 0.0,
        );
    }
}

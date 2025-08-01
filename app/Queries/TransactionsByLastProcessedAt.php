<?php

declare(strict_types=1);

namespace App\Queries;

use App\Contracts\FilterInterface;
use App\Enums\RecurringFrequency;
use App\Models\RecurringTransaction;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Builder;

/**
 * @implements FilterInterface<RecurringTransaction>
 */
final readonly class TransactionsByLastProcessedAt implements FilterInterface
{
    public function __construct(private CarbonImmutable $today) {}

    public function __invoke(Builder $query): Builder
    {
        return $query->where(fn (Builder $q) => $q
            ->whereNull('last_processed_at')
            ->orWhere(fn (Builder $q) => $q
                ->where('last_processed_at', '<', $this->today->copy()->startOfDay()->toDateTimeString())
                ->where(fn (Builder $q2) => $q2
                    ->where('frequency', RecurringFrequency::DAILY)
                    ->orWhere($this->weekly(...))
                    ->orWhere($this->monthly(...))
                    ->orWhere($this->yearly(...))
                )
            )
        );
    }

    /**
     * @param  Builder<RecurringTransaction>  $query
     */
    private function weekly(Builder $query): void
    {
        $query->where('frequency', RecurringFrequency::WEEKLY)
            ->whereRaw('strftime("%w", start_date) = strftime("%w", ?)', [$this->today]);
    }

    /**
     * @param  Builder<RecurringTransaction>  $query
     */
    private function monthly(Builder $query): void
    {
        $query->where('frequency', RecurringFrequency::MONTHLY)
            ->whereDay('start_date', $this->today->day);
    }

    /**
     * @param  Builder<RecurringTransaction>  $query
     */
    private function yearly(Builder $query): void
    {
        $query->where('frequency', RecurringFrequency::YEARLY)
            ->whereMonth('start_date', $this->today->month)
            ->whereDay('start_date', $this->today->day);
    }
}

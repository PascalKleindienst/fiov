@php
    $budgetProgressClass = [
        'bg-green-50 p-3 dark:bg-green-800/50' => ($budget->type->isGoalBased() && ! $budget->is_over_budget) || ($budget->type->isGoalBased() && $budget->is_over_budget),
        'bg-red-50 dark:bg-red-800/50' => ! $budget->type->isGoalBased() && $budget->is_over_budget,
        'bg-blue-50 dark:bg-blue-800/50' => ! $budget->is_over_budget,
        'mb-4 rounded-lg p-3',
    ];
@endphp

<x-card>
    <!-- Header -->
    <div class="mb-4 flex items-start justify-between">
        <div class="flex-1 space-y-1">
            <flux:heading level="3" size="lg" class="flex items-center gap-2">
                {{ $budget->title }}

                @if ($budget->type->isGoalBased())
                    <button title="{{ $budget->priority->label() }}">
                        <flux:icon :name="$budget->priority->icon()" class="{{ $budget->priority->color() }} size-4" />
                    </button>
                @endif
            </flux:heading>

            <div class="flex items-center space-x-2">
                <flux:badge variant="pill" color="indigo" size="sm">
                    {{ $budget->type->label() }}
                </flux:badge>

                <flux:badge variant="pill" :color="$budget->status->color()" size="sm">
                    {{ $budget->status->label() }}
                </flux:badge>
            </div>
        </div>

        <!-- Actions Dropdown -->
        <flux:dropdown align="end">
            <flux:button icon="ellipsis-vertical" variant="subtle" aria-label="{{ __('general.actions') }}"></flux:button>

            <flux:menu>
                <flux:menu.item icon="pencil" href="{{ route('budgets.edit', $budget) }}" wire:navigate>
                    {{ __('budgets.actions.edit') }}
                </flux:menu.item>

                @if ($budget->status->isPaused())
                    <flux:menu.item icon="play" wire:click="resume()">{{ __('budgets.actions.resume') }}</flux:menu.item>
                @else
                    <flux:menu.item icon="pause" wire:click="pause()">{{ __('budgets.actions.pause') }}</flux:menu.item>
                @endif

                <flux:modal.trigger name="confirm-budget-deletion-{{ $budget->id }}">
                    <flux:menu.item :title="__('budgets.actions.delete', ['name' => $budget->title])" variant="danger" icon="trash">
                        {{ __('budgets.actions.delete') }}
                    </flux:menu.item>
                </flux:modal.trigger>
            </flux:menu>
        </flux:dropdown>
    </div>

    <!-- Progress Section -->
    @if (! $budget->type->isGoalBased())
        <div @class($budgetProgressClass)>
            <x-progress
                :value="min($budget->progress_percentage, 100)"
                :variant="$budget->is_over_budget ? 'danger' : 'info'"
                size="md"
                :title="__('budgets.progress.spent', ['amount' => $budget->current_amount])"
                :label="__('budgets.progress.total', ['amount' => $budget->amount])"
            />
            <div class="mt-1 flex justify-between text-sm">
                <flux:text color="{{ $budget->is_over_budget ? 'red' : '' }}">
                    {{ __('budgets.progress.used', ['percentage' => round($budget->progress_percentage, 1)]) }}
                </flux:text>
                @if (! $budget->is_over_budget)
                    <flux:text>{{ __('budgets.progress.remaining', ['amount' => $budget->remaining_amount]) }}</flux:text>
                @endif
            </div>
        </div>
    @endif

    <!-- Goal Progress (for goal-based budgets) -->
    @if ($budget->type->isGoalBased())
        <div @class($budgetProgressClass)>
            <x-progress
                :value="min($budget->progress_percentage, 100)"
                variant="success"
                size="md"
                :title="__('budgets.progress.progress', ['amount' => $budget->current_amount])"
                :label="__('budgets.progress.target', ['amount' => $budget->amount])"
            />
            <div class="mt-1 flex justify-between text-sm">
                <flux:text color="{{ $budget->is_over_budget ? 'green' : '' }}">
                    {{ __('budgets.progress.completed', ['percentage' => round($budget->progress_percentage, 1)]) }}
                </flux:text>
                @if (! $budget->is_over_budget)
                    <flux:text>{{ __('budgets.progress.remaining', ['amount' => $budget->remaining_amount]) }}</flux:text>
                @endif
            </div>
        </div>
    @endif

    <!-- Categories -->
    <div class="mb-4">
        <div class="flex flex-wrap gap-1">
            @foreach ($budget->categories->take(3) as $category)
                <flux:badge variant="pill" size="sm">
                    {{ $category->title }}
                </flux:badge>
            @endforeach

            @if ($budget->categories->count() > 3)
                <flux:badge variant="pill" size="sm">+{{ $budget->categories->count() - 3 }}</flux:badge>
            @endif
        </div>
    </div>

    {{-- Date Range & Days Remaining --}}
    <div class="flex items-center justify-between">
        <flux:text variant="subtle">
            {{ $budget->start_date->format('M j') }}
            @if ($budget->end_date)
                - {{ $budget->end_date?->format('M j, Y') }}
            @endif
        </flux:text>
        @if ($budget->end_date)
            <flux:text variant="subtle" color="{{ $budget->days_remaining < 0 ? 'red' : ($budget->days_remaining <= 7 ? 'yellow' : '') }}">
                @if ($budget->days_remaining < 0)
                    {{ __('budgets.expires.past', ['days' => abs($budget->days_remaining)]) }}
                @elseif ($budget->days_remaining == 0)
                    {{ __('budgets.expires.today') }}
                @else
                    {{ __('budgets.expires.future', ['days' => $budget->days_remaining]) }}
                @endif
            </flux:text>
        @endif
    </div>

    <flux:modal name="confirm-budget-deletion-{{ $budget->id }}" :show="$errors->isNotEmpty()" focusable class="max-w-lg">
        <form wire:submit="delete()" class="space-y-6">
            <div>
                <flux:heading size="lg">{{ __('budgets.confirm_delete') }}</flux:heading>

                <flux:subheading>
                    {{ __('budgets.confirm_delete_desc') }}
                </flux:subheading>
            </div>

            <div class="flex justify-end space-x-2 rtl:space-x-reverse">
                <flux:modal.close>
                    <flux:button variant="filled">{{ __('general.cancel') }}</flux:button>
                </flux:modal.close>

                <flux:button variant="danger" type="submit">
                    {{ __('general.delete') }}
                </flux:button>
            </div>
        </form>
    </flux:modal>
</x-card>

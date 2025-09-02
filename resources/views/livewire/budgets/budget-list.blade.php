@php
    $budgetTypes = \App\Enums\BudgetType::cases();
    $budgetStatus = \App\Enums\BudgetStatus::cases();
@endphp

<div class="space-y-6">
    <div class="flex items-center justify-between gap-4">
        <div>
            <flux:heading size="xl" level="1">{{ __('budgets.index') }}</flux:heading>
            <flux:text>{{ __('budgets.index_description') }}</flux:text>
        </div>
        @can('create', App\Models\Budget::class)
            <flux:button variant="primary" :href="route('budgets.create')" wire:navigate class="mb-4">
                {{ __('budgets.create') }}
            </flux:button>
        @endcan
    </div>

    <section>
        <!-- Search and Filters -->
        <div class="mb-6">
            <div class="flex flex-col space-y-4 sm:flex-row sm:items-center sm:justify-between sm:space-y-0">
                <div class="flex items-center space-x-4">
                    <flux:select wire:model.live="type" :label="__('budgets.fields.type')">
                        <option value="">{{ __('general.all_items') }}</option>
                        @foreach ($budgetTypes as $type)
                            <option value="{{ $type->value }}">
                                {{ $type->label() }}
                            </option>
                        @endforeach
                    </flux:select>
                    <flux:select wire:model.live="status" :label="__('budgets.fields.status')">
                        <option value="">{{ __('general.all_items') }}</option>
                        @foreach ($budgetStatus as $status)
                            <option value="{{ $status->value }}">
                                {{ $status->label() }}
                            </option>
                        @endforeach
                    </flux:select>
                </div>
            </div>
        </div>

        <!-- Budget Grid -->
        @if ($this->budgets->isNotEmpty())
            <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
                @foreach ($this->budgets as $budget)
                    <livewire:budgets.budget-card :budget="$budget" :key="$budget->id" />
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-8">
                {{ $this->budgets->links() }}
            </div>
        @else
            <x-card class="space-y-4 py-12 text-center">
                <div class="mx-auto mb-4 flex h-24 w-24 items-center justify-center rounded-full bg-gray-100 dark:bg-gray-600">
                    <flux:icon name="hand-coins" class="size-12" />
                </div>
                <flux:heading size="xl">{{ __('budgets.empty.title') }}</flux:heading>
                <flux:text size="xl">{{ __('budgets.empty.description') }}</flux:text>
                <flux:button variant="primary" :href="route('budgets.create')" wire:navigate>
                    {{ __('budgets.create') }}
                </flux:button>
            </x-card>
        @endif
    </section>
</div>

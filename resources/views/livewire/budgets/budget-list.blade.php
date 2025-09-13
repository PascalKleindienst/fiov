@php
    $budgetTypes = \App\Enums\BudgetType::cases();
    $budgetStatus = \App\Enums\BudgetStatus::cases();
@endphp

<div class="space-y-6">
    <x-sections.header
        :title="__('budgets.index')"
        :lead="__('budgets.index_description')"
        :can:action="auth()->user()->can('create', App\Models\Budget::class)"
        :action:text="__('budgets.create')"
        :action:href="route('budgets.create')"
        action:icon="plus"
    />

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
            <x-empty icon="banknotes" title="{{ __('budgets.empty.title') }}" description="{{ __('budgets.empty.description') }}">
                <flux:button variant="primary" :href="route('budgets.create')" wire:navigate>
                    {{ __('budgets.create') }}
                </flux:button>
            </x-empty>
        @endif
    </section>
</div>

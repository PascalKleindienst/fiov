@php
    use Illuminate\Support\Number;

    $statuses = \App\Enums\BudgetStatus::cases();
@endphp

<div class="space-y-6 max-md:pt-6">
    @if ($form->isEdit)
        <x-sections.header :title="__('budgets.edit', ['name' => $form->model->title])" />
    @else
        <x-sections.header :title="__('budgets.create')" :lead="__('budgets.create_description')" />
    @endif

    {{-- Form --}}
    <form wire:submit.prevent="save" class="space-y-6 max-md:pt-6">
        {{-- <x-card> --}}
        <div class="grid gap-6 sm:grid-cols-2">
            <flux:input wire:model.defer="form.title" type="text" :label="__('budgets.fields.name')" required />

            <flux:select wire:model.live="form.wallet_id" :label="__('budgets.fields.wallet')" required>
                <flux:select.option value="">{{ __('general.please_select') }}</flux:select.option>
                @foreach ($wallets as $wallet)
                    <flux:select.option value="{{ $wallet->id }}">{{ $wallet->title }} ({{ $wallet->currency }})</flux:select.option>
                @endforeach
            </flux:select>

            <flux:select wire:model.live="form.type" :label="__('budgets.fields.type')" required>
                <flux:select.option value="">{{ __('general.please_select') }}</flux:select.option>
                <flux:select.option :value="App\Enums\BudgetType::Default->value">{{ App\Enums\BudgetType::Default->label() }}</flux:select.option>

                <optgroup label="{{ __('budgets.types.recurring') }}">
                    @foreach (App\Enums\BudgetType::recurringStates() as $type)
                        <flux:select.option value="{{ $type->value }}">{{ $type->label() }}</flux:select.option>
                    @endforeach
                </optgroup>

                <optgroup label="{{ __('budgets.types.goalBased') }}">
                    @foreach (App\Enums\BudgetType::goalBasedStates() as $type)
                        <flux:select.option value="{{ $type->value }}">{{ $type->label() }}</flux:select.option>
                    @endforeach
                </optgroup>
            </flux:select>

            <div>
                <flux:input.group :label="__('budgets.fields.amount')">
                    <flux:input.group.prefix>{{ $form->currency }}</flux:input.group.prefix>
                    <flux:input wire:model.blur="form.amount" type="number" step="0.01" required />
                </flux:input.group>
                <flux:error name="form.amount" class="mt-2" />
            </div>

            <flux:input :label="__('budgets.fields.start_date')" wire:model.live.debounce.300ms="form.start_date" type="date" required />

            <flux:input :label="__('budgets.fields.end_date')" wire:model.defer="form.end_date" type="date" />

            @if (isset($form->type) && $form->type->isGoalBased())
                <flux:select wire:model="form.priority" :label="__('budgets.fields.priority')">
                    <flux:select.option value="">{{ __('general.please_select') }}</flux:select.option>
                    @foreach (\App\Enums\Priority::cases() as $type)
                        <flux:select.option value="{{ $type->value }}">{{ $type->label() }}</flux:select.option>
                    @endforeach
                </flux:select>
            @endif

            <div class="col-span-full">
                <flux:textarea :label="__('budgets.fields.description')" wire:model.defer="form.description" rows="3" />
            </div>
        </div>
        {{-- </x-card> --}}

        <flux:separator class="my-8" />

        {{-- Categories --}}
        {{-- <x-card> --}}
        <flux:heading size="lg" level="3">
            {{ __('budgets.categories.title') }}
        </flux:heading>
        <flux:text>
            {{ __('budgets.categories.select') }}
        </flux:text>

        <div class="my-5">
            <flux:input
                wire:model.live.debounce.300ms="search"
                type="search"
                placeholder="{{ __('general.search_placeholder') }}"
                class="block w-full"
            />
        </div>

        <div class="space-y-4">
            @if ($this->filteredCategories->isEmpty())
                <flux:callout variant="secondary" icon="info" :heading="__('budgets.categories.no_categories_found')" />
            @else
                <div class="ring-opacity-5 overflow-hidden rounded-md shadow ring-1 ring-zinc-200 dark:ring-zinc-700">
                    <ul class="divide-y divide-zinc-200 dark:divide-zinc-700">
                        @foreach ($this->filteredCategories as $category)
                            <li class="px-4 py-4 sm:px-6">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <flux:icon :name="$category->icon->value ?? 'wallet'" class="size-6" />
                                        <flux:text variant="strong" class="ml-3">{{ $category->title }}</flux:text>
                                    </div>
                                    <div class="flex items-center gap-x-4">
                                        <flux:switch wire:model.live="form.selectedCategories" :value="$category->id" class="order-2 flex-shrink-0" />
                                        <div class="order-1 w-32">
                                            <flux:input
                                                wire:model.blur="form.allocatedAmounts.{{ $category->id }}"
                                                type="number"
                                                step="0.01"
                                                min="0"
                                                :max="$form->amount"
                                                :placeholder="__('budgets.fields.amount')"
                                                :disabled="!in_array($category->id, $form->selectedCategories)"
                                            />
                                        </div>
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <flux:error name="form.selectedCategories" class="mt-2" />
            @endif
            {{-- </div> --}}

            <flux:separator class="my-4" />

            <div class="">
                <x-progress
                    :title="__('budgets.categories.total_allocated')"
                    :label="Number::currency($this->totalAllocatedAmount, $form->currency, locale: app()->getLocale())"
                    :variant="$this->totalAllocatedAmount > $form->amount ? 'danger' : 'info'"
                    :value="$form->amount > 0 ? min(100, ($this->totalAllocatedAmount / $form->amount) * 100) : 0"
                    size="md"
                />
                <div class="my-1 flex justify-between">
                    <flux:text variant="subtle">
                        {{ Number::currency(0, $form->currency, locale: app()->getLocale()) }}
                    </flux:text>
                    <flux:text variant="subtle">
                        {{ Number::currency($form->amount, $form->currency, locale: app()->getLocale()) }}
                    </flux:text>
                </div>

                @if ($this->totalAllocatedAmount > $form->amount)
                    <flux:callout variant="danger" icon="triangle-alert" :heading="__('budgets.categories.warning_allocated_amount_exceeded')" />
                @endif
            </div>
            {{-- </x-card> --}}

            <div class="flex justify-start space-x-3">
                <flux:button variant="primary" type="submit">
                    {{ __('general.save') }}
                </flux:button>
                <flux:button variant="ghost" :href="route('budgets.index')" wire:navigate>
                    {{ __('general.cancel') }}
                </flux:button>
            </div>
        </div>
    </form>
</div>

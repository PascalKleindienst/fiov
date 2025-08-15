<div class="space-y-6 max-md:pt-6">
    <div class="relative mb-6 w-full">
        <flux:heading size="xl" level="1">
            @if ($form->model)
                {{ __('categories.edit', ['name' => $form->model->title]) }}
            @else
                {{ __('categories.create') }}
            @endif
        </flux:heading>
        {{-- <flux:subheading size="lg" class="mb-6"> --}}
        {{-- Lorem ipsum dolor sit amet, consectetur adipisicing elit. Architecto beatae commodi consequuntur delectus dolores eius eos eum fuga, --}}
        {{-- maiores, odit pariatur, quibusdam sapiente sequi! Blanditiis consequatur esse itaque nulla quae! --}}
        {{-- </flux:subheading> --}}
        {{-- <flux:separator variant="subtle" /> --}}
    </div>

    <form wire:submit.prevent="save" class="grid w-full max-w-4xl items-start gap-6 space-y-6 md:grid-cols-2">
        <flux:input :label="__('categories.fields.title')" wire:model="form.title" required />

        <flux:radio.group
            :label="__('categories.fields.color')"
            wire:model="form.color"
            class="grid [grid-template-columns:repeat(auto-fill,minmax(48px,1fr))] gap-4 [&>[data-flux-field]]:mb-0"
        >
            @foreach (\App\Enums\Color::cases() as $color)
                <x-radio.card name="form.color" :value="$color->value" :checked="$form->color === $color->value">
                    <div class="{{ $color->css() }} size-8 rounded-full p-2"></div>
                </x-radio.card>
            @endforeach
        </flux:radio.group>

        <div class="col-span-full">
            <flux:radio.group
                :label="__('categories.fields.icon')"
                wire:model="form.icon"
                class="grid [grid-template-columns:repeat(auto-fill,minmax(48px,1fr))] gap-4 [&>[data-flux-field]]:mb-0"
            >
                @foreach (\App\Enums\Icon::cases() as $icon)
                    <x-radio.card name="form.icon" :value="$icon->value" :checked="$form->icon === $icon->value">
                        <flux:icon name="{{ $icon->value }}" class="size-8" />
                    </x-radio.card>
                @endforeach
            </flux:radio.group>
        </div>

        <div class="col-span-full">
            <flux:heading size="lg" level="2" class="mb-4">
                {{ __('categories.fields.rules.title') }}
            </flux:heading>

            <div class="mb-4 grid grid-cols-4 gap-4">
                @foreach ($this->form->rules as $index => $rule)
                    @if ($rule !== null)
                        <div>
                            <flux:select wire:model="form.rules.{{ $index }}.field" :label="__('categories.fields.rules.field')">
                                <flux:select.option value="title">{{ __('transactions.fields.title') }}</flux:select.option>
                                <flux:select.option value="icon">{{ __('transactions.fields.icon') }}</flux:select.option>
                                <flux:select.option value="amount">{{ __('transactions.fields.amount') }}</flux:select.option>
                                <flux:select.option value="is_investment">{{ __('transactions.fields.is_investment') }}</flux:select.option>
                            </flux:select>
                        </div>
                        <div>
                            <flux:select wire:model="form.rules.{{ $index }}.operator" :label="__('categories.fields.rules.operator')">
                                @foreach (\App\Enums\RuleOperator::cases() as $operator)
                                    <flux:select.option :value="$operator->value">{{ $operator->label() }}</flux:select.option>
                                @endforeach
                            </flux:select>
                        </div>
                        <div>
                            <flux:input
                                type="text"
                                wire:model="form.rules.{{ $index }}.value"
                                :label="__('categories.fields.rules.value')"
                                placeholder="foo"
                            />
                        </div>

                        <div class="place-content-end">
                            <flux:button
                                variant="danger"
                                type="button"
                                wire:click="removeRule({{ $index }})"
                                class="items-baseline"
                                :title="__('categories.rules.remove')"
                            >
                                <flux:icon name="trash" class="size-4" />
                                <span class="sr-only">{{ __('categories.rules.remove') }}</span>
                            </flux:button>
                        </div>
                    @endif
                @endforeach
            </div>

            <flux:button variant="primary" type="button" wire:click="addRule" icon="plus">
                {{ __('categories.rules.add') }}
            </flux:button>
        </div>

        <flux:error name="form.capability" />

        <flux:button variant="primary" type="submit" class="w-full">
            {{ __('general.save') }}
        </flux:button>
    </form>
</div>

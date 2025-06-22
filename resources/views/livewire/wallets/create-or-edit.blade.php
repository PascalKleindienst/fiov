<div class="space-y-6 max-md:pt-6">
    <div class="relative mb-6 w-full">
        <flux:heading size="xl" level="1">
            @if ($form->model)
                {{ __('wallets.edit', ['name' => $form->model->title]) }}
            @else
                {{ __('wallets.create') }}
            @endif
        </flux:heading>
        {{-- <flux:subheading size="lg" class="mb-6"> --}}
        {{-- Lorem ipsum dolor sit amet, consectetur adipisicing elit. Architecto beatae commodi consequuntur delectus dolores eius eos eum fuga, --}}
        {{-- maiores, odit pariatur, quibusdam sapiente sequi! Blanditiis consequatur esse itaque nulla quae! --}}
        {{-- </flux:subheading> --}}
        {{-- <flux:separator variant="subtle" /> --}}
    </div>

    <form wire:submit.prevent="save" class="grid w-full max-w-4xl gap-6 space-y-6 md:grid-cols-2">
        <flux:input :label="__('wallets.fields.title')" wire:model="form.title" required />
        <flux:input :label="__('wallets.fields.description')" wire:model="form.description" required />

        {{-- TODO: combo box / Radio group --}}
        <flux:select :label="__('wallets.fields.currency')" wire:model="form.currency" required>
            @foreach (\App\Enums\Currency::cases() as $currency)
                <flux:select.option :value="$currency->value">{{ $currency->name }} ({{ $currency->symbol() }})</flux:select.option>
            @endforeach
        </flux:select>

        <flux:radio.group
            :label="__('wallets.fields.color')"
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
                :label="__('wallets.fields.icon')"
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

        <flux:error name="form.capability" />

        <flux:button variant="primary" type="submit" class="col-span-full">
            {{ __('general.save') }}
        </flux:button>
    </form>
</div>

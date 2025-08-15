<form wire:submit.prevent="save" class="grid gap-6 md:grid-cols-2">
    <flux:input :label="__('transactions.fields.title')" wire:model.blur="form.title" required autofocus />

    <flux:input :label="__('transactions.fields.amount')" type="number" step="0.01" wire:model.blur="form.amount" required />

    <flux:select :label="__('transactions.fields.category')" wire:model="form.wallet_category_id" required>
        <flux:select.option value="">--- {{ __('general.please_select') }} ---</flux:select.option>
        @foreach ($categories as $id => $title)
            <flux:select.option :value="$id">{{ $title }}</flux:select.option>
        @endforeach
    </flux:select>

    <flux:select :label="__('transactions.fields.wallet')" wire:model="form.wallet_id" required>
        @foreach ($wallets as $id => $title)
            <flux:select.option :value="$id">{{ $title }}</flux:select.option>
        @endforeach
    </flux:select>

    <div class="col-span-full">
        <flux:radio.group
            :label="__('categories.fields.icon')"
            wire:model.blur="form.icon"
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
        <flux:checkbox :label="__('transactions.fields.is_recurring')" wire:model.change="form.is_recurring" />
    </div>

    @if ($form->is_recurring)
        <flux:select :label="__('transactions.fields.recurring_frequency')" wire:model="form.recurring_frequency" required>
            <flux:select.option value="">--- {{ __('general.please_select') }} ---</flux:select.option>
            @foreach (\App\Enums\RecurringFrequency::cases() as $frequency)
                <option value="{{ $frequency->value }}">{{ $frequency->label() }}</option>
            @endforeach
        </flux:select>

        <flux:input
            :label="__('transactions.fields.recurring_end_date')"
            type="date"
            wire:model="form.recurring_end_date"
            min="{{ now()->addDay()->format('Y-m-d') }}"
        />
    @endif

    <flux:description class="col-span-full">
        {{ __('general.required_fields') }}
    </flux:description>

    <flux:button variant="primary" type="submit" class="col-span-full w-full">
        {{ __('general.save') }}
    </flux:button>
</form>

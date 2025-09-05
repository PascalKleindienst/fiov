<form wire:submit="save">
    <flux:modal.trigger name="edit-user-{{ $user->id }}">
        <flux:button variant="primary" icon="pencil">
            {{ __('users.actions.edit') }}
        </flux:button>
    </flux:modal.trigger>
    <flux:modal name="edit-user-{{ $user->id }}" class="md:min-w-96">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">{{ __('users.update.title') }}</flux:heading>
            </div>
            <flux:input :label="__('users.fields.name')" wire:model="name" required />
            <flux:input :label="__('users.fields.email')" wire:model="email" required />

            <flux:select wire:model="level" :label="__('users.fields.level')" required>
                @foreach (\App\Enums\UserLevel::cases() as $levelOption)
                    <option value="{{ $levelOption }}">
                        {{ $levelOption->name }}
                    </option>
                @endforeach
            </flux:select>

            <div class="mt-6 flex items-center justify-end gap-2">
                <flux:modal.close>
                    <flux:button type="button" variant="ghost">{{ __('general.cancel') }}</flux:button>
                </flux:modal.close>
                <flux:button type="submit" variant="primary">{{ __('general.save') }}</flux:button>
            </div>
        </div>
    </flux:modal>
</form>

<form wire:submit="inviteUser">
    <flux:modal.trigger name="invite-user">
        <flux:button variant="primary" class="mb-4">
            {{ __('users.actions.invite') }}
        </flux:button>
    </flux:modal.trigger>

    <flux:modal name="invite-user" class="md:w-96">
        <div class="space-y-6">
            <!-- Email Address -->
            <flux:input wire:model="email" :label="__('users.fields.email')" type="text" required autofocus placeholder="email@example.com" />

            <!-- User Level -->
            <flux:select wire:model="level" :label="__('users.fields.level')" required>
                @foreach (\App\Enums\UserLevel::cases() as $levelOption)
                    <option value="{{ $levelOption }}" @if ($levelOption === old('level')) selected="selected" @endif>
                        {{ $levelOption->name }}
                    </option>
                @endforeach
            </flux:select>

            <div class="mt-6 flex items-center justify-end gap-2">
                <flux:modal.close>
                    <flux:button type="button" variant="ghost">{{ __('general.cancel') }}</flux:button>
                </flux:modal.close>

                <flux:button type="submit" variant="primary" class="ml-4">
                    {{ __('users.actions.invite') }}
                </flux:button>
            </div>
        </div>
    </flux:modal>
</form>

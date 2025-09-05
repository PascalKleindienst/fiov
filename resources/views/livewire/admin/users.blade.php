<div>
    <x-slot name="heading">
        <div>
            <flux:heading size="xl" level="1">{{ __('users.index') }}</flux:heading>
            <flux:text>{{ __('users.index_description') }}</flux:text>
        </div>
        @can('create', App\Models\User::class)
            <livewire:users.invite-user />
        @endcan
    </x-slot>

    <x-table>
        <x-table.columns>
            <x-table.column :text="__('users.fields.id')" />
            <x-table.column :text="__('users.fields.name')" />
            <x-table.column :text="__('users.fields.email')" />
            <x-table.column :text="__('users.fields.level')" />
            <x-table.column />
        </x-table.columns>
        <x-table.rows>
            @foreach ($this->users as $user)
                <x-table.row>
                    <x-table.cell>{{ $user->id }}</x-table.cell>
                    <x-table.cell>
                        {{ $user->name }}
                    </x-table.cell>
                    <x-table.cell>{{ $user->email }}</x-table.cell>
                    <x-table.cell>
                        <flux:badge :color="$user->level->isAdmin() ? 'green' : 'blue'">{{ $user->level->name }}</flux:badge>
                    </x-table.cell>
                    <x-table.cell class="flex items-center gap-4">
                        <livewire:users.edit-user :user="$user" />

                        <flux:modal.trigger name="confirm-user-deletion-{{ $user->id }}">
                            <flux:button variant="danger" icon="trash">
                                {{ __('users.actions.delete') }}
                            </flux:button>
                        </flux:modal.trigger>

                        <flux:modal
                            name="confirm-user-deletion-{{ $user->id }}"
                            :show="$errors->isNotEmpty()"
                            class="max-w-lg"
                            wire:key="confirm-user-deletion-{{ $user->id }}"
                        >
                            <form wire:submit="delete({{ $user->id }})" class="space-y-6">
                                <div>
                                    <flux:heading size="lg">{{ __('users.confirm_delete') }}</flux:heading>

                                    <flux:subheading>
                                        {{ __('users.confirm_delete_desc') }}
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
                    </x-table.cell>
                </x-table.row>
            @endforeach
        </x-table.rows>
    </x-table>

    {{ $this->users->links() }}
</div>

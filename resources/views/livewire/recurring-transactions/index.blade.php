<div class="space-y-6">
    <x-sections.header :title="__('recurring_transactions.index')" />

    <div class="grid grid-cols-1 gap-4">
        @forelse ($transactions as $transaction)
            {{-- TODO: Move to transaction component --}}
            <div class="flex items-center gap-4 rounded border border-zinc-200 p-4 dark:border-zinc-600">
                <div class="flex items-center gap-4">
                    <div class="flex-shrink-0">
                        <flux:icon
                            :name="$transaction->icon?->value ?? $transaction->category->icon?->value ?? $transaction->wallet->icon?->value ?? 'wallet'"
                            class="{{ $transaction->category->color?->css() ?? $transaction->wallet->color?->css() }} size-12 rounded p-2"
                        />
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-900">{{ $transaction->title }}</p>
                        <div class="flex space-x-2 text-sm text-gray-500">
                            <span>{{ $transaction->wallet->title }}</span>
                            <span aria-hidden="true">&middot;</span>
                            <span>{{ $transaction->category->title }}</span>
                        </div>
                    </div>
                </div>
                <div class="ml-auto flex gap-4">
                    <div class="text-right">
                        <flux:heading
                            size="lg"
                            @class(['text-green-600' => $transaction->amount->isPositive(), 'text-rose-600' => $transaction->amount->isNegative()])
                        >
                            {{ $transaction->amount }}
                        </flux:heading>
                        <div class="text-sm text-gray-500">
                            {{ $transaction->frequency->label() }}

                            @if ($transaction->end_date)
                                {{ __('general.until_date', ['date' => $transaction->end_date->format('M d, Y')]) }}
                            @endif
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <button
                            type="button"
                            wire:click="toggleStatus({{ $transaction->id }})"
                            @class([
                                'focus:ring-primary-500 relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:ring-2 focus:ring-offset-2 focus:outline-none',
                                'bg-accent-content' => $transaction->is_active,
                                'bg-gray-200' => ! $transaction->is_active,
                            ])
                        >
                            <span class="sr-only">{{ __('general.toggle_status') }}</span>
                            <span
                                @class([
                                    'pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out',
                                    'translate-x-5' => $transaction->is_active,
                                    'translate-x-0' => ! $transaction->is_active,
                                ])
                            ></span>
                        </button>
                        @can('delete', $transaction)
                            {{-- TODO: Move to component --}}
                            <flux:modal.trigger name="confirm-deletion-{{ $transaction->id }}">
                                <flux:button
                                    :title="__('wallets.delete', ['name' => $transaction->title])"
                                    variant="danger"
                                    class="cursor-pointer"
                                    icon="trash"
                                ></flux:button>
                            </flux:modal.trigger>

                            <flux:modal name="confirm-deletion-{{ $transaction->id }}" :show="$errors->isNotEmpty()" focusable class="max-w-lg">
                                <form wire:submit="delete({{ $transaction->id }})" class="space-y-6">
                                    <div>
                                        <flux:heading size="lg">{{ __('recurring_transactions.confirm_delete') }}</flux:heading>

                                        <flux:subheading>
                                            {{ __('recurring_transactions.confirm_delete_desc') }}
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
                        @endcan
                    </div>
                </div>
            </div>
        @empty
            <x-empty icon="calendar-clock" :description="__('recurring_transactions.empty')" />
        @endforelse
    </div>

    <div class="mt-4">
        {{ $transactions->links() }}
    </div>
</div>

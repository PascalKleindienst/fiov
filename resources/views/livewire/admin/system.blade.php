<div class="space-y-6">
    <x-slot name="heading">
        <div>
            <flux:heading size="xl" level="1">{{ __('system.index') }}</flux:heading>
            <flux:text>{{ __('system.index_description') }}</flux:text>
        </div>
    </x-slot>

    @if ($errors)
        <flux:callout variant="danger" icon="x-circle" :heading="__('system.alerts.error', ['path' => base_path('storage/logs/laravel.log')])" />
    @elseif (! $valid)
        <flux:callout variant="warning" icon="exclamation-triangle" :heading="__('system.alerts.warning')" />
    @else
        <flux:callout variant="success" icon="check-circle" :heading="__('system.alerts.success')" />
    @endif

    <x-table>
        <x-table.columns>
            <x-table.column>{{ __('system.fields.item') }}</x-table.column>
            <x-table.column>{{ __('system.fields.value') }}</x-table.column>
            <x-table.column>{{ __('system.fields.status') }}</x-table.column>
        </x-table.columns>
        <x-table.rows>
            <x-table.row>
                <x-table.cell>{{ __('system.php_version') }}</x-table.cell>
                <x-table.cell>{{ $status['php_version']->version }}</x-table.cell>
                <x-table.cell>
                    @if ($status['php_version']->valid)
                        <flux:icon name="check-circle" color="green" variant="solid" />
                        <span class="sr-only">{{ __('general.status.okay') }}</span>
                    @else
                        <flux:icon name="x-circle" color="red" variant="solid" />
                        <div class="sr-only">{{ __('general.status.error') }}</div>
                    @endif
                </x-table.cell>
            </x-table.row>
            <x-table.row>
                <x-table.cell>{{ __('system.node_version') }}</x-table.cell>
                <x-table.cell>{{ $status['node_version']->version }}</x-table.cell>
                <x-table.cell>
                    @if ($status['node_version']->valid)
                        <flux:icon name="check-circle" color="green" variant="solid" />
                        <span class="sr-only">{{ __('general.status.okay') }}</span>
                    @else
                        <flux:icon name="exclamation-triangle" color="orange" variant="solid" />
                        <div class="sr-only">{{ __('general.status.warning') }}</div>
                    @endif
                </x-table.cell>
            </x-table.row>
            <x-table.row>
                <x-table.cell>{{ __('system.npm_version') }}</x-table.cell>
                <x-table.cell>{{ $status['npm_version']->version }}</x-table.cell>
                <x-table.cell>
                    @if ($status['npm_version']->valid)
                        <flux:icon name="check-circle" color="green" variant="solid" />
                        <span class="sr-only">{{ __('general.status.okay') }}</span>
                    @else
                        <flux:icon name="exclamation-triangle" color="orange" variant="solid" />
                        <div class="sr-only">{{ __('general.status.warning') }}</div>
                    @endif
                </x-table.cell>
            </x-table.row>
            <x-table.row>
                <x-table.cell>{{ __('system.mail_configuration') }}</x-table.cell>
                <x-table.cell></x-table.cell>
                <x-table.cell>
                    @if ($status['mail_configuration'])
                        <flux:icon name="check-circle" color="green" variant="solid" />
                        <span class="sr-only">{{ __('general.status.okay') }}</span>
                    @else
                        <flux:icon name="x-circle" color="red" variant="solid" />
                        <div class="sr-only">{{ __('general.status.error') }}</div>
                    @endif
                </x-table.cell>
            </x-table.row>
            @foreach ($status['permissions'] as $permission => $value)
                <x-table.row>
                    <x-table.cell>{{ __('system.directory_permission', ['item' => $permission]) }}</x-table.cell>
                    <x-table.cell><code class="font-mono text-sm">{{ $value['path'] }}</code></x-table.cell>
                    <x-table.cell>
                        @if ($value['valid'])
                            <flux:icon name="check-circle" color="green" variant="solid" />
                            <span class="sr-only">{{ __('general.status.okay') }}</span>
                        @else
                            <flux:icon name="x-circle" color="red" variant="solid" />
                            <div class="sr-only">{{ __('general.status.error') }}</div>
                        @endif
                    </x-table.cell>
                </x-table.row>
            @endforeach
        </x-table.rows>
    </x-table>
</div>

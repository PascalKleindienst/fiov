@php
    /**
    * @property \App\Data\Chart $chart
    */
@endphp

<div class="space-y-4">
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <flux:heading size="lg" level="3">{{ $this->chart->name }} : {{ money($this->chart->total(), $this->chart->currency) }}</flux:heading>
            <div
                @class([
                    'flex items-center gap-2',
                    'text-red-600 dark:text-red-500' => $this->chart->growth() < 0,
                    'text-green-600 dark:text-green-500' => $this->chart->growth() > 0,
                ])
            >
                <flux:icon variant="micro" :name="($this->chart->growth() < 0) ? 'arrow-trending-down' : 'arrow-trending-up'" />
                <span class="text-sm">{{ \Illuminate\Support\Number::percentage($this->chart->growth()) }}</span>
            </div>
        </div>
        <div class="w-auto">
            <flux:select wire:model.change="interval">
                <flux:select.option value="day">{{ __('charts.day') }}</flux:select.option>
                <flux:select.option value="week">{{ __('charts.week') }}</flux:select.option>
                <flux:select.option value="month">{{ __('charts.month') }}</flux:select.option>
                <flux:select.option value="year">{{ __('charts.year') }}</flux:select.option>
            </flux:select>
        </div>
    </div>

    <div id="chart-{{ Str::uuid() }}" x-data="chart({{ json_encode($this->chart) }})" data-currency="{{ $this->chart->currency }}"></div>
</div>

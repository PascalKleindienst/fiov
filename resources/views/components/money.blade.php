@props([
    'amount',
    'currency' => null,
])

@php
    $money = $amount instanceof \Cknow\Money\Money ? $amount : money($amount, $currency);
@endphp

<span class="font-mono">
    {{ $money }}
</span>

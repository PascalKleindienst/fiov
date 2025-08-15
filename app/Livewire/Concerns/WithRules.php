<?php

declare(strict_types=1);

namespace App\Livewire\Concerns;

use Illuminate\Support\Arr;
use Livewire\Features\SupportFormObjects\SupportFormObjects;

/**
 * @mixin SupportFormObjects
 */
trait WithRules
{
    public function addRule(): void
    {
        $this->form->rules[] = [
            'operator' => null,
            'field' => null,
            'value' => null,
        ];
    }

    public function removeRule(int $index): void
    {
        Arr::forget($this->form->rules, $index);
    }
}

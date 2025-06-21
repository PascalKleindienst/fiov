<?php

declare(strict_types=1);

namespace App\Concerns;

use Illuminate\Database\Eloquent\Model;
use Livewire\Features\SupportFormObjects\SupportFormObjects;

/**
 * @template TModel of Model
 *
 * @mixin SupportFormObjects
 */
trait WithModel
{
    public ?Model $model = null;

    public function setModel(?Model $model): void
    {
        if ($model instanceof \Illuminate\Database\Eloquent\Model) {
            $this->model = $model;
            $this->fill($this->model->toArray());
        }
    }
}

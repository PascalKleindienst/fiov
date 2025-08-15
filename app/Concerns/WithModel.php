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
    /** @var TModel|null */
    public ?Model $model = null;

    public function setModel(?Model $model): void
    {
        if ($model instanceof \Illuminate\Database\Eloquent\Model) {
            /** @var TModel $model */
            $this->model = $model;
            $this->fill($this->model->toArray());
        }
    }
}

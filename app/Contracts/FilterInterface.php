<?php

declare(strict_types=1);

namespace App\Contracts;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * @template TModel of Model
 */
interface FilterInterface
{
    /**
     * @param  Builder<TModel>  $query
     * @return Builder<TModel>
     */
    public function __invoke(Builder $query): Builder;
}

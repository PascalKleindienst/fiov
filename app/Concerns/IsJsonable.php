<?php

declare(strict_types=1);

namespace App\Concerns;

use Illuminate\Contracts\Support\Arrayable;
use JsonException;

trait IsJsonable
{
    /**
     * @throws JsonException
     */
    public function toJson(int $options = 0): string
    {
        return json_encode($this instanceof Arrayable ? $this->toArray() : $this, $options | JSON_THROW_ON_ERROR);
    }
}

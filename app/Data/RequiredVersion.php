<?php

declare(strict_types=1);

namespace App\Data;

final readonly class RequiredVersion
{
    public bool $valid;

    public function __construct(public string $required, public ?string $version = null)
    {
        $this->valid = version_compare($this->version ?? '', $this->required, '>=');
    }
}

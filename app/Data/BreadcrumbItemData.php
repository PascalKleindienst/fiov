<?php

declare(strict_types=1);

namespace App\Data;

final readonly class BreadcrumbItemData
{
    public function __construct(
        public string $title,
        public ?string $url = null,
    ) {}
}

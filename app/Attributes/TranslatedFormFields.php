<?php

declare(strict_types=1);

namespace App\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
final readonly class TranslatedFormFields
{
    public function __construct(public string $prefix) {}
}

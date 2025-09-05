<?php

declare(strict_types=1);

namespace App\Attributes;

use Attribute;
use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Container\ContextualAttribute;

#[Attribute(Attribute::TARGET_CLASS)]
final readonly class TranslatedFormFields implements ContextualAttribute
{
    public function __construct(public string $prefix) {}

    public function resolve(self $attribute, Container $container): self
    {
        return $attribute;
    }
}

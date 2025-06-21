<?php

declare(strict_types=1);

namespace App\Concerns;

use App\Attributes\TranslatedFormFields;
use ReflectionClass;

/**
 * @mixin \Livewire\Component
 */
trait WithTranslatedFields
{
    /**
     * @return array<array-key, string>
     */
    protected function validationAttributes(): array
    {
        $attributes = new ReflectionClass($this)->getAttributes();
        $prefix = 'validation.';

        if ($attributes !== []) {
            $translatedFormFields = $attributes[0]->newInstance();

            if ($translatedFormFields instanceof TranslatedFormFields) {
                $prefix = $translatedFormFields->prefix;
            }
        }

        $messages = [];

        foreach (array_keys($this->all()) as $key) {
            $messages[$key] = (string) __($prefix.$key);
        }

        return $messages;
    }
}

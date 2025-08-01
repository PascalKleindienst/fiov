<?php

declare(strict_types=1);

namespace App\Concerns;

use App\Attributes\TranslatedFormFields;
use Livewire\Form;
use ReflectionClass;

/**
 * @mixin Form
 */
trait WithTranslatedFields
{
    /**
     * @return array<array-key, string>
     */
    protected function validationAttributes(): array
    {
        $form = new ReflectionClass($this);
        $attributes = $form->getAttributes();
        $prefix = 'validation.';

        if ($attributes !== []) {
            $translatedFormFields = $attributes[0]->newInstance();

            if ($translatedFormFields instanceof TranslatedFormFields) {
                $prefix = $translatedFormFields->prefix;
            }
        }

        $messages = [];
        foreach (array_keys($this->all()) as $key) {
            $label = $prefix.$key;

            foreach ($this->validationAttributesFromOutside as $field => $attributes) {
                if ($attributes[$key] ?? null) {
                    $label = $prefix.$attributes[$key];
                    unset($this->validationAttributesFromOutside[$field][$key]);
                    break;
                }
            }

            $messages[$key] = (string) __($label);
        }

        return $messages;
    }
}

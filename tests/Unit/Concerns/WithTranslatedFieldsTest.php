<?php

declare(strict_types=1);

use App\Concerns\WithTranslatedFields;
use Livewire\Attributes\Validate;

final class TestComponent extends \Livewire\Component
{
    use WithTranslatedFields;

    #[Validate('required')]
    public $foo;

    public function save(): void
    {
        $this->validate();
    }

    public function render(): string
    {
        return '<div>SOME VIEW</div>';
    }
}

#[\App\Attributes\TranslatedFormFields('custom.')]
final class CustomTestComponent extends \Livewire\Component
{
    use WithTranslatedFields;

    #[Validate('required')]
    public $foo;

    public function save(): void
    {
        $this->validate();
    }

    public function render(): string
    {
        return '<div>SOME VIEW</div>';
    }
}

it('returns validation attributes with default prefix', function (): void {
    $component = Livewire::test(TestComponent::class)
        ->call('save')
        ->assertHasErrors('foo');

    expect($component->errors()->get('foo')[0])->toContain(__('validation.required', ['attribute' => 'validation.foo']));
});

it('returns validation attributes with custom_prefix', function (): void {
    $component = Livewire::test(CustomTestComponent::class)
        ->call('save')
        ->assertHasErrors('foo');

    expect($component->errors()->get('foo')[0])->toContain(__('validation.required', ['attribute' => 'custom.foo']));
});

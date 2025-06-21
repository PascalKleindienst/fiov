<?php

declare(strict_types=1);


use App\Concerns\WithTranslatedFields;
use Livewire\Attributes\Validate;

class TestComponent extends \Livewire\Component {
    use WithTranslatedFields;

    #[Validate('required')]
    public $foo;

    public function save()
    {
        $this->validate();
    }

    public function render()
    {
        return '<div>SOME VIEW</div>';
    }
}

it('returns validation attributes with default prefix', function () {
     $component = Livewire::test(TestComponent::class)
         ->call('save')
         ->assertHasErrors('foo');

     expect($component->errors()->get('foo')[0])->toContain('Validation.foo');
});

it('returns validation attributes with custom_prefix', function () {
    #[\App\Attributes\TranslatedFormFields('custom.')]
    class CustomTestComponent extends TestComponent {}

    $component = Livewire::test(CustomTestComponent::class)
        ->call('save')
        ->assertHasErrors('foo');

    expect($component->errors()->get('foo')[0])->toContain('Custom.foo');
});

<?php

declare(strict_types=1);

namespace App\Livewire\Forms;

use App\Actions\CreateOrUpdateCategory;
use App\Attributes\TranslatedFormFields;
use App\Concerns\WithModel;
use App\Concerns\WithTranslatedFields;
use App\Enums\Color;
use App\Enums\Icon;
use App\Enums\RuleOperator;
use App\Models\WalletCategory;
use App\Models\WalletCategoryRule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Enum;
use Livewire\Attributes\Validate;
use Livewire\Form;

#[TranslatedFormFields('categories.fields.')]
final class WalletCategoryForm extends Form
{
    /** @use WithModel<WalletCategory> */
    use WithModel;

    use WithTranslatedFields;

    #[Validate(['required'])]
    public string $title = '';

    #[Validate]
    public ?string $color = null;

    #[Validate]
    public ?string $icon = null;

    /**
     * @var array<int, array{field: ?string, operator: ?string, value: ?string}>
     */
    #[Validate(
        [
            'rules' => ['sometimes', 'array'],
            'rules.*.field' => ['required', 'string'],
            'rules.*.value' => ['required', 'string'],
        ],
        as: [
            'rules.*.field' => 'categories.fields.rules.field',
            'rules.*.operator' => 'categories.fields.rules.operator',
            'rules.*.value' => 'categories.fields.rules.value',
        ]
    )]
    public array $rules = [];

    public function submit(): void
    {
        $this->validate();

        resolve(CreateOrUpdateCategory::class)->handle(
            ['id' => $this->model?->id, ...$this->except(['model', 'rules']), 'user_id' => Auth::id()],
            collect($this->rules)->map(fn (array $rule): WalletCategoryRule => new WalletCategoryRule($rule))
        );
    }

    /**
     * @return array<string, array<int, string|Enum>>
     */
    public function rules(): array
    {
        return [
            'color' => ['nullable', new Enum(Color::class)],
            'icon' => ['nullable', new Enum(Icon::class)],
            'rules.*.operator' => ['required', new Enum(RuleOperator::class)],
        ];
    }
}

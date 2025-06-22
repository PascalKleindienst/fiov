<?php

declare(strict_types=1);

namespace App\Livewire\Forms;

use App\Attributes\TranslatedFormFields;
use App\Concerns\WithModel;
use App\Concerns\WithTranslatedFields;
use App\Enums\Color;
use App\Enums\Icon;
use App\Models\WalletCategory;
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
     * @return array<string, array<int, string|Enum>>
     */
    public function rules(): array
    {
        return [
            'color' => ['nullable', new Enum(Color::class)],
            'icon' => ['nullable', new Enum(Icon::class)],
        ];
    }

    public function submit(): void
    {
        $this->validate();

        if ($this->model instanceof \Illuminate\Database\Eloquent\Model) {
            $this->model->update($this->except('model'));

            return;
        }

        WalletCategory::create([...$this->except('model'), 'user_id' => Auth::id()]);
    }
}

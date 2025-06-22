<?php

declare(strict_types=1);

namespace App\Livewire\Forms;

use App\Attributes\TranslatedFormFields;
use App\Concerns\WithModel;
use App\Concerns\WithTranslatedFields;
use App\Enums\Color;
use App\Enums\Currency;
use App\Enums\Icon;
use App\Models\Wallet;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Enum;
use Livewire\Attributes\Validate;
use Livewire\Form;

#[TranslatedFormFields('wallets.fields.')]
final class WalletForm extends Form
{
    /** @use WithModel<Wallet> */
    use WithModel;

    use WithTranslatedFields;

    #[Validate(['required'])]
    public string $title = '';

    #[Validate(['required'])]
    public string $description = '';

    #[Validate]
    public ?string $color = null;

    #[Validate]
    public ?string $icon = null;

    #[Validate(['required', 'currency'])]
    public string $currency = Currency::EUR->value;

    public function boot(): void
    {
        // $this->withValidator(function ($validator): void {
        //     $validator->after(function ($validator): void {
        //         // TODO: check capability
        //         if (Auth::user()->wallets()->count() > 1) {
        //             // $validator->errors()->add('capability', __('You can not add more than 1 wallet'));
        //         }
        //     });
        // });
    }

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

        if ($this->model instanceof \App\Models\Wallet) {
            $this->model->update($this->except('model'));

            return;
        }

        Wallet::create([...$this->except('model'), 'user_id' => Auth::id()]);
    }
}

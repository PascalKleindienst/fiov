<?php

declare(strict_types=1);

namespace App\Livewire\Forms;

use App\Actions\CreateTransaction;
use App\Attributes\TranslatedFormFields;
use App\Concerns\WithTranslatedFields;
use App\Enums\Icon;
use App\Enums\RecurringFrequency;
use App\Models\Wallet;
use App\Models\WalletCategory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Validation\Rules\Exists;
use Livewire\Attributes\Validate;
use Livewire\Form;

#[TranslatedFormFields('transactions.fields.')]
final class TransactionForm extends Form
{
    use WithTranslatedFields;

    #[Validate(['required'])]
    public string $title = '';

    #[Validate]
    public ?string $icon = null;

    #[Validate(['required', 'money'])]
    public float $amount;

    public bool $is_investment = false;

    #[Validate(as: 'category')]
    public ?int $wallet_category_id = null;

    #[Validate(as: 'wallet')]
    public ?int $wallet_id = null;

    #[Validate(['boolean'])]
    public bool $is_recurring = false;

    public ?string $recurring_frequency = null;

    public ?string $recurring_end_date = null;

    /**
     * @return array<string, array<int, string|Enum|Exists>>
     */
    public function rules(): array
    {
        return [
            'icon' => ['nullable', new Enum(Icon::class)],
            'wallet_category_id' => [
                'required',
                Rule::exists(WalletCategory::class, 'id')->where('user_id', Auth::id()),
            ],
            'wallet_id' => [
                'required',
                Rule::exists(Wallet::class, 'id')->where('user_id', Auth::id()),
            ],
            'recurring_frequency' => ['nullable', 'required_if:is_recurring,true', new Enum(RecurringFrequency::class)],
            'recurring_end_date' => ['nullable', 'date', 'after:today'],
        ];
    }

    public function submit(): void
    {
        $this->validate();

        resolve(CreateTransaction::class)->handle(
            $this->except(['is_recurring', 'recurring_frequency', 'recurring_end_date']),
            $this->only(['is_recurring', 'recurring_frequency', 'recurring_end_date']),
        );
    }
}

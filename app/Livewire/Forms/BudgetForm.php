<?php

declare(strict_types=1);

namespace App\Livewire\Forms;

use App\Attributes\TranslatedFormFields;
use App\Concerns\WithModel;
use App\Concerns\WithTranslatedFields;
use App\Enums\BudgetStatus;
use App\Enums\BudgetType;
use App\Enums\Priority;
use App\Models\Budget;
use App\Models\Wallet;
use Illuminate\Validation\Rules\Enum;
use Livewire\Attributes\Validate;
use Livewire\Form;

#[TranslatedFormFields('budgets.fields.')]
final class BudgetForm extends Form
{
    /** @use WithModel<Budget> */
    use WithModel;

    use WithTranslatedFields;

    public string $currency = 'EUR';

    public bool $isEdit = false;

    #[Validate(['required', 'string', 'max:255'])]
    public string $title = '';

    #[Validate(['nullable', 'string'])]
    public string $description = '';

    #[Validate(['required', new Enum(BudgetType::class)])]
    public BudgetType $type = BudgetType::Default;

    #[Validate(['required', new Enum(BudgetStatus::class)])]
    public BudgetStatus $status = BudgetStatus::Active;

    #[Validate(['required', 'numeric', 'money', 'min:0.01'])]
    public float $amount = 0;

    #[Validate(['required', 'date'])]
    public string $start_date = '';

    #[Validate(['nullable', 'date', 'after:start_date'])]
    public ?string $end_date = null;

    #[Validate(['required', 'exists:wallets,id'], as: 'wallet')]
    public ?int $wallet_id = null;

    /**
     * @var array<int, float>
     */
    #[Validate([
        'allocatedAmounts.*' => ['required', 'numeric', 'money', 'min:0.01'],
    ])]
    public array $allocatedAmounts = [];

    /**
     * @var int[]
     */
    #[Validate([
        'selectedCategories' => ['required', 'array', 'min:1'],
        'selectedCategories.*' => ['required', 'exists:wallet_categories,id'],
    ])]
    public array $selectedCategories = [];

    #[Validate(['nullable', new Enum(Priority::class)])]
    public Priority $priority = Priority::Low;

    public function submit(): void
    {
        $this->validate();

        // Prepare the categories data with their allocated amounts
        $categories = [];
        foreach ($this->selectedCategories as $categoryId) {
            $categories[$categoryId] = [
                'allocated_amount' => (float) ($this->allocatedAmounts[$categoryId] ?? 0),
            ];
        }

        // Save the budget
        $data = ['user_id' => auth()->id()] + $this->except(['amount', 'isEdit', 'model', 'allocatedAmounts', 'selectedCategories', 'currency']);

        if ($this->isEdit && $this->model) {
            $this->model->update($data);
            $budget = $this->model;
        } else {
            $budget = Budget::create($data);
        }

        $budget->categories()->sync($categories);
    }

    public function resetWallet(?Wallet $wallet): void
    {
        $this->currency = $wallet?->currency->value ?? $this->currency;
        $this->selectedCategories = [];
        $this->allocatedAmounts = [];
    }
}

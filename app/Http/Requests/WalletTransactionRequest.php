<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class WalletTransactionRequest extends FormRequest
{
    /**
     * @return array<string, string[]>
     */
    public function rules(): array
    {
        return [
            'title' => ['required'],
            'icon' => ['nullable'],
            'amount' => ['required', 'integer'],
            'currency' => ['nullable'],
            'is_investment' => ['boolean'],
            'user_id' => ['required', 'exists:users'],
            'wallet_category_id' => ['required', 'exists:wallet_categories'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}

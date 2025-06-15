<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class WalletCategoryRequest extends FormRequest
{
    /**
     * @return array<string, string[]>
     */
    public function rules(): array
    {
        return [
            'title' => ['required'],
            'icon' => ['nullable'],
            'color' => ['nullable'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}

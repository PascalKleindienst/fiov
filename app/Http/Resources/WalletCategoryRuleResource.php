<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\WalletCategoryRule;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin WalletCategoryRule */
final class WalletCategoryRuleResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'field' => $this->field,
            'operator' => $this->operator,
            'value' => $this->value,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            'wallet_category_id' => $this->wallet_category_id,

            'walletCategory' => new WalletCategoryResource($this->whenLoaded('walletCategory')),
        ];
    }
}

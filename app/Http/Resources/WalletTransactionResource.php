<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\WalletTransaction;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin WalletTransaction */
final class WalletTransactionResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'transaction_id' => $this->transaction_id,
            'title' => $this->title,
            'icon' => $this->icon,
            'amount' => $this->amount,
            'currency' => $this->currency,
            'is_investment' => $this->is_investment,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'is_spending' => $this->is_spending,

            'wallet_id' => $this->wallet_id,
            'wallet_category_id' => $this->wallet_category_id,

            'category' => new WalletCategoryResource($this->whenLoaded('category')),
            'wallet' => new WalletResource($this->whenLoaded('wallet')),
        ];
    }
}

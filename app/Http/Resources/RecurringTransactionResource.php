<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\RecurringTransaction;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin RecurringTransaction */
final class RecurringTransactionResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'icon' => $this->icon,
            'amount' => $this->amount,
            'currency' => $this->currency,
            'is_investment' => $this->is_investment,
            'frequency' => $this->frequency,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'last_processed_at' => $this->last_processed_at,
            'is_active' => $this->is_active,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            'user_id' => $this->user_id,
            'wallet_id' => $this->wallet_id,
            'wallet_category_id' => $this->wallet_category_id,

            'user' => new UserResource($this->whenLoaded('user')),
            'wallet' => new WalletResource($this->whenLoaded('wallet')),
            'walletCategory' => new WalletCategoryResource($this->whenLoaded('walletCategory')),
        ];
    }
}

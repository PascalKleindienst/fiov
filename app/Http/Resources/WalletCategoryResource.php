<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\WalletCategory;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin WalletCategory */
final class WalletCategoryResource extends JsonResource
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
            'color' => $this->color,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'transactions_count' => $this->whenCounted('transactions'),

            'user_id' => $this->user_id,

            'user' => $this->whenLoaded('user'),
            'transactions' => WalletTransactionResource::collection($this->whenLoaded('transactions')),
        ];
    }
}

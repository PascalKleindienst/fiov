<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin User */
final class UserResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'email_verified_at' => $this->email_verified_at,
            'password' => $this->password,
            'remember_token' => $this->remember_token,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'notifications_count' => $this->whenCounted('notifications'),
            'wallet_categories_count' => $this->whenCounted('wallet_categories'),
            'wallet_transactions_count' => $this->whenCounted('wallet_transactions'),
            'wallets_count' => $this->whenCounted('wallets'),
            'read_notifications_count' => $this->whenCounted('read_notifications'),
            'unread_notifications_count' => $this->whenCounted('unread_notifications'),

            'walletCategories' => WalletCategoryResource::collection($this->whenLoaded('walletCategories')),
            'walletTransactions' => WalletTransactionResource::collection($this->whenLoaded('walletTransactions')),
            'wallets' => WalletCollection::collection($this->whenLoaded('wallets')),
        ];
    }
}

<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Collection;

/** @see \App\Models\Wallet */
final class WalletCollection extends ResourceCollection
{
    /**
     * @return array{data: Collection<int, WalletResource>}
     */
    public function toArray(Request $request): array
    {
        return [
            'data' => $this->collection,
        ];
    }
}

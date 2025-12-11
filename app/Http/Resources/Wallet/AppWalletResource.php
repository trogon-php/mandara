<?php

namespace App\Http\Resources\Wallet;

use App\Http\Resources\BaseResource;
use Illuminate\Http\Request;

class AppWalletResource extends BaseResource
{
    public function __construct($resource)
    {
        parent::__construct($resource);
        $this->includeId = true;
    }

    protected function resourceFields(Request $request): array
    {
        return [
            'total_coins' => $this->total_coins,
            'transactions' => $this->transactions,
        ];
    }
}

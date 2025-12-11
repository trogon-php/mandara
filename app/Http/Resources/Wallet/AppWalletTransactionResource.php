<?php

namespace App\Http\Resources\Wallet;

use App\Http\Resources\BaseResource;
use Illuminate\Http\Request;

class AppWalletTransactionResource extends BaseResource
{
    public function __construct($resource)
    {
        parent::__construct($resource);
        $this->includeId = true;
    }

    protected function resourceFields(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->user->name,
            'profile_picture' => $this->user->profile_picture_url,
            'date' => $this->formatted_date,
            'amount' => $this->formatted_amount,
            'source' => $this->source_type,
        ];
    }
}

<?php

namespace App\Http\Resources\Wallet;

use App\Http\Resources\BaseResource;
use Illuminate\Http\Request;

class AppReferralResource extends BaseResource
{
    public function __construct($resource)
    {
        parent::__construct($resource);
        $this->includeId = true;
    }

    protected function resourceFields(Request $request): array
    {
        return [
            'referral_code' => $this->referral_code,
            'status' => $this->status,
            'reward_coins' => $this->reward_coins,
            'referred_user' => $this->when($this->referred, function () {
                return [
                    'id' => $this->referred->id,
                    'name' => $this->referred->name,
                    'profile_picture' => $this->referred->profile_picture_url,
                ];
            }),
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
        ];
    }
}

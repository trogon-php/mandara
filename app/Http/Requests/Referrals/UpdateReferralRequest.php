<?php

namespace App\Http\Requests\Referrals;

use Illuminate\Foundation\Http\FormRequest;

class UpdateReferralRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'status' => 'required|in:pending,completed,rewarded,cancelled',
            'reward_coins' => 'required|integer|min:0',
        ];
    }

    /**
     * Get custom attribute names
     */
    public function attributes(): array
    {
        return [
            'status' => 'Status',
            'reward_coins' => 'Reward Coins',
        ];
    }
}




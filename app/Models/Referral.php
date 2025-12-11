<?php

namespace App\Models;

use App\Models\BaseModel;

class Referral extends BaseModel
{
    protected $casts = [
        'referrer_id' => 'integer',
        'referred_id' => 'integer',
        'reward_coins' => 'integer',
        'reward_transaction_id' => 'integer',
    ];

    /**
     * Relationship with User (referrer)
     */
    public function referrer()
    {
        return $this->belongsTo(User::class, 'referrer_id');
    }

    /**
     * Relationship with User (referred)
     */
    public function referred()
    {
        return $this->belongsTo(User::class, 'referred_id');
    }

    /**
     * Relationship with WalletTransaction
     */
    public function rewardTransaction()
    {
        return $this->belongsTo(WalletTransaction::class, 'reward_transaction_id');
    }

    /**
     * Scope for completed referrals
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope for rewarded referrals
     */
    public function scopeRewarded($query)
    {
        return $query->where('status', 'rewarded');
    }

    /**
     * Scope for pending referrals
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Generate unique referral code
     */
    public static function generateReferralCode()
    {
        do {
            $code = strtoupper(substr(md5(uniqid()), 0, 8));
        } while (static::where('referral_code', $code)->exists());

        return $code;
    }

    /**
     * Create referral for user
     */
    public static function createForUser($userId)
    {
        return static::create([
            'referrer_id' => $userId,
            'referral_code' => static::generateReferralCode(),
            'status' => 'pending',
            'reward_coins' => 5, // Default reward amount
        ]);
    }

    /**
     * Complete referral when someone uses the code
     */
    public function complete($referredUserId)
    {
        $this->update([
            'referred_id' => $referredUserId,
            'status' => 'completed',
        ]);

        return $this;
    }

    /**
     * Reward the referrer
     */
    public function reward()
    {
        if ($this->status !== 'completed') {
            throw new \Exception('Cannot reward incomplete referral');
        }

        $wallet = Wallet::getOrCreateForUser($this->referrer_id);
        $transaction = $wallet->addCoins(
            $this->reward_coins,
            'referral',
            $this->id,
            "Referral reward for {$this->referred->name}"
        );

        $this->update([
            'status' => 'rewarded',
            'reward_transaction_id' => $transaction->id,
        ]);

        return $transaction;
    }
}

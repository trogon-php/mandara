<?php

namespace App\Models;

use App\Models\BaseModel;

class Wallet extends BaseModel
{
    protected $casts = [
        'user_id' => 'integer',
        'balance' => 'integer',
    ];

    /**
     * Relationship with User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relationship with WalletTransactions
     */
    public function transactions()
    {
        return $this->hasMany(WalletTransaction::class);
    }

    /**
     * Get recent transactions
     */
    public function recentTransactions($limit = 10)
    {
        return $this->transactions()
            ->with(['user'])
            ->orderBy('created_at', 'desc')
            ->limit($limit);
    }

    /**
     * Get transactions by source type
     */
    public function transactionsBySource($sourceType)
    {
        return $this->transactions()
            ->where('source_type', $sourceType)
            ->with(['user'])
            ->orderBy('created_at', 'desc');
    }

    /**
     * Get or create wallet for user
     */
    public static function getOrCreateForUser($userId)
    {
        return static::firstOrCreate(
            ['user_id' => $userId],
            ['balance' => 0]
        );
    }

    /**
     * Add coins to wallet
     */
    public function addCoins($amount, $sourceType, $sourceId = null, $description = null)
    {
        $this->balance += $amount;
        $this->save();

        // Create transaction record
        return $this->transactions()->create([
            'user_id' => $this->user_id,
            'amount' => $amount,
            'balance_after' => $this->balance,
            'type' => 'credit',
            'source_type' => $sourceType,
            'source_id' => $sourceId,
            'description' => $description,
        ]);
    }

    /**
     * Deduct coins from wallet
     */
    public function deductCoins($amount, $sourceType, $sourceId = null, $description = null)
    {
        if ($this->balance < $amount) {
            throw new \Exception('Insufficient balance');
        }

        $this->balance -= $amount;
        $this->save();

        // Create transaction record
        return $this->transactions()->create([
            'user_id' => $this->user_id,
            'amount' => -$amount,
            'balance_after' => $this->balance,
            'type' => 'debit',
            'source_type' => $sourceType,
            'source_id' => $sourceId,
            'description' => $description,
        ]);
    }
}

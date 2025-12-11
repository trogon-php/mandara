<?php

namespace App\Models;

use App\Models\BaseModel;

class WalletTransaction extends BaseModel
{
    protected $casts = [
        'wallet_id' => 'integer',
        'user_id' => 'integer',
        'amount' => 'integer',
        'balance_after' => 'integer',
        'source_id' => 'integer',
    ];

    /**
     * Relationship with Wallet
     */
    public function wallet()
    {
        return $this->belongsTo(Wallet::class);
    }

    /**
     * Relationship with User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get formatted date for display
     */
    public function getFormattedDateAttribute()
    {
        return $this->created_at->format('d M, Y');
    }

    /**
     * Get formatted amount with sign
     */
    public function getFormattedAmountAttribute()
    {
        return $this->amount > 0 ? '+' . $this->amount : (string) $this->amount;
    }

    /**
     * Scope for credit transactions
     */
    public function scopeCredits($query)
    {
        return $query->where('type', 'credit');
    }

    /**
     * Scope for debit transactions
     */
    public function scopeDebits($query)
    {
        return $query->where('type', 'debit');
    }

    /**
     * Scope for specific source type
     */
    public function scopeBySource($query, $sourceType)
    {
        return $query->where('source_type', $sourceType);
    }

    /**
     * Scope for recent transactions
     */
    public function scopeRecent($query, $limit = 10)
    {
        return $query->orderBy('created_at', 'desc')->limit($limit);
    }
}

<?php

namespace App\Services\Wallet;

use App\Models\Wallet;
use App\Models\WalletTransaction;
use App\Models\Referral;
use App\Models\User;
use App\Services\App\AppBaseService;
use Illuminate\Support\Collection;

class WalletService extends AppBaseService
{
    protected string $modelClass = Wallet::class;

    public function __construct()
    {
        $this->clearCache();
    }

    /**
     * Get wallet data for API response
     */
    public function getWalletData(): array
    {
        $user = $this->getAuthUser();
        $wallet = Wallet::getOrCreateForUser($user->id);
        
        // Get recent transactions with user details
        $transactions = $wallet->transactions()
            ->with(['user'])
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get();

        return [
            'total_coins' => $wallet->balance,
            'transactions' => $transactions->map(function ($transaction) {
                return [
                    'id' => $transaction->id,
                    'name' => $transaction->user->name,
                    'profile_picture' => $transaction->user->profile_picture_url,
                    'date' => $transaction->formatted_date,
                    'amount' => $transaction->formatted_amount,
                    'source' => $transaction->source_type,
                ];
            })->toArray()
        ];
    }

    /**
     * Get wallet transactions with pagination
     */
    public function getTransactions($perPage = 15)
    {
        $user = $this->getAuthUser();
        $wallet = Wallet::getOrCreateForUser($user->id);
        
        return $wallet->transactions()
            ->with(['user'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }


    /**
     * Get filter configuration for admin
     */
    public function getFilterConfig(): array
    {
        return [
            'source_type' => [
                'type' => 'exact',
                'label' => 'Source Type',
                'col' => 3,
                'options' => [
                    'referral' => 'Referral',
                    'course' => 'Course',
                    'package' => 'Package',
                    'exam' => 'Exam',
                    'admin_adjustment' => 'Admin Adjustment',
                ],
            ],
            'type' => [
                'type' => 'exact',
                'label' => 'Transaction Type',
                'col' => 3,
                'options' => [
                    'credit' => 'Credit',
                    'debit' => 'Debit',
                ],
            ],
        ];
    }

    /**
     * Get search fields configuration
     */
    public function getSearchFieldsConfig(): array
    {
        return [
            'description' => 'Description',
            'source_type' => 'Source Type',
        ];
    }

    /**
     * Get default search fields
     */
    public function getDefaultSearchFields(): array
    {
        return ['description', 'source_type'];
    }

    /**
     * Get default sorting
     */
    public function getDefaultSorting(): array
    {
        return ['field' => 'created_at', 'direction' => 'desc'];
    }
}

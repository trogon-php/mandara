<?php

namespace App\Http\Controllers\Api;

use App\Services\Wallet\WalletService;
use App\Http\Resources\Wallet\AppWalletResource;
use App\Http\Resources\Wallet\AppWalletTransactionResource;
use App\Http\Resources\Wallet\AppReferralResource;
use Illuminate\Http\Request;

class WalletController extends BaseApiController
{
    public function __construct(protected WalletService $walletService) {}

    /**
     * Get wallet data with transactions
     */
    public function index()
    {
        $data = $this->walletService->getWalletData();

        return $this->respondSuccess(
            $data,
            'Wallet data fetched successfully'
        );
    }

    /**
     * Get wallet transactions with pagination
     */
    public function transactions(Request $request)
    {
        $perPage = $request->get('per_page', 15);
        
        $transactions = $this->walletService->getTransactions($perPage);
        
        $transactions->through(function ($transaction) {
            return new AppWalletTransactionResource($transaction);
        });

        return $this->respondPaginated($transactions, 'Transactions fetched successfully');
    }

}

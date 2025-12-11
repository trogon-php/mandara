<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use App\Models\Referral;
use Carbon\Carbon;

class User520WalletSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Creating wallet data for User ID 520...');

        // Check if user exists
        $user = User::find(520);
        if (!$user) {
            $this->command->error('User with ID 520 not found!');
            return;
        }

        $this->command->info("Found user: {$user->name}");

        // Create wallet for user 520
        $this->createWalletForUser($user);

        // Create referral data for user 520
        $this->createReferralData($user);

        $this->command->info('Wallet data for User ID 520 created successfully!');
    }

    /**
     * Create wallet and transactions for user 520
     */
    private function createWalletForUser(User $user): void
    {
        // Delete existing wallet and transactions if they exist
        if ($user->wallet) {
            $user->wallet->transactions()->delete();
            $user->wallet->delete();
        }

        // Use updateOrCreate to handle any race conditions
        $wallet = Wallet::updateOrCreate(
            ['user_id' => $user->id],
            ['balance' => 0]
        );

        $this->command->info("Created/Updated wallet for user: {$user->name}");

        // Generate specific transactions for user 520
        $this->generateSpecificTransactions($wallet);

        $this->command->info("Generated transactions for user: {$user->name}");
    }

    /**
     * Generate specific transactions for user 520
     */
    private function generateSpecificTransactions(Wallet $wallet): void
    {
        // Create referred users for the transactions
        $referredUsers = $this->createReferredUsers();

        $transactions = [
            // Referral rewards with referred user details
            [
                'amount' => 5,
                'source_type' => 'referral',
                'referred_user' => $referredUsers[0],
                'created_at' => Carbon::parse('2025-09-12 10:30:00'),
            ],
            [
                'amount' => 5,
                'source_type' => 'referral',
                'referred_user' => $referredUsers[1],
                'created_at' => Carbon::parse('2025-09-10 14:15:00'),
            ],
            [
                'amount' => 5,
                'source_type' => 'referral',
                'referred_user' => $referredUsers[2],
                'created_at' => Carbon::parse('2025-09-08 09:45:00'),
            ],
            [
                'amount' => 5,
                'source_type' => 'referral',
                'referred_user' => $referredUsers[3],
                'created_at' => Carbon::parse('2025-09-05 16:20:00'),
            ],
            [
                'amount' => 5,
                'source_type' => 'referral',
                'referred_user' => $referredUsers[4],
                'created_at' => Carbon::parse('2025-09-03 11:10:00'),
            ],
            [
                'amount' => 5,
                'source_type' => 'referral',
                'referred_user' => $referredUsers[5],
                'created_at' => Carbon::parse('2025-09-01 13:25:00'),
            ],
            [
                'amount' => 5,
                'source_type' => 'referral',
                'referred_user' => $referredUsers[6],
                'created_at' => Carbon::parse('2025-08-28 15:40:00'),
            ],
            [
                'amount' => 5,
                'source_type' => 'referral',
                'referred_user' => $referredUsers[7],
                'created_at' => Carbon::parse('2025-08-25 12:55:00'),
            ],
            [
                'amount' => 5,
                'source_type' => 'referral',
                'referred_user' => $referredUsers[8],
                'created_at' => Carbon::parse('2025-08-22 08:30:00'),
            ],
            [
                'amount' => 5,
                'source_type' => 'referral',
                'referred_user' => $referredUsers[9],
                'created_at' => Carbon::parse('2025-08-20 17:15:00'),
            ],
            [
                'amount' => 5,
                'source_type' => 'referral',
                'referred_user' => $referredUsers[10],
                'created_at' => Carbon::parse('2025-08-18 14:45:00'),
            ],
            [
                'amount' => 5,
                'source_type' => 'referral',
                'referred_user' => $referredUsers[11],
                'created_at' => Carbon::parse('2025-08-15 10:20:00'),
            ],
            [
                'amount' => 5,
                'source_type' => 'referral',
                'referred_user' => $referredUsers[12],
                'created_at' => Carbon::parse('2025-08-12 16:35:00'),
            ],
            [
                'amount' => 5,
                'source_type' => 'referral',
                'referred_user' => $referredUsers[13],
                'created_at' => Carbon::parse('2025-08-10 09:50:00'),
            ],
            [
                'amount' => 5,
                'source_type' => 'referral',
                'referred_user' => $referredUsers[14],
                'created_at' => Carbon::parse('2025-08-08 13:40:00'),
            ],
            [
                'amount' => 5,
                'source_type' => 'referral',
                'referred_user' => $referredUsers[15],
                'created_at' => Carbon::parse('2025-08-05 11:25:00'),
            ],
            [
                'amount' => 5,
                'source_type' => 'referral',
                'referred_user' => $referredUsers[16],
                'created_at' => Carbon::parse('2025-08-03 15:10:00'),
            ],
            [
                'amount' => 5,
                'source_type' => 'referral',
                'referred_user' => $referredUsers[17],
                'created_at' => Carbon::parse('2025-08-01 12:30:00'),
            ],
            [
                'amount' => 5,
                'source_type' => 'referral',
                'referred_user' => $referredUsers[18],
                'created_at' => Carbon::parse('2025-07-29 14:15:00'),
            ],
            [
                'amount' => 5,
                'source_type' => 'referral',
                'referred_user' => $referredUsers[19],
                'created_at' => Carbon::parse('2025-07-27 16:45:00'),
            ],
            [
                'amount' => 5,
                'source_type' => 'referral',
                'referred_user' => $referredUsers[20],
                'created_at' => Carbon::parse('2025-07-25 10:20:00'),
            ],
            [
                'amount' => 5,
                'source_type' => 'referral',
                'referred_user' => $referredUsers[21],
                'created_at' => Carbon::parse('2025-07-23 13:50:00'),
            ],
        ];

        $currentBalance = 0;

        foreach ($transactions as $transactionData) {
            $currentBalance += $transactionData['amount'];

            WalletTransaction::create([
                'wallet_id' => $wallet->id,
                'user_id' => $transactionData['referred_user']->id, // Use referred user's ID
                'amount' => $transactionData['amount'],
                'balance_after' => $currentBalance,
                'type' => 'credit',
                'source_type' => $transactionData['source_type'],
                'source_id' => rand(1, 100),
                'created_at' => $transactionData['created_at'],
            ]);
        }

        // Update wallet balance
        $wallet->update(['balance' => $currentBalance]);
        
        $this->command->info("Total balance: {$currentBalance} coins");
    }

    /**
     * Create referred users for transactions
     */
    private function createReferredUsers(): array
    {
        $names = [
            'Martin Lubin', 'Jaylon Baptista', 'Sarah Johnson', 'Mike Wilson', 'Emma Davis',
            'Alex Brown', 'Lisa Garcia', 'Tom Miller', 'Anna Taylor', 'Chris Anderson',
            'Maria Rodriguez', 'David Lee', 'Jennifer White', 'Robert Harris', 'Michelle Clark',
            'James Lewis', 'Amanda Walker', 'Kevin Hall', 'Nicole Allen', 'Daniel Young',
            'Stephanie King', 'Matthew Wright'
        ];

        $users = [];
        foreach ($names as $name) {
            $user = User::create([
                'name' => $name,
                'email' => strtolower(str_replace(' ', '.', $name)) . '@example.com',
                'phone' => rand(1000000000, 9999999999),
                'country_code' => '+1',
                'status' => 'active',
                'role_id' => 1, // Student role
                'password' => bcrypt('password123'),
                'created_at' => now(),
            ]);
            $users[] = $user;
        }

        return $users;
    }

    /**
     * Create referral data for user 520
     */
    private function createReferralData(User $user): void
    {
        // Create multiple referrals for user 520
        $referrals = [
            [
                'referral_code' => 'REF520001',
                'status' => 'rewarded',
                'reward_coins' => 5,
                'created_at' => Carbon::parse('2025-07-20 10:00:00'),
            ],
            [
                'referral_code' => 'REF520002',
                'status' => 'rewarded',
                'reward_coins' => 5,
                'created_at' => Carbon::parse('2025-07-22 14:30:00'),
            ],
            [
                'referral_code' => 'REF520003',
                'status' => 'completed',
                'reward_coins' => 5,
                'created_at' => Carbon::parse('2025-07-25 09:15:00'),
            ],
            [
                'referral_code' => 'REF520004',
                'status' => 'pending',
                'reward_coins' => 5,
                'created_at' => Carbon::parse('2025-07-28 16:45:00'),
            ],
            [
                'referral_code' => 'REF520005',
                'status' => 'pending',
                'reward_coins' => 5,
                'created_at' => Carbon::parse('2025-07-30 11:20:00'),
            ],
        ];

        foreach ($referrals as $referralData) {
            Referral::create([
                'referrer_id' => $user->id,
                'referral_code' => $referralData['referral_code'],
                'status' => $referralData['status'],
                'reward_coins' => $referralData['reward_coins'],
                'created_at' => $referralData['created_at'],
            ]);
        }

        $this->command->info("Created " . count($referrals) . " referrals for user: {$user->name}");
    }
}

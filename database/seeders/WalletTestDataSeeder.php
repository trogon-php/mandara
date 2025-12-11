<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use App\Models\Referral;
use Carbon\Carbon;

class WalletTestDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Creating wallet test data...');

        // Get some existing users or create test users
        $users = User::whereIn('role_id', [1, 2, 3])->limit(20)->get(); // Get users with any role
        
        if ($users->isEmpty()) {
            $this->command->error('No users found. Please run StudentTestDataSeeder first.');
            return;
        }

        // Create wallets for users
        foreach ($users as $user) {
            $this->createWalletForUser($user);
        }

        $this->command->info('Wallet test data seeding completed!');
    }

    /**
     * Create wallet and transactions for a user
     */
    private function createWalletForUser(User $user): void
    {
        // Create or get wallet
        $wallet = Wallet::firstOrCreate(
            ['user_id' => $user->id],
            ['balance' => 0]
        );

        // Generate random transactions
        $this->generateTransactions($wallet);

        // Create some referral data
        $this->createReferralData($user);

        $this->command->info("Created wallet data for user: {$user->name}");
    }

    /**
     * Generate random transactions for a wallet
     */
    private function generateTransactions(Wallet $wallet): void
    {
        $sourceTypes = ['referral', 'course', 'package', 'exam', 'admin_adjustment'];
        $transactionCount = rand(5, 15);
        $currentBalance = 0;

        for ($i = 0; $i < $transactionCount; $i++) {
            $sourceType = $sourceTypes[array_rand($sourceTypes)];
            $amount = $this->getRandomAmount($sourceType);
            $currentBalance += $amount;

            // Create transaction
            WalletTransaction::create([
                'wallet_id' => $wallet->id,
                'user_id' => $wallet->user_id,
                'amount' => $amount,
                'balance_after' => $currentBalance,
                'type' => $amount > 0 ? 'credit' : 'debit',
                'source_type' => $sourceType,
                'source_id' => rand(1, 100),
                'description' => $this->getTransactionDescription($sourceType, $amount),
                'created_at' => $this->getRandomDate(),
            ]);
        }

        // Update wallet balance
        $wallet->update(['balance' => $currentBalance]);
    }

    /**
     * Get random amount based on source type
     */
    private function getRandomAmount(string $sourceType): int
    {
        return match ($sourceType) {
            'referral' => rand(5, 10), // Referral rewards
            'course' => rand(-50, -10), // Course purchases (negative)
            'package' => rand(-100, -20), // Package purchases (negative)
            'exam' => rand(-30, -5), // Exam fees (negative)
            'admin_adjustment' => rand(-50, 50), // Admin adjustments (can be positive or negative)
            default => rand(-20, 20),
        };
    }

    /**
     * Get transaction description based on source type
     */
    private function getTransactionDescription(string $sourceType, int $amount): string
    {
        $descriptions = [
            'referral' => [
                'Referral reward for new user signup',
                'Bonus coins for successful referral',
                'Referral commission earned',
            ],
            'course' => [
                'Course enrollment fee',
                'Premium course purchase',
                'Course access payment',
            ],
            'package' => [
                'Study package purchase',
                'Premium package upgrade',
                'Package subscription fee',
            ],
            'exam' => [
                'Exam registration fee',
                'Certification exam payment',
                'Practice exam access',
            ],
            'admin_adjustment' => [
                'Admin balance adjustment',
                'System correction',
                'Manual balance update',
            ],
        ];

        $typeDescriptions = $descriptions[$sourceType] ?? ['Transaction'];
        return $typeDescriptions[array_rand($typeDescriptions)];
    }

    /**
     * Get random date within last 6 months
     */
    private function getRandomDate(): Carbon
    {
        $daysAgo = rand(0, 180); // Last 6 months
        $hoursAgo = rand(0, 23);
        $minutesAgo = rand(0, 59);
        
        return now()->subDays($daysAgo)->subHours($hoursAgo)->subMinutes($minutesAgo);
    }

    /**
     * Create referral data for some users
     */
    private function createReferralData(User $user): void
    {
        // 30% chance to create referral data
        if (rand(1, 100) <= 30) {
            $referral = Referral::create([
                'referrer_id' => $user->id,
                'referral_code' => $this->generateReferralCode(),
                'status' => $this->getRandomReferralStatus(),
                'reward_coins' => rand(5, 10),
                'created_at' => $this->getRandomDate(),
            ]);

            // If completed, add referred user
            if ($referral->status === 'completed') {
                $referredUser = User::where('id', '!=', $user->id)
                    ->where('role_id', 1)
                    ->inRandomOrder()
                    ->first();
                
                if ($referredUser) {
                    $referral->update(['referred_id' => $referredUser->id]);
                }
            }
        }
    }

    /**
     * Generate unique referral code
     */
    private function generateReferralCode(): string
    {
        do {
            $code = strtoupper(substr(md5(uniqid()), 0, 8));
        } while (Referral::where('referral_code', $code)->exists());

        return $code;
    }

    /**
     * Get random referral status
     */
    private function getRandomReferralStatus(): string
    {
        $statuses = ['pending', 'completed', 'rewarded'];
        $weights = [40, 35, 25]; // 40% pending, 35% completed, 25% rewarded
        
        $random = rand(1, 100);
        $cumulative = 0;
        
        foreach ($statuses as $index => $status) {
            $cumulative += $weights[$index];
            if ($random <= $cumulative) {
                return $status;
            }
        }
        
        return 'pending';
    }
}

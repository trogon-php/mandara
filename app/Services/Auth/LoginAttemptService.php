<?php

namespace App\Services\Auth;

use App\Models\LoginAttempt;
use Carbon\Carbon;

class LoginAttemptService
{
    /**
     * Record a login attempt
     */
    public function recordAttempt(array $data): LoginAttempt
    {
        return LoginAttempt::create($data);
    }

    /**
     * Mark login attempt as verified
     */
    public function markAsVerified(int $id): bool
    {
        return LoginAttempt::where('id', $id)->update(['status' => 'verified']);
    }

    /**
     * Mark login attempt as failed
     */
    public function markAsFailed(int $id): bool
    {
        return LoginAttempt::where('id', $id)->update(['status' => 'failed']);
    }

    /**
     * Get recent login attempts for a user
     */
    public function getRecentAttempts(string $identifier, int $limit = 5)
    {
        return LoginAttempt::where('identifier', $identifier)
            ->latest()
            ->limit($limit)
            ->get();
    }

    /**
     * Find login attempt by email
     */
    public function findByEmail(string $email)
    {
        return LoginAttempt::where('email', $email)
        ->where('status', 'pending')
        ->latest()
        ->first();
    }

    /**
     * Find login attempt by phone and country code
     */
    public function findByPhone(string $phone, string $countryCode)
    {
        return LoginAttempt::where('phone', $phone)
            ->where('country_code', $countryCode)
            ->where('status', 'pending')
            ->latest()
            ->first();
    }

    /**
     * Clean up expired attempts
     */
    public function cleanupExpired(): int
    {
        return LoginAttempt::where('status', 'pending')
            ->where('created_at', '<', Carbon::now()->subMinutes(10))
            ->update(['status' => 'expired']);
    }

    /**
     * Check if user has too many failed attempts
     */
    public function hasTooManyFailedAttempts(string $phone, string $countryCode, int $maxAttempts = 100): bool
    {
        $failedCount = LoginAttempt::where('phone', $phone)
            ->where('country_code', $countryCode)
            ->where('status', 'failed')
            ->where('created_at', '>', Carbon::now()->subMinutes(15))
            ->count();

        return $failedCount >= $maxAttempts;
    }
}

<?php

namespace App\Services\Auth;

use App\Models\Otp;
use Illuminate\Support\Facades\App;
use Carbon\Carbon;

class OtpService
{
    protected Otp $model;

    public function __construct()
    {
        $this->model = new Otp();
    }

    public function sendOtp(string $identifier, string $type, ?string $provider = null): bool
    {
        $otp = rand(100000, 999999);

        // resolve provider class
        $providerKey = $provider ?? config('otp.default');
        $providerClass = config('otp.providers')[$providerKey] ?? null;

        if (!$providerClass) {
            throw new \InvalidArgumentException("Invalid OTP provider: $providerKey");
        }

        $providerInstance = App::make($providerClass);

        // save OTP to DB
        $this->createOtp($identifier, $type, $otp, $providerKey);

        // send via provider
        return $providerInstance->sendOtp($identifier, $otp, $type);
    }

    public function verifyOtp(string $identifier, string $otp): bool
    {
        $record = $this->model->where('identifier', $identifier)
            ->where('otp_code', $otp)
            ->where('expires_at', '>', Carbon::now())
            ->whereNull('verified_at')
            ->latest()
            ->first();

        if (!$record) {
            return false;
        }

        $record->update(['verified_at' => Carbon::now()]);
        return true;
    }

    // OTP-specific methods
    public function createOtp(string $identifier, string $type, string $otp, string $provider): Otp
    {
        return $this->model->create([
            'identifier' => $identifier,
            'otp_code'   => $otp,
            'type'       => $type,
            'provider'   => $provider,
            'expires_at' => Carbon::now()->addMinutes(config('otp.expiry_minutes', 5)),
        ]);
    }

    public function getOtpByIdentifier(string $identifier): ?Otp
    {
        return $this->model->where('identifier', $identifier)
            ->latest()
            ->first();
    }

    public function getValidOtp(string $identifier, string $otp): ?Otp
    {
        return $this->model->where('identifier', $identifier)
            ->where('otp_code', $otp)
            ->where('expires_at', '>', Carbon::now())
            ->whereNull('verified_at')
            ->latest()
            ->first();
    }

    public function markAsVerified(string $identifier, string $otp): bool
    {
        $record = $this->getValidOtp($identifier, $otp);
        if (!$record) {
            return false;
        }

        return $record->update(['verified_at' => Carbon::now()]);
    }

    public function markAsExpired(string $identifier): bool
    {
        return $this->model->where('identifier', $identifier)
            ->whereNull('verified_at')
            ->where('expires_at', '>', Carbon::now())
            ->update(['expires_at' => Carbon::now()]);
    }

    public function getExpiredOtps()
    {
        return $this->model->where('expires_at', '<', Carbon::now())
            ->whereNull('verified_at')
            ->get();
    }

    public function getVerifiedOtps()
    {
        return $this->model->whereNotNull('verified_at')->get();
    }

    public function getOtpsByType(string $type)
    {
        return $this->model->where('type', $type)->get();
    }

    public function getOtpsByProvider(string $provider)
    {
        return $this->model->where('provider', $provider)->get();
    }

    public function getOtpsByIdentifier(string $identifier)
    {
        return $this->model->where('identifier', $identifier)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getRecentOtps(int $limit = 10)
    {
        return $this->model->latest()
            ->limit($limit)
            ->get();
    }

    public function getOtpsByDateRange($startDate, $endDate)
    {
        return $this->model->whereBetween('created_at', [$startDate, $endDate])
            ->get();
    }

    public function getOtpsCount(): int
    {
        return $this->model->count();
    }

    public function getVerifiedOtpsCount(): int
    {
        return $this->model->whereNotNull('verified_at')->count();
    }

    public function getExpiredOtpsCount(): int
    {
        return $this->model->where('expires_at', '<', Carbon::now())
            ->whereNull('verified_at')
            ->count();
    }

    public function getPendingOtpsCount(): int
    {
        return $this->model->where('expires_at', '>', Carbon::now())
            ->whereNull('verified_at')
            ->count();
    }

    public function getOtpsByTypeCount(string $type): int
    {
        return $this->model->where('type', $type)->count();
    }

    public function getOtpsByProviderCount(string $provider): int
    {
        return $this->model->where('provider', $provider)->count();
    }

    public function getOtpsByIdentifierCount(string $identifier): int
    {
        return $this->model->where('identifier', $identifier)->count();
    }

    public function getMostUsedProviders(int $limit = 5)
    {
        return $this->model->selectRaw('provider, COUNT(*) as otps_count')
            ->groupBy('provider')
            ->orderBy('otps_count', 'desc')
            ->limit($limit)
            ->get();
    }

    public function getMostUsedTypes(int $limit = 5)
    {
        return $this->model->selectRaw('type, COUNT(*) as otps_count')
            ->groupBy('type')
            ->orderBy('otps_count', 'desc')
            ->limit($limit)
            ->get();
    }

    public function getOtpsStatistics()
    {
        return [
            'total' => $this->getOtpsCount(),
            'verified' => $this->getVerifiedOtpsCount(),
            'expired' => $this->getExpiredOtpsCount(),
            'pending' => $this->getPendingOtpsCount(),
        ];
    }

    public function cleanupExpiredOtps(): int
    {
        return $this->model->where('expires_at', '<', Carbon::now())
            ->whereNull('verified_at')
            ->delete();
    }

    public function cleanupOldOtps(int $days = 30): int
    {
        return $this->model->where('created_at', '<', Carbon::now()->subDays($days))
            ->delete();
    }

    public function getOtpsByUser(string $identifier, int $limit = 10)
    {
        return $this->model->where('identifier', $identifier)
            ->latest()
            ->limit($limit)
            ->get();
    }

    public function getOtpsByUserCount(string $identifier): int
    {
        return $this->model->where('identifier', $identifier)->count();
    }

    public function getOtpsByUserVerifiedCount(string $identifier): int
    {
        return $this->model->where('identifier', $identifier)
            ->whereNotNull('verified_at')
            ->count();
    }

    public function getOtpsByUserFailedCount(string $identifier): int
    {
        return $this->model->where('identifier', $identifier)
            ->where('expires_at', '<', Carbon::now())
            ->whereNull('verified_at')
            ->count();
    }

    public function getOtpsByUserStatistics(string $identifier)
    {
        return [
            'total' => $this->getOtpsByUserCount($identifier),
            'verified' => $this->getOtpsByUserVerifiedCount($identifier),
            'failed' => $this->getOtpsByUserFailedCount($identifier),
        ];
    }
}

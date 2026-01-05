<?php

namespace App\Models;

use App\Models\BaseModel;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AmenityBooking extends BaseModel
{
    protected $casts = [
        'user_id' => 'integer',
        'amenity_id' => 'integer',
        'package_id' => 'integer',
        'booking_date' => 'date',
        'booking_time' => 'datetime',
        'end_time' => 'datetime',
        'booking_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'payable_amount' => 'decimal:2',
        'reminder_sent' => 'boolean',
        'reminder_sent_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    /**
     * Boot method to auto-generate booking number
     */
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($booking) {
            if (empty($booking->booking_number)) {
                $amenity = Amenity::find($booking->amenity_id);
                $prefix = $amenity ? strtoupper(substr($amenity->title, 0, 3)) : 'AMN';
                $booking->booking_number = $prefix . '-' . date('Ymd') . '-' . str_pad(
                    static::whereDate('created_at', today())->count() + 1,
                    3,
                    '0',
                    STR_PAD_LEFT
                );
            }
        });
    }

    /**
     * Get the user who made this booking
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the amenity for this booking
     */
    public function amenity(): BelongsTo
    {
        return $this->belongsTo(Amenity::class);
    }

    /**
     * Get the package at time of booking
     */
    public function package(): BelongsTo
    {
        return $this->belongsTo(CottagePackage::class, 'package_id');
    }

    /**
     * Get all items in this booking
     */
    public function items(): HasMany
    {
        return $this->hasMany(AmenityBookingItem::class);
    }

    /**
     * Get included items (from package)
     */
    public function includedItems(): HasMany
    {
        return $this->hasMany(AmenityBookingItem::class)
            ->where('was_included_in_package', true);
    }

    /**
     * Get paid items (not included in package)
     */
    public function paidItems(): HasMany
    {
        return $this->hasMany(AmenityBookingItem::class)
            ->where('was_included_in_package', false);
    }

    /**
     * Check if booking is confirmed
     */
    public function isConfirmed(): bool
    {
        return in_array($this->status, ['confirmed', 'upcoming']);
    }

    /**
     * Check if booking is completed
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * Check if booking is cancelled
     */
    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    /**
     * Check if booking is paid
     */
    public function isPaid(): bool
    {
        return $this->payment_status === 'paid';
    }

    /**
     * Check if reminder was sent
     */
    public function reminderSent(): bool
    {
        return $this->reminder_sent === true;
    }

    /**
     * Mark reminder as sent
     */
    public function markReminderSent(): void
    {
        $this->update([
            'reminder_sent' => true,
            'reminder_sent_at' => now(),
        ]);
    }

    /**
     * Cancel booking
     */
    public function cancel(?string $reason = null): void
    {
        $this->update([
            'status' => 'cancelled',
            'cancellation_reason' => $reason,
            'cancelled_at' => now(),
        ]);
    }

    /**
     * Calculate end time based on items duration
     */
    public function calculateEndTime(): void
    {
        $totalMinutes = (int) $this->items()->sum('duration_minutes');
    
        // Get time as string (H:i format) if it's a Carbon instance or time object
        $timeString = $this->booking_time;
        if ($timeString instanceof \DateTime || $timeString instanceof \Carbon\Carbon) {
            $timeString = $timeString->format('H:i:s');
        } elseif (is_string($timeString) && strlen($timeString) > 5) {
            // If it's a datetime string, extract just the time part
            $timeString = Carbon::parse($timeString)->format('H:i:s');
        }
        
        $startTime = Carbon::parse($this->booking_date->format('Y-m-d') . ' ' . $timeString);
        $endTime = $startTime->copy()->addMinutes($totalMinutes);
        
        $this->update(['end_time' => $endTime->format('H:i:s')]);
    }

    /**
     * Scope for upcoming bookings
     */
    public function scopeUpcoming($query)
    {
        return $query->where('status', 'upcoming')
            ->where('booking_date', '>=', now()->toDateString());
    }

    /**
     * Scope for user bookings
     */
    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope for bookings on a specific date
     */
    public function scopeForDate($query, string $date)
    {
        return $query->where('booking_date', $date);
    }

    /**
     * Scope for pending payment
     */
    public function scopePendingPayment($query)
    {
        return $query->where('payment_status', 'pending');
    }

    public function totalDurationText(): string
    {
        $minutes = (int) $this->items()->sum('duration_minutes');

        if ($minutes < 60) {
            return $minutes . ' minute' . ($minutes !== 1 ? 's' : '');
        }

        $hours = intdiv($minutes, 60);
        $remainingMinutes = $minutes % 60;

        $text = $hours . ' hour' . ($hours !== 1 ? 's' : '');

        if ($remainingMinutes > 0) {
            $text .= ' ' . $remainingMinutes . ' minute' . ($remainingMinutes !== 1 ? 's' : '');
        }

        return $text;
    }
}

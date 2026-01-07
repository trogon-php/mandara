<?php

namespace App\Services\MandaraBookings;

use App\Models\MandaraBooking;
use App\Services\Core\BaseService;
use App\Services\CottagePackages\CottagePackageService;
use App\Models\MandaraBookingAnswer;
use App\Models\MandaraBookingOrder;
use App\Models\CottagePackage;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Exception;

class MandaraBookingService extends BaseService
{
    protected string $modelClass = MandaraBooking::class;

    public function __construct(private CottagePackageService $cottagePackageService)
    {
        parent::__construct();
    }

    public function getFilterConfig(): array
    {
        return [
            'approval_status' => [
                'type' => 'exact',
                'label' => 'Approval Status',
                'col' => 3,
                'options' => [
                    'pending' => 'Pending',
                    'approved' => 'Approved',
                    'rejected' => 'Rejected',
                ],
            ]
        ];
    }

    public function getSearchFieldsConfig(): array
    {
        return [
            'booking_number' => 'Booking Number',
            'user.name' => 'User Name',
            'approval_status' => 'Approval Status',
        ];
    }

    public function getDefaultSearchFields(): array
    {
        return ['booking_number', 'user.name', 'date_from', 'date_to', 'approval_status'];
    }

    public function getDefaultSorting(): array
    {
        return ['field' => 'created_at', 'direction' => 'desc'];
    }

    public function getByUserId(int $userId): ?MandaraBooking
    {
        return $this->model->where('user_id', $userId)->first();
    }
    public function getCottagePackageOptions(): array
    {
        return $this->cottagePackageService->model->pluck('title', 'id')->toArray();
    }
    public function getLatestUnpaidBookingByUser(int $userId): ?MandaraBooking
    {
        return $this->model->where('user_id', $userId)
            ->where('booking_payment_status', '!=', 'paid')
            ->latest()
            ->first();
    }
    public function approveBooking(MandaraBooking $booking): void
    {
        // 1. Validate payment
        if ($booking->booking_payment_status !== 'paid') {
            throw new Exception('Payment not completed for this booking.');
        }

        // 2. Prevent double processing
        if ($booking->approval_status !== 'pending') {
            throw new Exception('This booking is already processed.');
        }

        // 3. Prevent duplicate package creation
        $exists = DB::table('user_packages')
            ->where('booking_id', $booking->id)
            ->exists();

        if ($exists) {
            throw new Exception('Package already activated for this booking.');
        }

        // 4. Calculate dates
        $package = $this->cottagePackageService->findOrFail(
            $booking->cottage_package_id
        );

        $startDate  = Carbon::parse($booking->date_from);
        $expiryDate = $startDate->copy()->addDays(
            (int) $package->duration_days
        );

        // 5. Insert into user_packages
        DB::table('user_packages')->insert([
            'user_id'     => $booking->user_id,
            'package_id'  => $booking->cottage_package_id,
            'booking_id'  => $booking->id,
            'status'      => 'active',
            'expiry_date' => $expiryDate->toDateString(),
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);

        // 6. Update booking status
        $booking->update([
            'approval_status' => 'approved',
        ]);
    }
    /**
     * Reject a booking
     */
    public function rejectBooking(MandaraBooking $booking): void
    {
        // Payment must be completed
        if ($booking->booking_payment_status !== 'paid') {
            throw new Exception('Payment not completed for this booking.');
        }

        // Prevent double processing
        if ($booking->approval_status !== 'pending') {
            throw new Exception('This booking is already processed.');
        }

        // Update status
        $booking->update([
            'approval_status' => 'rejected',
        ]);
    }

    public function storeAdditionalDetailsWithQuestions(MandaraBooking $booking, array $data): void
    {
        $emergencyPhone = null;

        if (!empty($data['emergency_contact_phone'])) {
    
            $countryCode = $data['emergency_contact_country_code'] ?? '';
            $phone       = $data['emergency_contact_phone'];
    
            // Normalize
            $countryCode = trim($countryCode);
            $phone       = preg_replace('/\s+/', '', $phone);
    
            // Ensure + prefix
            if ($countryCode && $countryCode[0] !== '+') {
                $countryCode = '+' . $countryCode;
            }
    
            $emergencyPhone = $countryCode . $phone;
        }
    
        // ----------------------------
        // SAVE BOOKING (without events)
        // ----------------------------
        $payload = [
            'blood_group'    => $data['blood_group'] ?? null,
            'is_veg'         => (int) ($data['is_veg'] ?? 0),
            'diet_remarks'   => $data['diet_remarks'] ?? null,
            'address'        => $data['address'] ?? null,
            'have_caretaker' => (int) ($data['have_caretaker'] ?? 0),
            'have_siblings'  => (int) ($data['have_siblings'] ?? 0),
            'husband_name'   => $data['husband_name'] ?? null,
        
           'consent' => isset($data['consent']) && (string)$data['consent'] === '1' ? 1 : 0,
            
            'special_notes'            => $data['special_notes'] ?? null,
            'emergency_contact_name'   => $data['emergency_contact_name'] ?? null,
            'emergency_contact_relationship'
                                    => $data['emergency_contact_relationship'] ?? null,
            'emergency_contact_phone'  => $emergencyPhone,
        ];
       
        MandaraBooking::withoutEvents(function () use ($booking, $payload) {
            $booking->fill($payload);
            $booking->save();
        });

        // ----------------------------------
        // SAVE / UPDATE BOOKING ANSWERS
        // ----------------------------------
        foreach ($data['questions'] ?? [] as $questionId => $answerData) {

            MandaraBookingAnswer::updateOrCreate(
                [
                    'booking_id'  => $booking->id,
                    'question_id' => (int) $questionId,
                ],
                [
                    'answer'   => $answerData['answer'] ?? null,
                    'remarks'  => $answerData['remarks'] ?? null, 
                    'user_id'  => $booking->user_id,
                ]
            );
        }
    }
    public function getBookingAnswers(int $bookingId)
    {
        return MandaraBookingAnswer::where('booking_id', $bookingId)
            ->get()
            ->keyBy('question_id');
    }
    public function getPaymentViewData(MandaraBooking $booking): array
    {
        // Latest payment (optional, for display only)
        $paymentOrder = MandaraBookingOrder::where('booking_id', $booking->id)
            ->latest()
            ->first();

        // Advance amount (fixed at booking time)
        $bookingAmount = (float) $booking->booking_amount;

        // Full package amount (SOURCE OF TRUTH)
        $packageAmount = (float) $booking->total_amount;

        // Total paid so far
        $paidAmount = MandaraBookingOrder::where('booking_id', $booking->id)
            ->where('payment_status', 'paid')
            ->sum('paid_amount');

        // Pending = FULL package − paid
        $pendingAmount = max($packageAmount - $paidAmount, 0);

        // Fully paid flag
        $isFullyPaid = $paidAmount >= $packageAmount;

        return [
            'booking'        => $booking,
            'paymentOrder'   => $paymentOrder,

            // Display clarity
            'bookingAmount'  => $bookingAmount,   // advance
            'packageAmount'  => $packageAmount,   // full price

            // Payment status
            'paidAmount'     => $paidAmount,
            'pendingAmount'  => $pendingAmount,
            'isFullyPaid'    => $isFullyPaid,
        ];
    }

    public function storePayment(MandaraBooking $booking, array $data): void
    {
      
        $bookingAmount = (float) $booking->booking_amount;
        $totalAmount   = (float) $booking->total_amount;
        $paidNow       = (float) $data['paid_amount'];

        // Total paid so far
        $alreadyPaid = MandaraBookingOrder::where('booking_id', $booking->id)
            ->where('payment_status', 'paid')
            ->sum('paid_amount');

        $remaining = $totalAmount - $alreadyPaid;

        // Guard (will automatically 500 if violated)
        if ($paidNow <= 0 || $paidNow > $remaining) {
            dd('here0');
            abort(422, 'Invalid payment amount');
        }

        // FIRST PAYMENT (booking not yet paid)
        if ($alreadyPaid == 0) {

            // Booking portion
            MandaraBookingOrder::create([
                'booking_id'     => $booking->id,
                'type'           => 'booking',
                'paid_amount'    => min($bookingAmount, $paidNow),
                'payable_amount' => $totalAmount,
                'total_amount'   => $totalAmount,
                'payment_method' => $data['payment_method'],
                'payment_id'     => $data['payment_method'] === 'bank'
                                    ? ($data['payment_id'] ?? null)
                                    : null,
                'payment_status' => 'paid',
                'created_by'     => auth()->id(),
                'updated_by'     => auth()->id(),
            ]);

            // Extra paid beyond booking
            if ($paidNow > $bookingAmount) {
               
                MandaraBookingOrder::create([
                    'booking_id'     => $booking->id,
                    'type'           => 'due',
                    'paid_amount'    => $paidNow - $bookingAmount,
                    'payable_amount' => $totalAmount,
                    'total_amount'   => $totalAmount,
                    'payment_method' => $data['payment_method'],
                    'payment_id'     => $data['payment_method'] === 'bank'
                                        ? ($data['payment_id'] ?? null)
                                        : null,
                    'payment_status' => 'paid',
                    'created_by'     => auth()->id(),
                    'updated_by'     => auth()->id(),
                ]);
            }
        }

        // SUBSEQUENT PAYMENTS (only due)
        else {
          
            MandaraBookingOrder::create([
                'booking_id'     => $booking->id,
                'type'           => 'due',
                'paid_amount'    => $paidNow,
                'payable_amount' => $totalAmount,
                'total_amount'   => $totalAmount,
                'payment_method' => $data['payment_method'],
                'payment_id'     => $data['payment_method'] === 'bank'
                                    ? ($data['payment_id'] ?? null)
                                    : null,
                'payment_status' => 'paid',
                'created_by'     => auth()->id(),
                'updated_by'     => auth()->id(),
            ]);
        }

        if ($alreadyPaid == 0) {

            // Mark booking as paid
            $booking->update([
                'booking_payment_status' => 'paid',
                'payment_method'         => $data['payment_method'],
            ]);
        
            $firstOrder = MandaraBookingOrder::where('booking_id', $booking->id)
            ->orderBy('id', 'ASC')   // first inserted record
            ->first();
        
            $isWebBooking = $firstOrder && is_null($firstOrder->payment_order_id);
        
            //  AUTO-APPROVE WEB BOOKINGS
            if ($isWebBooking) {
                $this->approveBooking($booking);
            }
        }
    }
    public function attachBookingSourceFlag($items)
    {
        if ($items->isEmpty()) {
            return $items;
        }

        // Collect booking IDs
        $bookingIds = $items->pluck('id')->toArray();

        // Get App bookings (payment_order_id NOT NULL)
        $appBookings = MandaraBookingOrder::whereIn('booking_id', $bookingIds)
            ->whereNotNull('payment_order_id')
            ->pluck('booking_id')
            ->toArray();
       

        $appBookingMap = array_flip($appBookings);

        // Attach flag
        foreach ($items as $item) {
            $item->is_app_booking = isset($appBookingMap[$item->id]);
        }

        return $items;
    }

}
    

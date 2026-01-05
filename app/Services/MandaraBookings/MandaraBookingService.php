<?php

namespace App\Services\MandaraBookings;

use App\Models\MandaraBooking;
use App\Services\Core\BaseService;
use App\Services\CottagePackages\CottagePackageService;
use App\Models\MandaraBookingAnswer;
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
       
        DB::transaction(function () use ($booking) {

            // 3. Prevent duplicate package creation
            $exists = DB::table('user_packages')
                ->where('booking_id', $booking->id)
                ->exists();
          

            if ($exists) {
                throw new Exception('Package already activated for this booking.');
            }
           
            // 4. Calculate dates
            $package = $this->cottagePackageService->findOrFail($booking->cottage_package_id);
           

            $startDate  = Carbon::parse($booking->date_from);
           
            $expiryDate = $startDate->copy()->addDays((int) $package->duration_days);
           
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
        });
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
    
}
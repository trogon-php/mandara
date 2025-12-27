<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\Booking\StoreMandaraBookingAdditionalRequest;
use App\Http\Requests\Api\Booking\StoreMandaraBookingRequest;
use App\Services\App\BookingService;
use Illuminate\Http\Request;

class MandaraBookingController extends BaseApiController
{
    public function __construct(protected BookingService $bookingService) {}

    /**
     * Store booking information (mandara-booking)
     */
    public function storeMandaraBooking(StoreMandaraBookingRequest $request)
    {
        $validated = $request->validated();
        // dd($validated);
        $result = $this->bookingService->storeMandaraBooking($validated);

        if ($result['status']) {
            return $this->respondSuccess($result['data'], $result['message']);
        }

        return $this->respondError($result['message'], 400);
    }

    public function getSummary()
    {
        $result = $this->bookingService->getSummary();

        if ($result['status']) {
            return $this->respondSuccess($result['data'], $result['message']);
        }

        return $this->respondError($result['message'], 400);
    }

    /**
     * Create booking order
     */
    public function createOrder(Request $request)
    {
        $request->validate([
            'remarks' => 'nullable|string|max:1000',
        ]);

        $user = $this->getAuthUser();

        $data['remarks'] = $request->input('remarks') ?? null;
        $data['notes'] = [
            'email' => $user->email,
            'phone' => $user->country_code . $user->phone,
        ];

        $result = $this->bookingService->createOrder($user->id, $data);

        return $this->serviceResponse($result);
    }
    /**
     * Complete booking order
     */
    public function completeOrder(Request $request)
    {
        $data = $request->validate([
            'razorpay_order_id' => 'required|string',
            'razorpay_payment_id' => 'required|string',
            'razorpay_signature' => 'required|string',
        ]);

        $result = $this->bookingService->completeOrder($data);

        return $this->serviceResponse($result);
    }

    /**
     * Store additional booking information (mandara-booking-additional)
     */
    public function storeMandaraAdditional(StoreMandaraBookingAdditionalRequest $request)
    {
        $validated = $request->validated();
        $user = $this->getAuthUser();
        // dd($validated);
        $result = $this->bookingService->storeBookingAdditional($user->id, $validated);

        if ($result['status']) {
            return $this->respondSuccess($result['data'], $result['message']);
        }

        return $this->respondError($result['message'], 400);
    }
}

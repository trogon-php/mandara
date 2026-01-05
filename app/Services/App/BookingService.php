<?php

namespace App\Services\App;

use App\Services\CottagePackages\CottagePackageService;
use App\Services\MandaraBookings\MandaraBookingOrderService;
use App\Services\MandaraBookings\MandaraBookingService;
use App\Services\Payments\RazorpayService;
use App\Services\Users\UserMetaService;
use Carbon\Carbon;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BookingService extends AppBaseService
{
    protected string $cachePrefix = 'booking';
    protected int $defaultTtl = 300;

    public function __construct(
        protected UserMetaService $userMetaService,
        protected MandaraBookingService $mandaraBookingService,
        protected MandaraBookingOrderService $orderService,
        protected CottagePackageService $cottagePackageService,
        protected RazorpayService $razorpayService
    ) {
        $this->clearCache();
    }

    /**
     * Store booking information (mandara-booking)
     */
    public function storeMandaraBooking(array $data): array
    {
        $user = $this->getAuthUser();
        
        if (!$user) {
            return [
                'status' => false,
                'message' => 'User not authenticated',
                'data' => []
            ];
        }

        // Prepare mandara booking data
        $bookingData = [
            'user_id' => $user->id,
            'cottage_package_id' => $data['cottage_package_id'] ?? null,
            'date_from' => $data['date_from'] ?? null,
            'date_to' => $data['date_to'] ?? null,
            'is_delivered' => $data['is_delivered'] ?? '0',
            'delivery_date' => $data['delivery_date'] ?? null,
            'additional_note' => $data['additional_note'] ?? null,
        ];

        // find cottage package
        $cottagePackage = $this->cottagePackageService->find($data['cottage_package_id']);
        // preparing booking summary data

        // preparing amounts by package
        $bookingData['booking_amount'] = $cottagePackage->booking_amount;
        $bookingData['total_amount'] = $cottagePackage->price;
        $bookingData['payable_amount'] = $cottagePackage->price;

        // dd($bookingData);
        // Store mandara booking
        // check if booking already exists
        $existingBooking = $this->mandaraBookingService->getByUserId($user->id);
        if ($existingBooking) {
            // update existing booking
            $result = $this->mandaraBookingService->update($existingBooking->id, $bookingData);
        } else {
            // create new booking
            $result = $this->mandaraBookingService->store($bookingData);
        }

        if ($result) {
            // Clear cache
            $this->clearCache();
            
            return [
                'status' => true,
                'message' => 'Booking information saved successfully',
                'data' => [
                    'booking_id' => $result->id,
                ]
            ];
        }

        return [
            'status' => false,
            'message' => 'Failed to save booking information',
            'data' => []
        ];
    }

    public function getSummary(): array
    {
        $user = $this->getAuthUser();
        
        if (!$user) {
            return [
                'status' => false,
                'message' => 'User not authenticated',
                'data' => []
            ];
        }

        // get booking summary
        $mandaraBooking = $this->mandaraBookingService->getByUserId($user->id);
        // dd($mandaraBooking);
        if (!$mandaraBooking) {
            return [
                'status' => false,
                'message' => 'Booking not found',
                'data' => []
            ];
        }
        // Tax calculation
        $taxAmount = 0;
        if($mandaraBooking->cottagePackage->tax_included == 0){
            $taxAmount = $mandaraBooking->booking_amount * 18 / 100;
        }

        $bookingSummary = [
            'date_from' => Carbon::parse($mandaraBooking->date_from)->format('M d,Y'),
            'date_to' => Carbon::parse($mandaraBooking->date_to)->format('M d,Y'),
            'duration_days' => $mandaraBooking->cottagePackage->duration_days,
            'booking_amount' => $mandaraBooking->booking_amount,
            'total_amount' => $mandaraBooking->booking_amount,
            'tax_amount' => $taxAmount,
            'payable_amount' => $mandaraBooking->booking_amount,
        ];
        return [
            'status' => true,
            'message' => 'Booking summary retrieved successfully',
            'data' => $bookingSummary
        ];
    }
    /**
     * Create booking order
     */
    public function createOrder(int $userId, array $data): array
    {
        return DB::transaction(function () use ($userId, $data) {

            $mandaraBooking = $this->mandaraBookingService->getByUserId($userId);
            // dd($mandaraBooking);
            if (!$mandaraBooking) {
                return [
                    'status' => false,
                    'message' => 'Booking not found',
                    'data' => []
                ];
            }
            if($data['remarks']) {
                // update booking remarks if available
                $mandaraBooking->remarks = $data['remarks'];
                $mandaraBooking->save();
            }

            // Create Razorpay order
            $razorpayReceipt = $this->orderService->generateReceipt();
            $bookingAmount = round($mandaraBooking->booking_amount, 2);
            $razorpayOrder = $this->razorpayService->createOrder([
                'amount' => $bookingAmount,
                'currency' => 'INR',
                'receipt' => $razorpayReceipt,
                'notes' => $data['notes']
            ]);
            if(!$razorpayOrder['status']) {
                return [
                    'status' => false,
                    'message' => 'Failed to create order',
                    'data' => [],
                    'http_code' => Response::HTTP_OK
                ];
            }
            $orderData = [
                'booking_id' => $mandaraBooking->id,
                'payment_status' => 'unpaid',
                'payment_method' => 'online',
                'payment_order_id' => $razorpayOrder['order_id'],
                'total_amount' => $mandaraBooking->booking_amount,
                'discount_amount' => 0,
                'payable_amount' => $mandaraBooking->booking_amount,
                'notes' => json_encode($data['notes'] ?? null),
            ];
            // store booking order
            $this->orderService->store($orderData);

            return [
                'status' => true,
                'message' => 'Booking order created successfully',
                'data' => [
                    'razorpay_order_id' => $razorpayOrder['order_id'],
                    'amount' => $razorpayOrder['amount'],
                    'currency' => 'INR',
                    'key_id' => config('services.razorpay.key_id'),
                    'notes' => $data['notes']
                ]
            ];
        });
    }

    /**
     * Complete booking order verifying payment
     */
    public function completeOrder(array $data): array
    {
        $result = $this->razorpayService->verifyPayment($data);

        if($result === false) {
            return [
                'status' => false,
                'message' => 'Payment verification failed',
                'http_code' => Response::HTTP_OK
            ];
        }

        $order = $this->orderService->getOrderByPaymentOrderId($data['razorpay_order_id']);

        if(!$order['status']) {
            return [
                'status' => false,
                'message' => $order['message'],
                'http_code' => Response::HTTP_OK
            ];
        }
        // update order
        $updateData = [
            'payment_status' => 'paid',
            'payment_id' => $data['razorpay_payment_id'],
        ];
        
        $order = $this->orderService->update($order['data']['id'], $updateData);

        if(!$order) {
            return [
                'status' => false,
                'message' => 'Order update failed',
                'http_code' => Response::HTTP_OK
            ];
        }
        $order->load('booking');
        // update booking payment status
        $this->mandaraBookingService->update($order->booking->id, [
            'booking_payment_status' => 'paid',
        ]);

        $orderData = [
            'date_from' => Carbon::parse($order->booking->date_from)->format('M d,Y'),
            'date_to' => Carbon::parse($order->booking->date_to)->format('M d,Y'),
            'duration_days' => $order->booking->cottagePackage->duration_days,
            'amount_paid' => $order->payable_amount,
        ];

        return [
            'status' => true,
            'message' => 'Payment completed successfully',
            'data' => $orderData,
            'http_code' => Response::HTTP_OK
        ];
    }
    /**
     * Store additional booking information (mandara-booking-additional)
     */
    public function storeBookingAdditional(int $userId, array $data): array
    {
        $mandaraBooking = $this->mandaraBookingService->getByUserId($userId);
        if (!$mandaraBooking) {
            return [
                'status' => false,
                'message' => 'Booking not found',
                'data' => []
            ];
        }
        $this->mandaraBookingService->update($mandaraBooking->id, $data);

        // store vegitarian status in user meta
        $this->userMetaService->updateUserMetaValue($userId, 'is_veg', $data['is_veg']);
        
        return [
            'status' => true,
            'message' => 'Additional booking information saved successfully',
            'data' => $mandaraBooking
        ];
    }
}

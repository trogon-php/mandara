<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\MandaraBookings\StoreMandaraBookingRequest as StoreRequest;
use App\Http\Requests\MandaraBookings\UpdateMandaraBookingRequest as UpdateRequest;
use App\Services\MandaraBookings\MandaraBookingService;
use Illuminate\Http\Request;
use App\Models\MandaraBooking;
use App\Models\User;

class MandaraBookingController extends AdminBaseController
{
    public function __construct(private MandaraBookingService $service) {}

    public function index(Request $request)
    {
    
        $filters = array_filter($request->only(['approval_status']));
        $searchParams = ['search' => $request->get('search')];

        $list_items = $this->service->getFilteredData(['search' => $searchParams['search'], 'filters' => $filters]);
       

        return view('admin.mandara_bookings.index', [
            'page_title' => 'Stay Bookings List',
            'list_items' => $list_items,
            'filters' => $filters,
            'search_params' => $searchParams,
            'filterConfig' => $this->service->getFilterConfig() ,
            'searchConfig' => $this->service->getSearchConfig() ,
        ]);
    }

    public function create() 
    { 
        return view('admin.mandara_bookings.create', [
            'page_title' => 'Reserve Stay Booking'
        ]);
    }

    public function store(StoreRequest $request)
    {
       
        $data = $request->validated();
       
        $data['user_id'] = $request->input('user_id');
        $data['booking_number'] = $request->input('booking_number');
        
        // 1. Find user by phone/email
        $user = User::withoutGlobalScopes()
        ->where(function ($q) use ($data) {
            if (!empty($data['phone'])) {
                $q->where('phone', $data['phone']);
            }
            if (!empty($data['email'])) {
                $q->orWhere('email', $data['email']);
            }
        })
        ->first();
       
       

        if (!$user) {
            return response()->json([
                'status'  => 0,
                'message' => 'User not found for given phone/email'
            ], 422);
        }
    
        $data['user_id'] = $user->id;
       

        // 2. Find existing booking
        $booking = null;
        if ($user) {
            $booking = MandaraBooking::where('user_id', $user->id)->latest()->first();
        }

        // 3. Update OR Create using existing service
        if ($booking) {

            
            $data['booking_number'] = $booking->booking_number;
            $data['booking_payment_status'] = $booking->booking_payment_status;
            $data['user_id'] = $booking->user_id;
        
            $this->service->update($booking->id, $data);
            $bookingId = $booking->id;
        
        } else {
        
            $newBooking = $this->service->store($data);
            $bookingId = $newBooking->id;
        }
        
        return redirect()->route(
            'admin.mandara-bookings.payment-view',
            $bookingId
        );

    }

    public function edit(string $id)
    {
        $edit_data = $this->service->find($id);
        return view('admin.mandara_bookings.edit', compact('edit_data'));
    }

    public function update(UpdateRequest $request, string $id)
    {
        $this->service->update($id, $request->validated());
        return $this->successResponse('Item updated successfully');
    }

    public function destroy(string $id)
    {
        if (!$this->service->delete($id)) {
            return $this->errorResponse('Failed to delete item');
        }
        return $this->successResponse('Item deleted successfully');
    }

    public function bulkDelete(Request $request)
    {
        if (!$this->service->bulkDelete($request->ids)) {
            return $this->errorResponse('Failed to delete items');
        }
        return $this->successResponse('Selected items deleted successfully');
    }
    //check existing booking ajax function
    public function checkExisting(Request $request)
    {
        $phone = trim($request->phone);
        $email = trim($request->email);

        if (!$phone && !$email) {
            return response()->json(['status' => 0]);
        }

        $booking = MandaraBooking::with('user')
            ->whereHas('user', function ($q) use ($phone, $email) {

                if ($phone && strlen($phone) >= 4) {
                    $q->where('phone', 'LIKE', "%{$phone}%");
                }

                if ($email && strlen($email) >= 4) {
                    $q->where('email', 'LIKE', "%{$email}%");
                }

            })
            ->latest()
            ->first();

        if (!$booking) {
            return response()->json(['status' => 0]);
        }

        // Return ONLY filled fields (NO booking number)
        $data = [];

        if ($booking->user?->phone) {
            $data['phone'] = $booking->user->phone;
        }

        if ($booking->user?->email) {
            $data['email'] = $booking->user->email;
        }

        if (!is_null($booking->is_delivered)) {
            $data['delivery_status'] = $booking->is_delivered ? 'Delivered' : 'Expected';
        }

        if ($booking->delivery_date) {
            $data['delivery_date'] = $booking->delivery_date;
        }

        if ($booking->date_from) {
            $data['arrival_date'] = $booking->date_from;
        }

        if ($booking->date_to) {
            $data['departure_date'] = $booking->date_to;
        }

        if ($booking->additional_note) {
            $data['additional_note'] = $booking->additional_note;
        }

        return response()->json([
            'status' => 1,
            'data' => $data
        ]);
    }
    public function paymentView(MandaraBooking $booking)
    {
    
        return view('admin.mandara_bookings.payment', [
            'page_title' => 'Payment Details',
            'booking'    => $booking,
        ]);
    }
    public function storePayment(Request $request, MandaraBooking $booking)
    {
        // Optional safety check
        if ($booking->booking_payment_status === 'paid') {
            return redirect()
                ->route('admin.mandara-bookings.additional-details', $booking->id)
                ->with('info', 'Payment already completed');
        }

        //  Mark booking as paid
        $booking->update([
            'booking_payment_status' => 'paid',
        ]);

        //  Redirect to next step (additional details)
        return redirect()->route(
            'admin.mandara-bookings.additional-details',
            $booking->id
        );
    }

    public function additionalDetails(MandaraBooking $booking)
    {

        return view('admin.mandara_bookings.additional-details', [
            'page_title' => 'Additional Details',
            'booking' => $booking,
        ]);
    }
    public function storeAdditionalDetails(Request $request, MandaraBooking $booking)
    {
        // Safety check
        if ($booking->booking_payment_status !== 'paid') {
            return redirect()
                ->route('admin.mandara-bookings.index')
                ->with('error', 'Complete payment first.');
        }

        $booking->update($request->only([
            'blood_group',
            'dietary_preference',
            'allergies',
            'address',
            'pickup_location',
            'accompanying_person',
            'family_details',
        ]));

        return redirect()
            ->route('admin.mandara-bookings.index')
            ->with('success', 'Additional details saved successfully');
    }

    public function approve(MandaraBooking $booking)
    {
        // Allow only if payment is completed
        if ($booking->booking_payment_status !== 'paid') {
            return back()->with('error', 'Payment not completed for this booking.');
        }
    
        // Prevent double approval/rejection
        if ($booking->approval_status !== 'pending') {
            return back()->with('info', 'This booking is already processed.');
        }
    
        $booking->update([
            'approval_status' => 'approved',
        ]);
    
        return back()->with('success', 'Booking approved successfully.');
    }
    
    public function reject(MandaraBooking $booking)
    {
        if ($booking->booking_payment_status !== 'paid') {
            return back()->with('error', 'Payment not completed for this booking.');
        }
    
        if ($booking->approval_status !== 'pending') {
            return back()->with('info', 'This booking is already processed.');
        }
    
        $booking->update([
            'approval_status' => 'rejected',
        ]);
    
        return back()->with('success', 'Booking rejected successfully.');
    }
    


}
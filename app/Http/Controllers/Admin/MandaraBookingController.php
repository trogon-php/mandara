<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\MandaraBookings\StoreMandaraBookingRequest as StoreRequest;
use App\Http\Requests\MandaraBookings\UpdateMandaraBookingRequest as UpdateRequest;
use App\Http\Requests\MandaraBookings\StoreMandaraBookingAdditionalDetailsRequest as StoreAdditionalDetailsRequest;
use App\Services\MandaraBookings\MandaraBookingService;
use App\Services\MandaraBookings\MandaraBookingQuestionsService;
use App\Services\CottagePackages\CottagePackageService;
use Illuminate\Http\Request;
use App\Models\MandaraBooking;
use App\Models\User;
use App\Models\MandaraBookingOrder;
use App\Models\CottagePackage;
use App\Services\Users\ClientService;

class MandaraBookingController extends AdminBaseController
{
    public function __construct(private MandaraBookingService $service,
     private CottagePackageService $cottagePackageService,
     private ClientService $clientService,
     private MandaraBookingQuestionsService $questionService
     ) {}

    public function index(Request $request)
    {
    
        $filters = array_filter($request->only(['approval_status']));
        $searchParams = ['search' => $request->get('search')];

        //$list_items = $this->service->getFilteredData(['search' => $searchParams['search'], 'filters' => $filters]);
        $list_items = $this->service->attachBookingSourceFlag($this->service->getFilteredData(['search' => $searchParams['search'], 'filters' => $filters]));
      

        return view('admin.mandara_bookings.index', [
            'page_title' => 'Stay Bookings List',
            'list_items' => $list_items,
            'filters' => $filters,
            'search_params' => $searchParams,
            'filterConfig' => $this->service->getFilterConfig() ,
            'searchConfig' => $this->service->getSearchConfig() ,
        ]);
    }

    public function create(Request $request)
    {
        $packages = CottagePackage::select('id', 'duration_days')->get();

        $packageDurations = CottagePackage::pluck('duration_days', 'id')->toArray();
    
        $cottagePackages = ['' => 'Select Cottage Package']
            + $this->cottagePackageService->getOptions();
        $prefillBooking = null;

        if ($request->booking_id) {
            $prefillBooking = MandaraBooking::with('user')
                ->findOrFail($request->booking_id);
        }
    
        return view('admin.mandara_bookings.create', [
            'page_title'        => 'Reserve Stay Booking',
            'cottagePackages'   => $cottagePackages,
            'packageDurations' => $packageDurations, 
            'prefillBooking'   => $prefillBooking,
        ]);
    }

    public function store(StoreRequest $request)
    {
        $data = $request->validated();
        

         //  Find or create client
         $user = $this->findOrCreateClient($data);
        $data['user_id'] = $user->id;
      
       //  Find unpaid booking
       if ($request->filled('booking_id')) {
        $booking = $this->service->update($request->booking_id, $data);
    } else {
        $booking = $this->service->getLatestUnpaidBookingByUser($user->id);

        if ($booking) {
            $booking = $this->service->update($booking->id, $data);
        } else {
            $booking = $this->service->store($data);
        }
    }

        return redirect()->route('admin.mandara-bookings.payment-view', $booking->id);
    }


    public function edit(MandaraBooking $mandara_booking)
    {
       // Modal edit (AJAX)
    if (request()->ajax()) {
        return view('admin.mandara_bookings.edit', [
            'booking' => $mandara_booking,
        ]);
    }

    // Full page edit (fallback)
    return view('admin.mandara_bookings.edit', [
        'page_title' => 'Edit Mandara Booking',
        'booking'    => $mandara_booking, 
    ]);
    }

    public function update(UpdateRequest $request, int $id)
    {
        //$this->service->update($id, $request->validated());
        $this->service->update($id, $request->all());
    
       
        if ($request->ajax()) {
            return response()->json([
                'status'  => 'success',
                'message' => 'Item updated successfully',
                'reload'  => true, 
            ]);
        }
    
        return redirect()
            ->route('admin.mandara-bookings.index')
            ->with('success', 'Item updated successfully');
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
    public function storeClient(Request $request)
    {
        $request->validate([
            'name'         => 'required|string|max:255',
            'phone'        => 'nullable|string|max:20',
            'email'        => 'nullable|email',
            'country_code' => 'nullable|string|max:10',
        ]);

        //  FIND EXISTING CLIENT
        $client = User::where(function ($q) use ($request) {
            if ($request->phone && $request->country_code) {
                $q->where('phone', $request->phone)
                ->where('country_code', $request->country_code);
            }
            if ($request->email) {
                $q->orWhere('email', $request->email);
            }
        })->first();

        // ➕ CREATE ONLY IF NOT FOUND
        if (!$client) {
            $client = User::create([
                'name'         => $request->name,
                'phone'        => $request->phone,
                'email'        => $request->email,
                'country_code' => $request->country_code,
                'status'       => 'active',
                'password'     => bcrypt(env('AUTO_CLIENT_PASSWORD', 'Client@123')),
            ]);
        }

        // ALWAYS REUSE EXISTING CLIENT
        session(['mandara_client_id' => $client->id]);

        // MOVE TO BOOKING PAGE
        return redirect()->route('admin.mandara-bookings.create');
    }

    public function paymentView(MandaraBooking $booking) {

        return view(
            'admin.mandara_bookings.payment',
            array_merge(
                ['page_title' => 'Payment Details'],
                $this->service->getPaymentViewData($booking)
            )
        );
    }


    public function storePayment(Request $request,MandaraBooking $booking) 
    {
        $paidAmount = \App\Models\MandaraBookingOrder::where('booking_id', $booking->id)
        ->where('payment_status', 'paid')
        ->sum('paid_amount');

        if ($paidAmount >= $booking->total_amount) {
            return redirect()
                ->route('admin.mandara-bookings.additional-details', $booking->id)
                ->with('info', 'Payment already completed');
        }
    
        // Validate
        $request->validate([
            'payment_method' => 'required|in:cash,online,bank',
            'payment_id'     => 'required_if:payment_method,bank',
            'paid_amount'    => 'required|numeric|min:1',
        ]);
    
        // Delegate to service
        $this->service->storePayment($booking, $request->all());
    
        return redirect()
            ->route('admin.mandara-bookings.additional-details', $booking->id)
            ->with('success', 'Payment recorded successfully');
    }


    public function additionalDetails(MandaraBooking $booking)
    {
        $termsContent = view('admin.mandara_bookings.terms')->render();
        $answers = $this->service->getBookingAnswers($booking->id);

          return view('admin.mandara_bookings.additional-details', [
            'page_title' => 'Additional Details',
            'booking' => $booking,
            'questions' => $this->questionService->getForAdditionalDetails(),
            'termsAndConditions' => $termsContent,
            'answers' => $answers,
        ]);
    }
    public function storeAdditionalDetails(StoreAdditionalDetailsRequest $request,MandaraBooking $booking) 
    {
        
        $data = $request->validated();
       
        $this->service->storeAdditionalDetailsWithQuestions($booking, $data);
    
        return redirect()->route('admin.mandara-bookings.index')->with('success', 'Additional details and questionnaire saved successfully.');
    }
    

    
    
    public function approve(MandaraBooking $booking,MandaraBookingService $service) 
    {
        try {
          
            $service->approveBooking($booking);
    
            return back()->with('success', 'Booking approved and package activated successfully.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
    
    public function reject(MandaraBooking $booking)
    {
        try {
            $this->service->rejectBooking($booking);
    
            return back()->with('success', 'Booking rejected successfully.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
    public function client()
    {
        return view('admin.mandara_bookings.client', [
            'page_title' => 'Client Details'
        ]);
    }
    public function checkExisting(Request $request)
    {
       
        $user = $this->clientService->findExistingClient(
            $request->phone,
            $request->country_code,
            $request->email
        );
       

        if (!$user) {
            return response()->json(['status' => 0]);
        }

        //  Find existing booking
        $booking = MandaraBooking::where('user_id', $user->id)
            ->latest()
            ->first();
          
    

            return response()->json([
                'status' => 1,
                'data' => [
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'country_code' => $user->country_code,
                    'booking' => $booking ? [
                        'id' => $booking->id,
                        'cottage_package_id' => $booking->cottage_package_id,
                        'is_delivered'       => $booking->is_delivered,
                        'delivery_date'      => $booking->delivery_date,
                        'date_from'          => $booking->date_from,
                        'date_to'            => $booking->date_to,
                        'additional_note'    => $booking->additional_note,
                    ] : null
                ]
            ]);
    }
    

    private function findOrCreateClient(array $data): User
    {
        return $this->clientService->findOrCreate($data);
    }
    public function review(MandaraBooking $booking)
    {
        // Only app-created + pending
        if (!$booking->is_app_booking || $booking->approval_status !== 'pending') {
            return redirect()
                ->route('admin.mandara-bookings.index')
                ->with('error', 'Invalid booking review request.');
        }

        return redirect()->route('admin.mandara-bookings.create',['booking_id' => $booking->id]);
    }


}
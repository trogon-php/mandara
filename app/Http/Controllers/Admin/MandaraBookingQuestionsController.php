<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\MandaraBookings\StoreMandaraBookingQuestionsRequest as StoreRequest;
use App\Http\Requests\MandaraBookings\UpdateMandaraBookingQuestionsRequest as UpdateRequest;
use App\Services\MandaraBookings\MandaraBookingQuestionsService;
use Illuminate\Http\Request;


class MandaraBookingQuestionsController extends AdminBaseController
{
    public function __construct(private MandaraBookingQuestionsService $service) {}

    public function index(Request $request)
    {
    
        $filters = array_filter($request->only(['question']));
        $searchParams = ['search' => $request->get('search')];

        $list_items = $this->service->getFilteredData(['search' => $searchParams['search'], 'filters' => $filters]);
       

        return view('admin.mandara_booking_questions.index', [
            'page_title' => 'Mandara Booking Questions List',
            'list_items' => $list_items,
            'filters' => $filters,
            'search_params' => $searchParams,
            'filterConfig' => $this->service->getFilterConfig() ,
            'searchConfig' => $this->service->getSearchConfig() ,
        ]);
    }

    public function create()
    {
       
        return view('admin.mandara_booking_questions.create');
    }

    public function store(StoreRequest $request)
    {
        //dd($request->all());
        $this->service->store($request->validated());
        return $this->successResponse('Item created successfully');
    }


    public function edit(string $id)
    {
        
        $edit_data = $this->service->find($id);
        return view('admin.mandara_booking_questions.edit', compact('edit_data'));
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
   

}
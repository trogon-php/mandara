<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Admin\AdminBaseController;
use App\Services\{
    Banners\BannerService,
    Courses\CourseService
};
use App\Http\Requests\Banners\{
    StoreBannerRequest as StoreRequest,
    UpdateBannerRequest as UpdateRequest
};

class BannerController extends AdminBaseController
{

    public function __construct(private BannerService $service, private CourseService $courseService)
    {
    }

    // list all items
    public function index(Request $request)
    {
        $filters = $request->only(['status']);
        $searchParams = [
            'search' => $request->get('search'),
        ];

        // Remove empty filters
        $filters = array_filter($filters, function($value) {
            return !empty($value);
        });

        $params = [
            'search' => $searchParams['search'],
            'filters' => $filters,
        ];

        $list_items = $this->service->getFilteredData($params);


        return view('admin.banners.index', [
            'page_title' => 'Banners',
            'list_items' => $list_items,
            'filters' => $filters,
            'search_params' => $searchParams,
            'filterConfig' => $this->service->getFilterConfig(),
            'searchConfig' => $this->service->getSearchConfig(),
        ]);
    }

    // show add form (AJAX modal)
    public function create()
    {
        $courses = $this->courseService->getIdTitle();
        return view('admin.banners.create', [
            'courses' => $courses,
        ]);
    }

    // handle add form submission
    public function store(StoreRequest $request)
    {
        $this->service->store($request->validated());

        return $this->successResponse('Banner created successfully');
    }

    // show edit form (AJAX modal)
    public function edit($id)
    {
        $banner = $this->service->find($id);

        return view('admin.banners.edit', [
            'edit_data'  => $banner,
        ]);
    }

    // handle edit form submission
    public function update(UpdateRequest $request, $id)
    {
        $this->service->update($id, $request->validated());

        return $this->successResponse('Banner updated successfully');
    }

    // show single banner
    public function show($id)
    {
        return view('admin.banners.show', [
            'item'     => $this->service->find($id),
        ]);
    }

    // show sort view
    public function sortView(Request $request)
    {
        return view('admin.banners.sort', [
            'list_items' => $this->service->getAll(),
        ]);
    }

    // handle sort update 
    public function sortUpdate(Request $request)
    {
        $result = $this->service->sortUpdate($request->order);
        if (!$result) {
            return $this->errorResponse('Failed to update sort order');
        }
        return $this->successResponse('Sort order updated successfully');
    }

    // delete a banner
    public function destroy($id)
    {
        if (!$this->service->delete($id)) {
            return $this->errorResponse('Failed to delete banner');
        }
        return $this->successResponse('Banner deleted successfully');
    }

    // bulk delete banners
    public function bulkDelete(Request $request)
    {
        if (!$this->service->bulkDelete($request->ids)) {
            return $this->errorResponse('Failed to delete banners');
        }
        return $this->successResponse('Selected banners deleted successfully');
    }

    // clone item
    public function cloneItem($id)
    {
        $banner = $this->service->find($id);

        $cloned = $this->service->clone($banner);

        if (!$cloned) {
            return $this->errorResponse('Failed to clone banner.');
        }

        return $this->successResponse('Banner cloned successfully.', [
            'action'  => 'modal', // or 'redirect'
            'url'     => route('admin.banners.edit', $cloned->id),
        ]);
    }
}

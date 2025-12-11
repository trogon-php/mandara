<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Admin\AdminBaseController;
use App\Services\Reels\ReelService;
use App\Http\Requests\Reels\StoreReelRequest as StoreRequest;
use App\Http\Requests\Reels\UpdateReelRequest as UpdateRequest;
use App\Models\Reel;
use App\Models\ReelCategory;
use App\Models\Course;
use App\Models\Category;

class ReelController extends AdminBaseController
{
    protected ReelService $service;

    public function __construct(ReelService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $filters = $request->only(['status', 'premium', 'reel_category_id', 'course_id', 'category_id']);
        $searchParams = [
            'search' => $request->get('search'),
        ];

        $filters = array_filter($filters, function($value) {
            return !empty($value);
        });

        $params = [
            'search' => $searchParams['search'],
            'filters' => $filters,
        ];

        if($filters){
            $list_items = $this->service->getFilteredData($params);
        }else{
            $list_items = $this->service->getAll();
        }

        return view('admin.reels.index', [
            'page_title' => 'Reels',
            'list_items' => $list_items,
            'filters' => $filters,
            'search_params' => $searchParams,
            'filterConfig' => $this->service->getFilterConfig(),
            'searchConfig' => $this->service->getSearchConfig(),
        ]);
    }

    public function create()
    {
        $reelCategories = $this->service->getReelCategoryOptions();
        $courses = $this->service->getCourseOptions();
        $categories = $this->service->getCategoryOptions();
        
        return view('admin.reels.create', [
            'reelCategories' => $reelCategories,
            'courses' => $courses,
            'categories' => $categories,
        ]);
    }

    public function store(StoreRequest $request)
    {
        $this->service->store($request->validated());
        return $this->successResponse('Reel added successfully');
    }

    public function edit($id)
    {
        $edit_data = $this->service->find($id);
        $reelCategories = $this->service->getReelCategoryOptions();
        $courses = $this->service->getCourseOptions();
        $categories = $this->service->getCategoryOptions();

        return view('admin.reels.edit', [
            'edit_data' => $edit_data,
            'reelCategories' => $reelCategories,
            'courses' => $courses,
            'categories' => $categories,
        ]);
    }

    public function update(UpdateRequest $request, $id)
    {
        $this->service->update($id, $request->validated());
        return $this->successResponse('Reel updated successfully');
    }

    public function show($id)
    {
        return view('admin.reels.show', [
            'item' => $this->service->find($id),
        ]);
    }

    public function sortView(Request $request)
    {
        return view('admin.reels.sort', [
            'list_items' => $this->service->getAll(),
        ]);
    }

    public function sortUpdate(Request $request)
    {
        $result = $this->service->sortUpdate($request->order);
        return $this->successResponse('Sort order updated successfully');
    }

    public function destroy($id)
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

    public function cloneItem($id)
    {
        $item = $this->service->find($id);

        $cloned = $this->service->clone($item);

        if (!$cloned) {
            return $this->errorResponse('Failed to clone item.');
        }

        return $this->successResponse('Item cloned successfully.', [
            'action' => 'modal', // or 'redirect'
            'url' => route('admin.reels.edit', $cloned->id),
        ]);
    }
}

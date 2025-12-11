<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Admin\AdminBaseController;
use App\Services\Courses\CourseService;
use App\Services\Programs\ProgramService;
use App\Services\Categories\CategoryService;
use App\Http\Requests\Courses\StoreCourseRequest as StoreRequest;
use App\Http\Requests\Courses\UpdateCourseRequest as UpdateRequest;

class CourseController extends AdminBaseController
{
    protected CourseService $service;
    protected ProgramService $programService;
    protected CategoryService $categoryService;

    public function __construct(
        CourseService $service,
        ProgramService $programService,
        CategoryService $categoryService
    ) {
        $this->service = $service;
        $this->programService = $programService;
        $this->categoryService = $categoryService;

        $this->middleware('can:courses/index')->only(['index', 'show', 'overview']);
        $this->middleware('can:courses/create')->only(['create', 'store', 'cloneItem']);
        $this->middleware('can:courses/edit')->only(['edit', 'update', 'sortView', 'sortUpdate']);
        $this->middleware('can:courses/delete')->only('destroy', 'bulkDelete');
    }

    public function index(Request $request)
    {
        $filters = $request->only(['status', 'category_id']);
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

        $filterConfig = $this->service->getFilterConfig();
        $searchConfig = $this->service->getSearchConfig();

        return view('admin.courses.index', [
            'page_title' => 'Courses',
            'list_items' => $list_items,
            'filters' => $filters,
            'search_params' => $searchParams,
            'filterConfig' => $filterConfig,
            'searchConfig' => $searchConfig,
        ]);
    }

    public function create()
    {
        $programs = [];
        $categories = [];

        if (has_feature('programs')) {
            $programs = $this->programService->getIdTitle();
        }
        if (has_feature('categories')) {
            $categories = $this->categoryService->getFlatCategoriesOptions();
        }

        return view('admin.courses.create', [
            'page_title' => 'Add Course',
            'programs' => $programs,
            'categories' => $categories,
        ]);
    }

    public function store(StoreRequest $request)
    {
        $this->service->store($request->validated());

        return response()->json([
            'status' => 'success',
            'message' => 'Course added successfully',
        ]);
    }

    public function edit($id)
    {
        $edit_data = $this->service->find($id);

        $programs = [];
        $categories = [];
        
        if (has_feature('programs')) {
            $programs = $this->programService->getIdTitle();
        }
        if (has_feature('categories')) {
            $categories = $this->categoryService->getFlatCategoriesOptions();
        }

        return view('admin.courses.edit', [
            'page_title' => 'Edit Course',
            'edit_data' => $edit_data,
            'programs' => $programs,
            'categories' => $categories,
        ]);
    }

    public function update(UpdateRequest $request, $id)
    {
        $this->service->update($id, $request->validated());

        return response()->json([
            'status' => 'success',
            'message' => 'Course updated successfully',
        ]);
    }

    public function show($id)
    {
        return view('admin.courses.show', [
            'page_title' => 'Course Details',
            'course' => $this->service->find($id),
        ]);
    }

    public function overview($id)
    {
        $overviewData = $this->service->getCourseOverview($id);
        
        if (empty($overviewData)) {
            return redirect()->route('admin.courses.index')
                           ->with('error', 'Course not found.');
        }

        return view('admin.courses.overview', array_merge([
            'page_title' => 'Course Overview - ' . $overviewData['course']->title,
        ], $overviewData));
    }

    public function sortView(Request $request)
    {
        return view('admin.courses.sort', [
            'list_items' => $this->service->getAll(),
        ]);
    }

    public function sortUpdate(Request $request)
    {
        $this->service->sortUpdate($request->order);

        return response()->json([
            'status' => 'success',
            'message' => 'Sort order updated successfully',
        ]);
    }

    public function destroy($id)
    {
        $this->service->delete($id);

        return response()->json([
            'status' => 'success',
            'message' => 'Course deleted successfully',
        ]);
    }

    public function bulkDelete(Request $request)
    {
        $this->service->bulkDelete($request->ids);

        return response()->json([
            'status' => 'success',
            'message' => 'Selected courses deleted successfully',
        ]);
    }

    public function cloneItem($id)
    {
        $course = $this->service->find($id);
        $cloned = $this->service->clone($course);

        if (!$cloned) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to clone course.'
            ]);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Course cloned successfully.',
            'action' => 'modal',
            'url' => route('admin.courses.edit', $cloned->id),
        ]);
    }
}
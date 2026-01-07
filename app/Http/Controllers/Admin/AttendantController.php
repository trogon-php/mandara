<?php

namespace App\Http\Controllers\Admin;

use App\Services\Users\AttendantService;
use App\Services\Users\UserMetaService;
use App\Services\Users\UserService;
use Illuminate\Http\Request;

class AttendantController extends AdminBaseController
{
    public function __construct(
        private AttendantService $service,
        private UserService $userService,
        private UserMetaService $userMetaService
        ) {}

        public function index(Request $request)
        {
          
            $filters = $request->only(['status', 'name', 'email', 'phone']);
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
            // dd($params);
            $list_items = $this->service->getFilteredData($params);
            
            
            return view('admin.attendants.index', [
                'page_title' => 'Attendants',
                'list_items' => $list_items,
                'filters' => $filters,
                'search_params' => $searchParams,
                'filterConfig' => $this->service->getFilterConfig(),
                'searchConfig' => $this->service->getSearchConfig(),
            ]);
        }

        public function getSelect2AjaxOptions(Request $request)
        {
            $params = [
                'search' => $request->get('search', ''),
                'page' => $request->get('page', 1),
                'per_page' => $request->get('per_page', 15)
            ];
            
            $students = $this->service->getSelect2AjaxOptions($params);
            
            $studentOptions = $students->map(function ($user) {
                $phone = $user->phone ? " [+{$user->country_code} {$user->phone}]" : '';
                $label = $user->name . $phone;
    
                return ['id' => $user->id, 'label' => $label];
            });
            return response()->json([
                'data' => $studentOptions,
                'pagination' => [
                    'more' => $students->hasMorePages()
                ]
            ]);
        }

        public function create()
        {
            return view('admin.attendants.create');
        }

        public function store(Request $request)
        {
            $data = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'phone' => 'nullable|string|max:20',
                'country_code' => 'nullable|string|max:10',
                'password' => 'required|string|min:8',
                'status' => 'required|in:active,pending,blocked',
                'profile_picture' => 'nullable|image|max:2048',
            ]);

            if ($request->hasFile('profile_picture')) {
                $data['profile_picture'] = $request->file('profile_picture');
            }

            $this->service->store($data);
            return $this->successResponse('Attendant added successfully');
        }

        public function edit($id)
        {
            $edit_data = $this->service->find($id);
            
            if (!$edit_data) {
                return $this->errorResponse('Attendant not found');
            }

            return view('admin.attendants.edit', [
                'edit_data' => $edit_data,
            ]);
        }

        public function update(Request $request, $id)
        {
            $attendant = $this->service->find($id);
            
            if (!$attendant) {
                return $this->errorResponse('Attendant not found');
            }

            $data = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email,' . $id,
                'phone' => 'nullable|string|max:20',
                'country_code' => 'nullable|string|max:10',
                'password' => 'nullable|string|min:8',
                'status' => 'required|in:active,pending,blocked',
                'profile_picture' => 'nullable|image|max:2048',
            ]);

            // Only update password if provided
            if (empty($data['password'])) {
                unset($data['password']);
            }

            if ($request->hasFile('profile_picture')) {
                $data['profile_picture'] = $request->file('profile_picture');
            }

            $this->service->update($id, $data);
            return $this->successResponse('Attendant updated successfully');
        }

        public function show($id)
        {
            $item = $this->service->find($id);
            
            if (!$item) {
                return $this->errorResponse('Attendant not found');
            }

            return view('admin.attendants.show', [
                'item' => $item,
            ]);
        }

        public function destroy($id)
        {
            if (!$this->service->delete($id)) {
                return $this->errorResponse('Failed to delete attendant');
            }
            return $this->successResponse('Attendant deleted successfully');
        }
}

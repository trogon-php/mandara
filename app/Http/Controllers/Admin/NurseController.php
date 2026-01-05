<?php

namespace App\Http\Controllers\Admin;

use App\Services\Users\NurseService;
use App\Services\Users\UserMetaService;
use App\Services\Users\UserService;
use Illuminate\Http\Request;

class NurseController extends AdminBaseController
{
    public function __construct(
        private NurseService $service,
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
           
            $list_items = $this->service->getFilteredData($params);
            
            return view('admin.nurses.index', [
                'page_title' => 'Nurses',
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
            return view('admin.nurses.create');
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
            $data['role_id'] = 4;

            //store user
            $user = $this->service->store($data);
            
            $metaData = $request->only([
                'qualification',
                'specialization',
                'blood_group',
                'date_of_birth',
                'joining_date',
            ]);

            $this->userMetaService->storeUserMeta(
                $user->id,
                $metaData
            );
             
            return $this->successResponse('Nurse added successfully');
        }

        public function edit($id)
        {
            $edit_data = $this->service->find($id);
            
            if (!$edit_data) {
                return $this->errorResponse('Nurse not found');
            }
            // Attach meta fields directly to the model
            $metaKeys = [
                'qualification',
                'specialization',
                'blood_group',
                'date_of_birth',
                'joining_date',
            ];

            foreach ($metaKeys as $key) {
                $edit_data->{$key} = optional(
                    $edit_data->userMeta()->where('meta_key', $key)->first()
                )->meta_value;
            }

            return view('admin.nurses.edit', [
                'edit_data' => $edit_data,
               
            ]);
        }

        public function update(Request $request, $id)
        {
            $nurse = $this->service->find($id);
            
            if (!$nurse) {
                return $this->errorResponse('Nurse not found');
            }

            $data = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email,' . $id,
                'phone' => 'nullable|string|max:20',
                'country_code' => 'nullable|string|max:10',
                'password' => 'nullable|string|min:8',
                'status' => 'required|in:active,pending,blocked',
                'profile_picture' => 'nullable|image|max:2048',

                // meta fields
                'qualification'   => 'nullable|string|max:255',
                'specialization'  => 'nullable|string|max:255',
                'blood_group'     => 'nullable|string|max:10',
                'date_of_birth'   => 'nullable|date',
                'joining_date' => 'nullable|date',
            ]);

            $metaKeys = [
                'qualification',
                'specialization',
                'blood_group',
                'date_of_birth',
                'joining_date',
            ];
        
            $metaData = array_intersect_key($data, array_flip($metaKeys));
            $userData = array_diff_key($data, array_flip($metaKeys));
        
          
        
            if (empty($userData['password'])) {
                unset($userData['password']);
            }
        
            if ($request->hasFile('profile_picture')) {
                $userData['profile_picture'] = $request->file('profile_picture');
            }
        
            $this->service->update($id, $userData);
        
            foreach ($metaData as $key => $value) {
                $this->userMetaService->updateUserMetaValue(
                    $id,
                    $key,
                    $value
                );
            }
        
            return $this->successResponse('Nurse updated successfully');
        }

        public function show($id)
        {
            $item = $this->service->find($id);
            
            if (!$item) {
                return $this->errorResponse('Client not found');
            }

            return view('admin.clients.show', [
                'item' => $item,
            ]);
        }

        public function destroy($id)
        {
            if (!$this->service->delete($id)) {
                return $this->errorResponse('Failed to delete nurse');
            }
            return $this->successResponse('Nurse deleted successfully');
        }
}

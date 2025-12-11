<?php

namespace App\Http\Controllers\Admin;

use App\Services\Users\ClientService;
use App\Services\Users\UserMetaService;
use App\Services\Users\UserService;
use Illuminate\Http\Request;

class ClientController extends AdminBaseController
{
    public function __construct(
        private ClientService $service,
        private UserService $userService,
        private UserMetaService $userMetaService
        ) {}

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
}

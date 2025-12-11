<?php

namespace App\Http\Controllers\Api;

use App\Services\App\ProfileService;
use App\Http\Requests\Profile\UpdateProfileRequest;
use Illuminate\Http\Request;

class ProfileController extends BaseApiController
{
    public function __construct(protected ProfileService $profileService) {}

    public function index()
    {
        $data = $this->profileService->getProfile();

        return $this->respondSuccess($data, 'Profile fetched successfully');
    }

    public function update(UpdateProfileRequest $request)
    {
        // Get validated data
        $validated = $request->validated();

        // Call ProfileService update method
        $result = $this->profileService->updateProfile($validated);

        if ($result['status']) {
            return $this->respondSuccess($result['data'], $result['message']);
        } else {
            return $this->respondError($result['message'], 400);
        }
    }

}

<?php

namespace App\Services\Auth;

use App\Enums\Role;
use Illuminate\Http\Response;

class ClientAuthService extends BaseAuthService
{
    public function sendOtp(array $data): array
    {
        if($data['type'] == 'phone') {
            $user = $this->users->findByPhone($data['phone'], $data['country_code']);
        } else {
            $user = $this->users->findByEmail($data['email']);
        }
        // dd($user);
        if(! $user) {
            // register user
            $user = $this->users->storeWithMeta($data);

            if($user) {

                return parent::loginWithOtp($data, [Role::CLIENT->value]);
            }
            return ['status' => false, 'message' => __('messages.user_registration_failed'), 'http_code' => Response::HTTP_INTERNAL_SERVER_ERROR];
        }
        return parent::loginWithOtp($data, [Role::CLIENT->value]);

    }
    // register client
    public function register(int $id,array $data): array
    {
        // Create user with meta data (separated)
        $user = $this->users->updateWithMeta($id, $data);
        
        if($user) {
            return ['status' => true, 'message' => __('messages.user_registered_successfully'), 'http_code' => Response::HTTP_OK];
        }
        return ['status' => false, 'message' => __('messages.user_registration_failed'), 'http_code' => Response::HTTP_INTERNAL_SERVER_ERROR];
    }
    // update date of birth
    public function updateDob(int $id, array $data): array
    {
        $user = $this->users->updateWithMeta($id, $data);
        if($user) {
            return ['status' => true, 'data' => null, 'message' => 'Date of birth updated successfully', 'http_code' => Response::HTTP_OK];
        }
        return ['status' => false, 'data' => null, 'message' => 'Date of birth update failed', 'http_code' => Response::HTTP_INTERNAL_SERVER_ERROR];
    }
    // update pregnancy
    public function updatePregnancy(int $id, array $data): array
    {
        $user = $this->users->updateWithMeta($id, $data);
        if($user) {
            return ['status' => true, 'data' => null, 'message' => 'Pregnancy updated successfully', 'http_code' => Response::HTTP_OK];
        }
        return ['status' => false, 'data' => null, 'message' => 'Pregnancy update failed', 'http_code' => Response::HTTP_INTERNAL_SERVER_ERROR];
    }
}

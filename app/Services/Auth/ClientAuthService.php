<?php

namespace App\Services\Auth;

use App\Enums\Role;
use App\Services\MandaraBookings\MandaraBookingService;
use Illuminate\Http\Response;

class ClientAuthService extends BaseAuthService
{
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
    public function updateJourney(int $id, array $data): array
    {
        $user = $this->users->updateWithMeta($id, $data);
        if($user) {
            return ['status' => true, 'data' => null, 'message' => 'Journey updated successfully', 'http_code' => Response::HTTP_OK];
        }
        return ['status' => false, 'data' => null, 'message' => 'Journey update failed', 'http_code' => Response::HTTP_INTERNAL_SERVER_ERROR];
    }

    // get pending steps
    public function getAuthNextStep(int $id, ?bool $includeSkippableSteps = true): int
    {
        $user = $this->users->find($id);

        if($this->isStepPending($user, 1)) {
            return 1;
        }

        if($this->isStepPending($user, 2)) {
            if(!$includeSkippableSteps && !$this->isStepPending($user, 3)) {
                return 0;
            }
            return 2;
        }
        if ($this->isStepPending($user, 3)) {
                return 3;
        }
        return 0;

    }
    public function getOnboardingNextStep(int $id): int
    {
        $booking = app(MandaraBookingService::class)->getByUserId($id);
        if($booking && $booking->booking_payment_status == 'paid') {
            if($booking->address == null) {
                return 3;
            }
            return 0;
        }
        if(!$booking) {
            return 1;
        }
        return 2;
    }
    private function isStepPending($user, $stepNumber)
    {
        if($stepNumber == 1) {
            return $user->name == null || empty($user->name);
        }
        if($stepNumber == 2) {
            return $user->getMetaField('date_of_birth') == null;
        }
        if($stepNumber == 3) {
            return $user->getMetaField('preparing_to_conceive') == null && $user->getMetaField('is_pregnant') == null && $user->getMetaField('is_delivered') == null;
        }
    }
}

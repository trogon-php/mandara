<?php

namespace App\Http\Controllers\Api;

use App\Services\App\UserJourneyService;
use App\Services\Users\ClientService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class UserJourneyController extends BaseApiController
{
    public function __construct(
        private ClientService $clientService,
        private UserJourneyService $userJourneyService
    )
    {}

    public function babyWellness(Request $request)
    {
        $user = $this->getAuthUser();

        $journeyStatus = $this->clientService->getJourneyStatus($user->id);

        $babyWellness = [];
        $recoveryJourneyData = $this->userJourneyService->getRecoveryJourneyData();
        $wellnessGuideData = $this->userJourneyService->getWellnessGuideData();
        $babyWellness['recovery_journey'] = $recoveryJourneyData;
        $babyWellness['wellness_guide'] = $wellnessGuideData;
        $babyWellness['journey_status'] = $journeyStatus;

        if($journeyStatus == 'delivered') {
            $babyWellnessData = $this->userJourneyService->postpartumTimeline($user->getMetaField('baby_dob'));
            $babyWellness['weekly_tips'] = $this->userJourneyService->getWeeklyTipsData($babyWellnessData['current_week']);
            $babyWellness = array_merge($babyWellness, $babyWellnessData);

            return $this->respondSuccess($babyWellness, 'Baby wellness fetched successfully');
        }
        return $this->respondSuccess($babyWellness, 'Baby wellness fetched successfully');
    }

    public function fertilityOverview()
    {
        $user = $this->getAuthUser();

        $journeyStatus = $this->clientService->getJourneyStatus($user->id);

        if($journeyStatus == 'preparing') {
            $fertilityOverview = $this->userJourneyService->getFertilityOverview($user->getMetaField('last_period_date'));
            return $this->respondSuccess($fertilityOverview, 'Fertility overview fetched successfully');
        }
        return $this->respondSuccess([], 'Fertility overview not found');
    }

    public function getNextPeriodDate(Request $request)
    {
        $request->validate([
            'last_period_date' => 'required|date',
            'cycle_length' => 'required|integer|min:21|max:35',
        ]);

        $lastPeriodDate = $request->input('last_period_date');
        $cycleLength = $request->input('cycle_length');

        $nextPeriodDate = $this->userJourneyService->calculateNextPeriodDate($lastPeriodDate, $cycleLength);
        $data = [
            'next_period_date' => "Your next period date is on " . Carbon::parse($nextPeriodDate)->format('M d Y'),
        ];
        return $this->respondSuccess($data, 'Next period date fetched successfully');
    }
}

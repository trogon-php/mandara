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
            // $fertilityOverview = $this->userJourneyService->getFertilityOverview($user->getMetaField('last_period_date'));
            $cycleOverview = $this->userJourneyService->getCycleOverview($user->id);
            return $this->respondSuccess($cycleOverview, 'Cycle overview fetched successfully');
        }
        return $this->respondSuccess([], 'Fertility overview not found');
    }

    public function confirmPeriod(Request $request)
    {
        $user = $this->getAuthUser();

        $request->validate([
            'period_start_date' => 'required|date',
            'period_length' => 'nullable|integer|min:1|max:10',
        ]);

        $periodStartDate = $request->input('period_start_date');
        $periodLength = $request->input('period_length');
        $this->userJourneyService->confirmPeriodStarted($periodStartDate, $periodLength, $user->id);

        return $this->respondSuccess([], 'Period confirmed successfully');
    }

    public function getOvulationDate(Request $request)
    {
        $request->validate([
            'last_period_date' => 'required|date',
            'cycle_length' => 'required|integer|min:21|max:35',
        ]);

        $lastPeriodDate = $request->input('last_period_date');
        $cycleLength = $request->input('cycle_length');

        $ovulationDate = $this->userJourneyService->calculateOvulationDate($lastPeriodDate, $cycleLength);
        $data = [
            'next_period_date' => "Your ovulation date is on " . Carbon::parse($ovulationDate)->format('M d Y'),
        ];
        return $this->respondSuccess($data, 'Ovulation date fetched successfully');
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\FeedbackRequest;
use App\Http\Resources\Feedbacks\FeedbackResource;
use App\Services\Feedbacks\FeedbackService;
use Illuminate\Http\Request;

class FeedbackController extends BaseApiController
{
    public function __construct(protected FeedbackService $feedbackService) {}

    /**
     * Get user's feedback (single feedback per user)
     */
    public function index(Request $request)
    {
        try {
            $user = $this->getAuthUser();
            if (!$user) {
                return $this->respondUnauthorized();
            }

            $feedback = $this->feedbackService->getUserFeedback($user->id);

            // Always return a feedback object with only rating and message
            $feedbackData = $feedback ? [
                'rating' => $feedback->rating,
                'message' => $feedback->message,
            ] : [
                'rating' => null,
                'message' => null,
            ];

            return $this->respondSuccess(
                $feedbackData,
                'Feedback fetched successfully'
            );
        } catch (\Exception $e) {
            return $this->respondError($e->getMessage(), 400);
        }
    }

    /**
     * Submit or update user feedback (upsert)
     */
    public function store(FeedbackRequest $request)
    {
        try {
            $user = $this->getAuthUser();
            if (!$user) {
                return $this->respondUnauthorized();
            }

            $data = $request->validated();
            $data['user_id'] = $user->id;

            $feedback = $this->feedbackService->upsertUserFeedback($data);

            return $this->respondSuccess(
                [
                    'rating' => $feedback->rating,
                    'message' => $feedback->message,
                ],
                'Feedback saved successfully'
            );
        } catch (\Exception $e) {
            return $this->respondError($e->getMessage(), 400);
        }
    }
}

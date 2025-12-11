<?php

namespace App\Http\Resources\Qa;

use App\Http\Resources\BaseResource;
use Illuminate\Http\Request;

class AppQaQuestionResource extends BaseResource
{
    public function __construct($resource)
    {
        parent::__construct($resource);
        $this->includeId = true;
        $this->includeAudit = false;
    }
    protected function resourceFields(Request $request): array
    {
        return [
            'question_text' => $this->question_text,
            'category_id' => $this->category_id,
            'answers_count' => $this->answers->count(),
            'vote_type' => $this->getVoteType(),
            'first_answer' => $this->getFirstAnswer(),
        ];
    }
    protected function getFirstAnswer()
    {
        return $this->whenLoaded('answers', function () {
            return AppQaAnswerResource::make($this->answers->first());
        }) ?? null;
    }
    protected function getVoteType()
    {
        return $this->whenLoaded('votes', function () {
            // dd($this->votes);
            return $this->votes->where('user_id', authUser()->id)->first()->vote_type ?? 'none';
        }) ?? 'none';
    }
}

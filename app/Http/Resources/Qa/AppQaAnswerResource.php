<?php

namespace App\Http\Resources\Qa;

use App\Http\Resources\BaseResource;
use Illuminate\Http\Request;

class AppQaAnswerResource extends BaseResource
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
            'question_text' => $this->question->question_text,
            'answer_text' => $this->answer_text,
            'user_name' => $this->user->name,
            'since' => $this->created_at->diffForHumans(),
        ];
    }
}

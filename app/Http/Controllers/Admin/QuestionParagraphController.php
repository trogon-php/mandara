<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\QuestionParagraph\StoreQuestionParagraphRequest;
use App\Http\Requests\QuestionParagraph\UpdateQuestionParagraphRequest;
use App\Services\QuestionParagraph\QuestionParagraphService;
use App\Services\Questions\QuestionService;
use Illuminate\Http\Request;

class QuestionParagraphController extends AdminBaseController
{
    public function __construct(
        private QuestionParagraphService $service,
        private QuestionService $questionService,
    )
    {
        //
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $filters = $request->all();
        $questionParagraphs = $this->service->getFilteredData($filters);
        $searchConfig = $this->service->getSearchConfig();
        $filterConfig = $this->service->getFilterConfig();
        

        return view('admin.question_paragraphs.index', [
            'page_title' => 'Question Paragraphs Management',
            'list_items' => $questionParagraphs,
            'search_config' => $searchConfig,
            'filter_config' => $filterConfig,
            'filters' => $filters
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $questions = $this->questionService->getAll()->map(function ($question) {
            $question->question_text = "ID : ". $question->id . " - " . $question->question_text;
            return $question;
        })->pluck('question_text', 'id')->toArray();
        
        
        // dd($questions);

        return view('admin.question_paragraphs.create',[
            'questions' => $questions,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreQuestionParagraphRequest $request)
    {
        // dd($request->validated());
        try {
            $this->service->store($request->validated());
            // dd('here');
            return $this->successResponse('Question paragraph created successfully');

        } catch (\Exception $e) {
            return $this->errorResponse('Failed to create paragraph question: ' . $e->getMessage());
            
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $edit_data = $this->service->findWithDetails($id);
        if (!$edit_data) {
            return $this->redirectWithError('admin.question-paragraphs.index', 'Question paragraph not found');
        }

        $questions = $this->questionService->getAll()->map(function ($question) {
            $question->question_text = "ID : ". $question->id . " - " . $question->question_text;
            return $question;
        })->pluck('question_text', 'id')->toArray();

        return view('admin.question_paragraphs.edit', [
            'page_title' => 'Edit Paragraph Question',
            'edit_data' => $edit_data,
            'questions' => $questions,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateQuestionParagraphRequest $request, string $id)
    {
        // dd($request->validated());
        try {
            $this->service->update($id, $request->validated());
            return $this->successResponse('Question paragraph updated successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to update paragraph question: ' . $e->getMessage());
        }
    }
    // show sort view
    public function sortView(Request $request)
    {
        return view('admin.question_paragraphs.sort', [
            'list_items' => $this->service->getAll(),
        ]);
    }

    // handle sort update
    public function sortUpdate(Request $request)
    {
        $result = $this->service->sortUpdate($request->order);
        if (!$result) {
            return $this->errorResponse('Failed to update sort order');
        }
        return $this->successResponse('Sort order updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}

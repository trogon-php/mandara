<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Core\SlugService;
use Illuminate\Http\Request;

class SlugController extends AdminBaseController
{
    public function __construct(private SlugService $slugService) {
        parent::__construct();
    }
    public function check(Request $request)
    {
        $request->validate([
            'model_name' => 'required|string',
            'slug' => 'required|string|max:255',
            'exclude_id' => 'nullable|integer',
        ]);
        
        $modelName = $request->input('model_name');
        $slug = $request->input('slug');
        $excludeId = $request->input('exclude_id');
        
        $exists = $this->slugService->checkSlug($modelName, $slug, $excludeId);
        
        if($exists === null){
            return $this->errorResponse('Model name not found!', null, 400);
        }
        
        return $this->successResponse('Slug check completed', [
            'exists' => $exists,
            'slug' => $slug,
            'model' => $modelName
        ]);
    }
}

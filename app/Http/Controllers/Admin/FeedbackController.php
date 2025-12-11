<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\AdminBaseController;
use App\Http\Resources\Feedbacks\FeedbackResource;
use App\Services\Feedbacks\FeedbackService;
use Illuminate\Http\Request;

class FeedbackController extends AdminBaseController
{
    protected string $serviceClass = FeedbackService::class;
    protected string $resourceClass = FeedbackResource::class;
    protected string $modelName = 'feedback';
    protected string $modelNamePlural = 'feedbacks';

    public function __construct(protected FeedbackService $feedbackService)
    {
        parent::__construct();
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $params = [
                'search' => $request->get('search'),
                'filters' => $request->get('filters', []),
                'sort_by' => $request->get('sort_by'),
                'sort_dir' => $request->get('sort_dir', 'desc'),
                'paginate' => true,
                'per_page' => $request->get('per_page', 15),
            ];

            $feedbacks = $this->feedbackService->getFilteredData($params);
            $statistics = $this->feedbackService->getStatistics();
            $filterConfig = $this->feedbackService->getFilterConfig();
            $searchFields = $this->feedbackService->getSearchFieldsConfig();

            return view('admin.feedbacks.index', compact(
                'feedbacks',
                'statistics',
                'filterConfig',
                'searchFields'
            ));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {
        try {
            $feedback = $this->feedbackService->find($id, ['user']);
            
            if (!$feedback) {
                return redirect()->route('admin.feedbacks.index')
                    ->with('error', 'Feedback not found.');
            }

            return view('admin.feedbacks.show', compact('feedback'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Update feedback status
     */
    public function updateStatus(Request $request, int $id)
    {
        try {
            $request->validate([
                'status' => 'required|in:pending,reviewed,resolved'
            ]);

            $success = $this->feedbackService->updateStatus($id, $request->status);
            
            if (!$success) {
                return redirect()->back()->with('error', 'Failed to update feedback status.');
            }

            $statusLabel = ucfirst($request->status);
            return redirect()->back()->with('success', "Feedback status updated to {$statusLabel}.");
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Bulk update status
     */
    public function bulkUpdateStatus(Request $request)
    {
        try {
            $request->validate([
                'ids' => 'required|array',
                'ids.*' => 'integer|exists:feedbacks,id',
                'status' => 'required|in:pending,reviewed,resolved'
            ]);

            $updated = 0;
            foreach ($request->ids as $id) {
                if ($this->feedbackService->updateStatus($id, $request->status)) {
                    $updated++;
                }
            }

            $statusLabel = ucfirst($request->status);
            return redirect()->back()->with('success', "Updated {$updated} feedback(s) to {$statusLabel}.");
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id)
    {
        try {
            $success = $this->feedbackService->delete($id);
            
            if (!$success) {
                return redirect()->back()->with('error', 'Failed to delete feedback.');
            }

            return redirect()->route('admin.feedbacks.index')
                ->with('success', 'Feedback deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Bulk delete feedbacks
     */
    public function bulkDelete(Request $request)
    {
        try {
            $request->validate([
                'ids' => 'required|array',
                'ids.*' => 'integer|exists:feedbacks,id'
            ]);

            $deleted = $this->feedbackService->bulkDelete($request->ids);
            
            return redirect()->back()->with('success', "Deleted {$deleted} feedback(s) successfully.");
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Get feedback statistics for dashboard
     */
    public function statistics()
    {
        try {
            $statistics = $this->feedbackService->getStatistics();
            return response()->json($statistics);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}

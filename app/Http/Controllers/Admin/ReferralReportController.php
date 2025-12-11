<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Admin\AdminBaseController;
use App\Services\Referrals\ReferralService;
use App\Http\Requests\Referrals\UpdateReferralRequest;

class ReferralReportController extends AdminBaseController
{
    protected ReferralService $service;

    public function __construct(ReferralService $service)
    {
        $this->service = $service;
    }

    /**
     * Display the referral report
     */
    public function index(Request $request)
    {
        $filters = $request->only(['referrer_id', 'date_from', 'date_to']);
        $searchParams = [
            'search' => $request->get('search'),
        ];

        // Remove empty filters
        $filters = array_filter($filters, function($value) {
            return !empty($value);
        });

        $params = [
            'search' => $searchParams['search'],
            'filters' => $filters,
        ];

        $list_items = $this->service->getFilteredData($params);
        
        return view('admin.reports.referrals.index', [
            'page_title' => 'Referral Report',
            'list_items' => $list_items,
            'filters' => $filters,
            'search_params' => $searchParams,
            'filterConfig' => $this->service->getFilterConfig(),
            'searchConfig' => $this->service->getSearchConfig(),
        ]);
    }

    /**
     * Show edit form for referral
     */
    public function edit($id)
    {
        $referral = $this->service->find($id, ['referrer', 'referred']);

        return view('admin.reports.referrals.edit', [
            'edit_data' => $referral,
        ]);
    }

    /**
     * Update referral
     */
    public function update(UpdateReferralRequest $request, $id)
    {
        $this->service->update($id, $request->validated());

        return $this->successResponse('Referral updated successfully');
    }

    /**
     * Delete referral
     */
    public function destroy($id)
    {
        $this->service->delete($id);

        return $this->successResponse('Referral deleted successfully');
    }
}




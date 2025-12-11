<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Admin\AdminBaseController;
use App\Services\Referrals\ReferralService;

class TopReferrersController extends AdminBaseController
{
    protected ReferralService $service;

    public function __construct(ReferralService $service)
    {
        $this->service = $service;
    }

    /**
     * Display the top referrers report
     */
    public function index(Request $request)
    {
        $filters = $request->only(['date_from', 'date_to']);
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
            'sort_by' => $request->get('sort_by', 'total_referrals'),
            'sort_dir' => $request->get('sort_dir', 'desc'),
        ];

        $list_items = $this->service->getTopReferrers($params);

        return view('admin.reports.top-referrers.index', [
            'page_title' => 'Top Referrers',
            'list_items' => $list_items,
            'filters' => $filters,
            'search_params' => $searchParams,
            'filterConfig' => $this->service->getTopReferrersFilterConfig(),
            'searchConfig' => $this->service->getSearchConfig(),
        ]);
    }
}




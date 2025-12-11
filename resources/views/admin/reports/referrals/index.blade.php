@include('admin.crud.crud-index-layout', [
    'page_title'    => 'Referral Report',
    'redirectUrl'   => url('admin/reports/referrals'),
    'tableId'       => 'referrals-table',
    'list_items'    => $list_items,
    'breadcrumbs'   => [
        'Dashboard' => url('admin/dashboard'),
        'Reports'   => null,
        'Referral Report' => null,
    ],
    'filters'       => view('admin.partials.universal-filters', [
        'filterConfig' => $filterConfig,
        'searchConfig' => $searchConfig
    ]),
    'customButtons' => [
        [
            'type'  => 'link',
            'text'  => 'Top Referrers',
            'url'   => url('admin/reports/top-referrers'),
            'class' => 'btn-outline-primary',
            'icon'  => 'ri-trophy-line',
        ]
    ],
    'tableHead'     => '
        <tr>
            <th>#</th>
            <th>Referrer</th>
            <th>Referred User</th>
            <th>Referral Code</th>
            <th>Reward Points</th>
            <th>Status</th>
            <th>Date Time</th>
            <th>Action</th>
        </tr>
    ',
    'tableBody'     => view('admin.reports.referrals.index-table', compact('list_items'))
])

<script>
// Initialize any custom JavaScript for referral report
$(document).ready(function() {
    console.log('Referral report loaded');
});
</script>




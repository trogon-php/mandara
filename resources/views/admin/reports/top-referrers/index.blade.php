@include('admin.crud.crud-index-layout', [
    'page_title'    => 'Top Referrers',
    'redirectUrl'   => url('admin/reports/top-referrers'),
    'tableId'       => 'top-referrers-table',
    'list_items'    => $list_items,
    'breadcrumbs'   => [
        'Dashboard' => url('admin/dashboard'),
        'Reports'   => null,
        'Top Referrers' => null,
    ],
    'filters'       => view('admin.partials.universal-filters', [
        'filterConfig' => $filterConfig,
        'searchConfig' => $searchConfig
    ]),
    'customButtons' => [
        [
            'type'  => 'link',
            'text'  => 'Referral Report',
            'url'   => url('admin/reports/referrals'),
            'class' => 'btn-outline-primary',
            'icon'  => 'ri-file-list-line',
        ]
    ],
    'tableHead'     => '
        <tr>
            <th>#</th>
            <th>Referrer Name</th>
            <th>Contact</th>
            <th>No. of Referred Users</th>
            <th>Total Reward Points</th>
            <th>Action</th>
        </tr>
    ',
    'tableBody'     => view('admin.reports.top-referrers.index-table', compact('list_items'))
])

<script>
$(document).ready(function() {
    console.log('Top referrers report loaded');
});
</script>




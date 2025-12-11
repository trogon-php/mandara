@include('admin.crud.crud-index-layout', [
    'page_title'    => 'Login Attempts',
    'createUrl'     => null, // No create functionality
    'sortUrl'       => null, // No sort functionality
    'bulkDeleteUrl' => url('admin/login-attempts/bulk-delete'),
    'redirectUrl'   => url('admin/login-attempts'),
    'tableId'       => 'login-attempts-table',
    'list_items'    => $list_items,
    'breadcrumbs'   => [
        'Dashboard' => url('admin/dashboard'),
        'Login Attempts' => null,
    ],
    'filters'       => view('admin.partials.universal-filters', [
        'filterConfig' => $filterConfig,
        'searchConfig' => $searchConfig
    ]),
    'customButtons' => [
        [
            'type'  => 'link',
            'text'  => 'Students',
            'url'   => url('admin/students'),
            'class' => 'btn-outline-success',
            'icon'  => 'mdi mdi-account-group'
        ],
        [
            'type'  => 'link',
            'text'  => 'Tutors',
            'url'   => url('admin/tutors'),
            'class' => 'btn-outline-info',
            'icon'  => 'mdi mdi-account-tie'
        ]
    ],
    'tableHead'     => '
        <tr>
            <th><input type="checkbox" id="select-all-bulk" class="form-check-input"></th>
            <th>#</th>
            <th>User Details</th>
            <th>Login Details</th>
            <th>Channel</th>
            <th>OTP</th>
            <th>Status</th>
            <th style="width: 120px;">Created</th>
            <th>Action</th>
        </tr>
    ',
    'tableBody'     => view('admin.login-attempts.index-table', compact('list_items'))
])

@include('admin.crud.crud-index-layout', [
    'page_title' => 'DietPlans',
    'createUrl' => url('admin/diet-plans/create'),
    'sortUrl' => url('admin/diet-plans/sort'),
    'bulkDeleteUrl' => url('admin/diet-plans/bulk-delete'),
    'redirectUrl' => url('admin/diet-plans'),
    'tableId' => 'diet-plans-table',
    'list_items' => $list_items,
    'breadcrumbs' => ['Dashboard'=>url('admin/dashboard'), 'DietPlans'=>null],
    'filters' => view('admin.partials.universal-filters', ['filterConfig'=>$filterConfig,'searchConfig'=>$searchConfig]),
    'tableHead' => '<tr>
                        <th><input type="checkbox" id="select-all-bulk" class="form-check-input"></th>
                        <th>#</th>
                        <th>Title</th>
                        <th>Month</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>',
    'tableBody' => view('admin.diet_plans.index-table', compact('list_items'))
])
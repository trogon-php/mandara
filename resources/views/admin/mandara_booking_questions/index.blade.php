@include('admin.crud.crud-index-layout', [
    'page_title' => 'Mandara Booking Questions',
    'createUrl' => url('admin/mandara-booking-questions/create'),
    'sortUrl' => url('admin/mandara-booking-questions/sort'),
    'bulkDeleteUrl' => url('admin/mandara-booking-questions/bulk-delete'),
    'redirectUrl' => url('admin/mandara-booking-questions'),
    'tableId' => 'mandara-booking-questions-table',
    'list_items' => $list_items,
    'breadcrumbs' => ['Dashboard'=>url('admin/dashboard'), 'Mandara Booking Questions'=>null],
    'filters' => view('admin.partials.universal-filters', ['filterConfig'=>$filterConfig,'searchConfig'=>$searchConfig]),
    'tableHead' => '<tr>
                        <th><input type="checkbox" id="select-all-bulk" class="form-check-input"></th>
                        <th>#</th>
                        <th>Question</th>
                        <th>Options</th>
                        <th>Require Remark</th>
                        <th>Action</th>
                    </tr>',
    'tableBody' => view('admin.mandara_booking_questions.index-table', compact('list_items'))
])
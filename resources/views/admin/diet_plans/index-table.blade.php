@if($list_items)
    @foreach($list_items as $list_item)
        <tr>
            <td><input type="checkbox" class="form-check-input row-checkbox" value="{{ $list_item->id }}"></td>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $list_item->title }}</td>
            <td>{{ $list_item->month }} Month(s)</td>
            <td>{{ $list_item->status ? 'Active' : 'Inactive' }}</td>
            <td>
                @include('admin.crud.action-dropdown', [
                    'cloneUrl'    => url('admin/diet-plans/'.$list_item->id.'/clone'),
                    'cloneTitle'  => 'Clone Diet Plan',
                    'editTitle'   => 'Edit Diet Plan',
                    'editUrl'=>url('admin/diet-plans/'.$list_item->id.'/edit'),
                    'deleteUrl'=>route('admin.diet-plans.destroy', $list_item->id),
                    'redirectUrl'=>route('admin.diet-plans.index')
                ])
            </td>
        </tr>
    @endforeach
@endif
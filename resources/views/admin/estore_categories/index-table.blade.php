@if ($list_items)
    @foreach ($list_items as $list_item)
        <tr>
            <td><input type="checkbox" class="form-check-input row-checkbox" value="{{ $list_item->id }}"></td>
            <td>{{ $loop->iteration }}</td>
           
            <td>
                <div class="fw-bold">{{ $list_item->title }}</div>
            </td>
            <td>
                @if ($list_item->status)
                    <span class="badge bg-success"><i class="mdi mdi-check"></i> Active</span>
                @else
                    <span class="badge bg-danger"><i class="mdi mdi-close"></i> Inactive</span>
                @endif
            </td>
            <td><small>{{ $list_item->updated_at->format('d-m-Y, g:i A') }}</small></td>
            <td>
                @include('admin.crud.action-dropdown', [
                    'cloneUrl'    => url('admin/estore-categories/'.$list_item->id.'/clone'),
                    'cloneTitle'  => 'Clone Estore Category',
                    'editUrl'     => url('admin/estore-categories/'.$list_item->id.'/edit'),
                    'editTitle'   => 'Update Estore Category',
                    'deleteUrl'   => route('admin.estore-categories.destroy', $list_item->id),
                    'redirectUrl' => route('admin.estore-categories.index')
                ])
            </td>
        </tr>
    @endforeach
@endif
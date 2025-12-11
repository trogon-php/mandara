@if ($list_items)
    @foreach ($list_items as $list_item)
        <tr>
            <td><input type="checkbox" class="form-check-input row-checkbox" value="{{ $list_item->id }}"></td>
            <td>{{ $loop->iteration }}</td>
            <td>
                <h6 class="mb-2 mt-1" style="font-size: 15px!important;">{{ $list_item->title }}</h6>
            </td>
            <td>
                @if ($list_item->status == 1)
                    <span class="badge bg-success p-2" style="font-size: 11px!important;min-width: 100px!important;border-radius: 50px!important;">
                        <i class="mdi mdi-check"></i> Active
                    </span>
                @else
                    <span class="badge bg-danger p-2" style="font-size: 11px!important;min-width: 100px!important;border-radius: 50px!important;">
                        <i class="mdi mdi-close"></i> Inactive
                    </span>
                @endif
            </td>
            <td>
                <span class="text-muted"><small>Created by:</small></span>
                <br>{{ $list_item->creator->name }} <br>
                <span class="text-muted mt-1"><small>{{ $list_item->created_at->format('d-m-Y, g:i A') }}</small></span>
            </td>
            <td>
                @include('admin.crud.action-dropdown', [
                    'cloneUrl'    => url('admin/reel-categories/'.$list_item->id.'/clone'),
                    'cloneTitle'  => 'Clone Reel Category',
                    'editUrl'     => url('admin/reel-categories/'.$list_item->id.'/edit'),
                    'editTitle'   => 'Update Reel Category',
                    'deleteUrl'   => route('admin.reel-categories.destroy', $list_item->id),
                    'redirectUrl' => route('admin.reel-categories.index')
                ])
            </td>
        </tr>
    @endforeach
@endif

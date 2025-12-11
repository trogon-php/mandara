@if ($list_items)
    @foreach ($list_items as $list_item)
        <tr>
            <td><input type="checkbox" class="form-check-input row-checkbox" value="{{ $list_item->id }}"></td>
            <td>{{ $loop->iteration }}</td>
            <td>
                @if($list_item->thumbnail_url)
                    <img src="{{ is_array($list_item->thumbnail_url) ? $list_item->thumbnail_url['thumb'] : $list_item->thumbnail_url }}" 
                         loading="lazy" 
                         alt="{{ $list_item->title }}" 
                         class="img-thumbnail" 
                         style="width: 100px; height: auto; object-fit: cover;">
                @else
                    <div class="bg-light border rounded d-flex align-items-center justify-content-center" 
                         style="width: 100px; height: 75px;">
                        <i class="mdi mdi-image-off text-muted"></i>
                    </div>
                @endif
            </td>
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
                    'cloneUrl'    => url('admin/cottage-categories/'.$list_item->id.'/clone'),
                    'cloneTitle'  => 'Clone Cottage Category',
                    'editUrl'     => url('admin/cottage-categories/'.$list_item->id.'/edit'),
                    'editTitle'   => 'Update Cottage Category',
                    'deleteUrl'   => route('admin.cottage-categories.destroy', $list_item->id),
                    'redirectUrl' => route('admin.cottage-categories.index')
                ])
            </td>
        </tr>
    @endforeach
@endif
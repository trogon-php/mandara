@if ($list_items)
    @foreach ($list_items as $list_item)
        <tr>
            <td><input type="checkbox" class="form-check-input row-checkbox" value="{{ $list_item->id }}"></td>
            <td>{{ $loop->iteration }}</td>
            <td>
                @if($list_item->image)
                    <img src="{{ $list_item->image_url }}" loading="lazy" alt="{{ $list_item->title }}" class="img-thumbnail" style="width: 100%!important; height: auto!important; object-fit: cover;">
                @else
                    <span class="text-muted">No Image</span>
                @endif
            </td>
            <td>
                <div class="fw-bold">{{ $list_item->title }}</div>
            </td>
            <td>
                <small class="text-muted">{{ $list_item->slug }}</small>
            </td>
            <td>
                @if($list_item->short_description)
                    <small class="text-muted">{{ Str::limit($list_item->short_description, 50) }}</small>
                @else
                    <span class="text-muted">-</span>
                @endif
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
                    'cloneUrl'    => url('admin/blogs/'.$list_item->id.'/clone'),
                    'cloneTitle'  => 'Clone Blog',
                    'editUrl'     => url('admin/blogs/'.$list_item->id.'/edit'),
                    'editTitle'   => 'Update Blog',
                    'deleteUrl'   => route('admin.blogs.destroy', $list_item->id),
                    'redirectUrl' => route('admin.blogs.index')
                ])
            </td>
        </tr>
    @endforeach
@endif
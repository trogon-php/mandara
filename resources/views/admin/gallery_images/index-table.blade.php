@if ($list_items)
    @foreach ($list_items as $list_item)
        <tr>
            <td><input type="checkbox" class="form-check-input row-checkbox" value="{{ $list_item->id }}"></td>
            <td>{{ $loop->iteration }}</td>
            <td>
                @if($list_item->image)
                    <img src="{{ $list_item->image_url }}" loading="lazy" alt="{{ $list_item->title ?: 'Gallery Image' }}" class="img-thumbnail" style="width: 100%!important; height: auto!important; object-fit: cover;">
                @else
                    <span class="text-muted">No Image</span>
                @endif
            </td>
            <td>
                <div class="fw-bold">{{ $list_item->title ?: 'Untitled Image' }}</div>
                @if($list_item->description)
                    <small class="text-muted">{{ Str::limit($list_item->description, 50) }}</small>
                @endif
            </td>
            <td>
                @if($list_item->album)
                    <span class="badge bg-primary">{{ $list_item->album->title }}</span>
                @else
                    <span class="text-muted">No Album</span>
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
                    'cloneUrl'    => url('admin/gallery-images/'.$list_item->id.'/clone'),
                    'cloneTitle'  => 'Clone Image',
                    'editUrl'     => url('admin/gallery-images/'.$list_item->id.'/edit'),
                    'editTitle'   => 'Update Image',
                    'deleteUrl'   => route('admin.gallery-images.destroy', $list_item->id),
                    'redirectUrl' => route('admin.gallery-images.index')
                ])
            </td>
        </tr>
    @endforeach
@endif

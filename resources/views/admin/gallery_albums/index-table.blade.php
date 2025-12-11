@if ($list_items)
    @foreach ($list_items as $list_item)
        <tr>
            <td><input type="checkbox" class="form-check-input row-checkbox" value="{{ $list_item->id }}"></td>
            <td>{{ $loop->iteration }}</td>
            <td>
                @if($list_item->thumbnail)
                    <img src="{{ $list_item->thumbnail_url }}" loading="lazy" alt="{{ $list_item->title }}" class="img-thumbnail" style="width: 100%!important; height: auto!important; object-fit: cover;">
                @else
                    <span class="text-muted">No Thumbnail</span>
                @endif
            </td>
            <td>
                <div class="fw-bold">{{ $list_item->title }}</div>
                @if($list_item->description)
                    <small class="text-muted">{{ Str::limit($list_item->description, 50) }}</small>
                @endif
            </td>
            <td>
                <span class="badge bg-info">{{ $list_item->images_count ?? 0 }} images</span>
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
                    'cloneUrl'    => url('admin/gallery-albums/'.$list_item->id.'/clone'),
                    'cloneTitle'  => 'Clone Album',
                    'editUrl'     => url('admin/gallery-albums/'.$list_item->id.'/edit'),
                    'editTitle'   => 'Update Album',
                    'deleteUrl'   => route('admin.gallery-albums.destroy', $list_item->id),
                    'redirectUrl' => route('admin.gallery-albums.index')
                ])
            </td>
        </tr>
    @endforeach
@endif

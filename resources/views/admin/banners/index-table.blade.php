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
                @if($list_item->description)
                    <small class="text-muted">{{ Str::limit($list_item->description, 50) }}</small>
                @endif
            </td>
            <td>
                <span class="badge bg-{{ $list_item->action_type === 'video' ? 'primary' : ($list_item->action_type === 'link' ? 'info' : ($list_item->action_type === 'course' ? 'success' : 'secondary')) }}">
                    {{ ucfirst($list_item->action_type) }}
                </span>
                @if($list_item->action_type === 'course' && $list_item->course)
                    <br><small class="text-muted">{{ $list_item->course->title }}</small>
                @elseif($list_item->action_value)
                    <br><small class="text-muted">{{ Str::limit($list_item->action_value, 30) }}</small>
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
                    'cloneUrl'    => url('admin/banners/'.$list_item->id.'/clone'),
                    'cloneTitle'  => 'Clone Banner',
                    'editUrl'     => url('admin/banners/'.$list_item->id.'/edit'),
                    'editTitle'   => 'Update Banner',
                    'deleteUrl'   => route('admin.banners.destroy', $list_item->id),
                    'redirectUrl' => route('admin.banners.index')
                ])
            </td>
        </tr>
    @endforeach
@endif


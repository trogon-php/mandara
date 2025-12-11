@if ($list_items)
    @foreach ($list_items as $list_item)
        <tr>
            <td><input type="checkbox" class="form-check-input row-checkbox" value="{{ $list_item->id }}"></td>
            <td>{{ $loop->iteration }}</td>
            <td>
                <div style="font-weight: 600;">
                    {{ $list_item->title }}
                </br>
                    @if($list_item->description)
                            <small class="text-muted">{{ Str::limit($list_item->description, 50) }}</small>
                        @endif
                </div>
            </td>
            <td>
                @if($list_item->course)
                    <span class="badge bg-info">{{ $list_item->course->title }}</span>
                @else
                    <span class="text-muted">No Course</span>
                @endif
            </td>
            <td>
                @if($list_item->category)
                    <span class="badge bg-secondary">{{ $list_item->category->title }}</span>
                @else
                    <span class="text-muted">No Category</span>
                @endif
            </td>
            <td>
                @if($list_item->premium)
                    <span class="badge bg-warning">Premium</span>
                @else
                    <span class="badge bg-success">Free</span>
                @endif
            </td>
            <td>
                <div class="d-flex align-items-center">
                    <img src="{{ $list_item->image_url }}" class="image rounded-circle me-2"
                         style="width:50px;height:50px;" onerror="this.style.display='none';">
                </div>
            </td>
            <td>
                <span class="badge bg-secondary"><i class="ri-eye-fill align-bottom me-1 text-white"></i>{{ $list_item->read_count }}</span>
            </td>
            <td><small>{{ $list_item->updated_at->format('d-m-Y, g:i A') }}</small></td>
            <td>
                @include('admin.crud.action-dropdown', [
                    'cloneUrl'    => url('admin/notifications/'.$list_item->id.'/clone'),
                    'cloneTitle'  => 'Clone Notification',
                    'editUrl'     => url('admin/notifications/'.$list_item->id.'/edit'),
                    'editTitle'   => 'Update Notification',
                    'deleteUrl'   => route('admin.notifications.destroy', $list_item->id),
                    'redirectUrl' => route('admin.notifications.index')
                ])
            </td>
        </tr>
    @endforeach
@endif

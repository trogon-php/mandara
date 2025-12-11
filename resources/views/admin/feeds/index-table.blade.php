@if ($list_items)
    @foreach ($list_items as $list_item)
        <tr>
            <td><input type="checkbox" class="form-check-input row-checkbox" value="{{ $list_item->id }}"></td>
            <td>{{ $loop->iteration }}</td>
            <td>
                <h6>{{ $list_item->title }}</h6>
            </td>
            <td>
                @if($list_item->feed_image_url && is_array($list_item->feed_image_url) && count($list_item->feed_image_url) > 0)
                    <button class="btn btn-info btn-sm mb-2" style="width: 100px;padding: 0px!important;height: 30px!important;" 
                        onclick='showFileModal("{{ addslashes($list_item->title) }}", {!! json_encode($list_item->feed_image_url) !!})'>
                        <i class="mdi mdi-image" style="font-size: 16px;"></i> {{ count($list_item->feed_image_url) }} Image(s)
                    </button>

                @endif

                @if($list_item->feed_video)
                    <button class="btn btn-warning btn-sm" style="width: 100px;padding: 0px!important;height: 30px!important;" onclick="showVideoModal('{{ $list_item->title }}', '{{ $list_item->feed_video_url }}')">
                        <i class="mdi mdi-video" style="font-size: 16px;"></i> Play Video
                    </button>
                @endif
                @if(!$list_item->feed_image && !$list_item->feed_video)
                    <span class="text-muted">No Media</span>
                @endif
            </td>
            <td>
                @if($list_item->feedCategory)
                    <span class="badge bg-secondary">{{ $list_item->feedCategory->title }}</span>
                @else
                    <span class="text-muted">No Category</span>
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
                    'cloneUrl'    => url('admin/feeds/'.$list_item->id.'/clone'),
                    'cloneTitle'  => 'Clone Feed',
                    'editUrl'     => url('admin/feeds/'.$list_item->id.'/edit'),
                    'editTitle'   => 'Update Feed',
                    'deleteUrl'   => route('admin.feeds.destroy', $list_item->id),
                    'redirectUrl' => route('admin.feeds.index')
                ])
            </td>
        </tr>
    @endforeach
@endif

@if ($list_items)
    @foreach ($list_items as $list_item)
        <tr>
            <td><input type="checkbox" class="form-check-input row-checkbox" value="{{ $list_item->id }}"></td>
            <td>{{ $loop->iteration }}</td>
            <td>
                <div class="d-flex align-items-start gap-3">
                    <div class="flex-shrink-0" style="width: 70px; height: auto;cursor: pointer;" onclick="showVideoModal('{{ $list_item->title }}', '{{ $list_item->video_url}}')">
                        @if($list_item->thumbnail_url)
                            <img src="{{ $list_item->thumbnail_url }}" class="img-thumbnail"
                                    style="width:70px;height:auto;border-radius: 8px!important; object-fit: cover;" onerror="this.style.display='none';">
                        @else
                            <div class="d-flex align-items-center justify-content-center bg-light border rounded" 
                                 style="width:70px;height:auto;border-radius: 8px!important;">
                                <i class="mdi mdi-video-off text-muted" style="font-size: 24px;"></i>
                            </div>
                        @endif
                    </div>
                    <div class="flex-grow-1">
                        <h6 class="mb-2 mt-1" style="font-size: 15px!important;">{{ $list_item->title }}</h6>
                        @if($list_item->description)
                            <small class="text-muted">{{ Str::limit($list_item->description, 50) }}</small>
                        @endif
                        @if($list_item->video_url)
                            <br>
                            <a href="javascript:void(0)" class="text-info" onclick="showVideoModal('{{ $list_item->title }}', '{{ $list_item->video_url}}')">
                                <i class="mdi mdi-play-circle"></i> Play Video
                            </a>
                        @endif
                    </div>
                </div>
            </td>
            <td>
                @if($list_item->reelCategory)
                    <a href="{{ url('admin/reels?reel_category_id='.$list_item->reel_category_id) }}" class="btn btn-outline-info p-1" style="min-width: 130px!important; font-size: 12px!important;">
                        <i class="mdi mdi-tag"></i> {{ $list_item->reelCategory->title }}
                    </a>
                @else
                    <span class="text-muted">No Category</span>
                @endif
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
                    'cloneUrl'    => url('admin/reels/'.$list_item->id.'/clone'),
                    'cloneTitle'  => 'Clone Reel',
                    'editUrl'     => url('admin/reels/'.$list_item->id.'/edit'),
                    'editTitle'   => 'Update Reel',
                    'deleteUrl'   => route('admin.reels.destroy', $list_item->id),
                    'redirectUrl' => route('admin.reels.index')
                ])
            </td>
        </tr>
    @endforeach
@endif
@if ($list_items)
    @foreach ($list_items as $list_item)
        <tr>
            <td>
                <input type="checkbox" class="form-check-input row-checkbox" value="{{ $list_item->id }}">
            </td>
            <td>
                <div style="width: 100px; height: 100px;">
                    @if($list_item->file_type == 'image')
                        <img src="{{ file_url($list_item->file_path, 'image') }}" 
                             class="img-thumbnail" 
                             style="width:100px;height:100px;object-fit: cover; cursor: pointer;"
                             onclick="viewMedia({{ $list_item->id }})"
                             onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                        <div class="d-flex align-items-center justify-content-center bg-light border rounded" 
                             style="width:100px;height:100px;display:none;">
                            <i class="mdi mdi-image-off text-muted" style="font-size: 24px;"></i>
                        </div>
                    @elseif($list_item->file_type == 'video')
                        <div class="d-flex align-items-center justify-content-center bg-light border rounded" 
                             style="width:100px;height:100px; cursor: pointer;"
                             onclick="viewMedia({{ $list_item->id }})">
                            <i class="mdi mdi-play-circle text-primary" style="font-size: 48px;"></i>
                        </div>
                    @elseif($list_item->file_type == 'audio')
                        <div class="d-flex align-items-center justify-content-center bg-light border rounded" 
                             style="width:100px;height:100px; cursor: pointer;"
                             onclick="viewMedia({{ $list_item->id }})">
                            <i class="mdi mdi-music-note text-info" style="font-size: 48px;"></i>
                        </div>
                    @else
                        <div class="d-flex align-items-center justify-content-center bg-light border rounded" 
                             style="width:100px;height:100px; cursor: pointer;"
                             onclick="viewMedia({{ $list_item->id }})">
                            <i class="mdi mdi-file text-secondary" style="font-size: 48px;"></i>
                        </div>
                    @endif
                </div>
            </td>
            <td>
                <div class="d-flex flex-column">
                    <h6 class="mb-1" style="font-size: 14px;">{{ $list_item->name }}</h6>
                    <code style="font-size: 11px;">{{ $list_item->original_name }}</code>
                    @if($list_item->alt_text)
                        <small class="text-muted mt-1">{{ Str::limit($list_item->alt_text, 50) }}</small>
                    @endif
                    <div class="mt-2">
                        <button class="btn btn-sm btn-outline-primary copy-url-btn" 
                                data-url="{{ file_url($list_item->file_path, $list_item->file_type) }}" 
                                title="Copy URL">
                            <i class="mdi mdi-content-copy"></i> Copy URL
                        </button>
                    </div>
                </div>
            </td>
            <td>
                <span class="badge bg-{{ $list_item->file_type == 'image' ? 'success' : ($list_item->file_type == 'video' ? 'primary' : ($list_item->file_type == 'audio' ? 'info' : 'secondary')) }}">
                    {{ ucfirst($list_item->file_type) }}
                </span>
            </td>
            <td>
                <small>{{ $list_item->formatted_size }}</small>
                @if($list_item->width && $list_item->height)
                    <br><small class="text-muted">{{ $list_item->width }} × {{ $list_item->height }}</small>
                @endif
            </td>
            <td>
                @if($list_item->folder)
                    <span class="badge bg-light text-dark">{{ $list_item->folder }}</span>
                @else
                    <span class="text-muted">—</span>
                @endif
            </td>
            <td>
                <small>{{ $list_item->created_at->format('d-m-Y') }}</small>
                <br><small class="text-muted">{{ $list_item->created_at->format('g:i A') }}</small>
            </td>
            <td>
                @include('admin.crud.action-dropdown', [
                    'editUrl' => null,
                    'deleteUrl' => route('admin.media.destroy', $list_item->id),
                    'redirectUrl' => route('admin.media.index'),
                    'viewUrl' => route('admin.media.show', $list_item->id),
                    'viewTitle' => 'View Media',
                ])
            </td>
        </tr>
    @endforeach
@endif
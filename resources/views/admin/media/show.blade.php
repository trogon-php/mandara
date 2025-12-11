{{ show_window_title('View Media') }}

<div class="row">
    <!-- Left Column - Preview -->
    <div class="col-lg-6">
        <div class="card shadow-sm">
            <div class="card-body p-0">
                @if($media->file_type == 'image')
                    <div class="position-relative">
                        <img src="{{ file_url($media->file_path, 'image') }}" 
                             alt="{{ $media->alt_text ?? $media->name }}" 
                             class="img-fluid w-100" 
                             style="max-height: 400px; object-fit: contain; background: #f8f9fa; border-radius: 8px 8px 0 0;">
                        <div class="position-absolute top-0 end-0 m-2">
                            <span class="badge bg-success fs-6">
                                <i class="mdi mdi-image"></i> Image
                            </span>
                        </div>
                    </div>
                @elseif($media->file_type == 'video')
                    <div class="d-flex align-items-center justify-content-center bg-dark" style="height: 300px; border-radius: 8px 8px 0 0;">
                        <div class="text-center text-white">
                            <i class="mdi mdi-play-circle-outline" style="font-size: 80px;"></i>
                            <p class="mt-2 mb-0">Video File</p>
                        </div>
                    </div>
                @elseif($media->file_type == 'audio')
                    <div class="d-flex align-items-center justify-content-center" style="height: 200px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 8px 8px 0 0;">
                        <div class="text-center text-white">
                            <i class="mdi mdi-music-circle-outline" style="font-size: 80px;"></i>
                            <p class="mt-2 mb-0">Audio File</p>
                        </div>
                    </div>
                @elseif($media->file_type == 'document')
                    <div class="d-flex align-items-center justify-content-center" style="height: 200px; background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); border-radius: 8px 8px 0 0;">
                        <div class="text-center text-white">
                            <i class="mdi mdi-file-document-outline" style="font-size: 80px;"></i>
                            <p class="mt-2 mb-0">Document</p>
                        </div>
                    </div>
                @else
                    <div class="d-flex align-items-center justify-content-center bg-secondary" style="height: 200px; border-radius: 8px 8px 0 0;">
                        <div class="text-center text-white">
                            <i class="mdi mdi-file-outline" style="font-size: 80px;"></i>
                            <p class="mt-2 mb-0">File</p>
                        </div>
                    </div>
                @endif

                <div class="p-3 border-top">
                    <div class="d-flex gap-2 flex-wrap">
                        <a href="{{ file_url($media->file_path, $media->file_type) }}" 
                           target="_blank" 
                           class="btn btn-primary flex-grow-1">
                            <i class="mdi mdi-open-in-new"></i> Open in New Tab
                        </a>
                        <button class="btn btn-outline-primary copy-url-btn" 
                                data-url="{{ file_url($media->file_path, $media->file_type) }}">
                            <i class="mdi mdi-content-copy"></i> Copy URL
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Column - Details -->
    <div class="col-lg-6">
        <!-- File Info Card -->
        <div class="card shadow-sm mb-3">
            <div class="card-header bg-transparent">
                <h5 class="card-title mb-0">
                    <i class="mdi mdi-information-outline text-primary me-2"></i>File Information
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-borderless mb-0">
                        <tbody>
                            <tr>
                                <td class="text-muted" style="width: 140px;">
                                    <i class="mdi mdi-tag-outline me-1"></i> Name
                                </td>
                                <td class="fw-medium">{{ $media->name }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">
                                    <i class="mdi mdi-file-outline me-1"></i> Original Name
                                </td>
                                <td>
                                    <code class="bg-light px-2 py-1 rounded">{{ $media->original_name }}</code>
                                </td>
                            </tr>
                            @if($media->alt_text)
                            <tr>
                                <td class="text-muted">
                                    <i class="mdi mdi-text me-1"></i> Alt Text
                                </td>
                                <td>{{ $media->alt_text }}</td>
                            </tr>
                            @endif
                            @if($media->description)
                            <tr>
                                <td class="text-muted">
                                    <i class="mdi mdi-text-box-outline me-1"></i> Description
                                </td>
                                <td>{{ $media->description }}</td>
                            </tr>
                            @endif
                            <tr>
                                <td class="text-muted">
                                    <i class="mdi mdi-folder-outline me-1"></i> Folder
                                </td>
                                <td>
                                    @if($media->folder)
                                        <span class="badge bg-light text-dark">{{ $media->folder }}</span>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Technical Details Card -->
        <div class="card shadow-sm mb-3">
            <div class="card-header bg-transparent">
                <h5 class="card-title mb-0">
                    <i class="mdi mdi-cog-outline text-primary me-2"></i>Technical Details
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-6">
                        <div class="border rounded p-3 text-center h-100">
                            <div class="text-muted small mb-1">File Type</div>
                            <span class="badge bg-{{ $media->file_type == 'image' ? 'success' : ($media->file_type == 'video' ? 'primary' : ($media->file_type == 'audio' ? 'info' : ($media->file_type == 'document' ? 'warning' : 'secondary'))) }} fs-6">
                                {{ ucfirst($media->file_type) }}
                            </span>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="border rounded p-3 text-center h-100">
                            <div class="text-muted small mb-1">File Size</div>
                            <div class="fw-bold text-dark">{{ $media->formatted_size }}</div>
                        </div>
                    </div>
                    @if($media->width && $media->height)
                    <div class="col-6">
                        <div class="border rounded p-3 text-center h-100">
                            <div class="text-muted small mb-1">Dimensions</div>
                            <div class="fw-bold text-dark">{{ $media->width }} × {{ $media->height }}</div>
                        </div>
                    </div>
                    @endif
                    <div class="col-6">
                        <div class="border rounded p-3 text-center h-100">
                            <div class="text-muted small mb-1">MIME Type</div>
                            <div class="fw-medium text-dark small">{{ $media->mime_type }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- File Path Card -->
        <div class="card shadow-sm mb-3">
            <div class="card-header bg-transparent">
                <h5 class="card-title mb-0">
                    <i class="mdi mdi-link-variant text-primary me-2"></i>File URL
                </h5>
            </div>
            <div class="card-body">
                <div class="input-group">
                    <input type="text" class="form-control bg-light" 
                           value="{{ file_url($media->file_path, $media->file_type) }}" 
                           id="file-url-input" readonly>
                    <button class="btn btn-outline-primary copy-url-btn" 
                            data-url="{{ file_url($media->file_path, $media->file_type) }}" 
                            type="button">
                        <i class="mdi mdi-content-copy"></i>
                    </button>
                </div>
                <small class="text-muted mt-2 d-block">
                    <i class="mdi mdi-folder-outline me-1"></i>
                    Storage Path: <code>{{ $media->file_path }}</code>
                </small>
            </div>
        </div>

        <!-- Timestamps Card -->
        <div class="card shadow-sm">
            <div class="card-header bg-transparent">
                <h5 class="card-title mb-0">
                    <i class="mdi mdi-clock-outline text-primary me-2"></i>Timestamps
                </h5>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <small class="text-muted d-block">Created</small>
                        <span class="fw-medium">{{ $media->created_at->format('M d, Y') }}</span>
                        <small class="text-muted d-block">{{ $media->created_at->format('g:i A') }}</small>
                    </div>
                    <div class="text-end">
                        <small class="text-muted d-block">Last Updated</small>
                        <span class="fw-medium">{{ $media->updated_at->format('M d, Y') }}</span>
                        <small class="text-muted d-block">{{ $media->updated_at->format('g:i A') }}</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('.copy-url-btn').on('click', function() {
        const url = $(this).data('url');
        navigator.clipboard.writeText(url).then(function() {
            messageSuccess('URL copied to clipboard!');
        }).catch(function(err) {
            messageDanger('Failed to copy URL');
        });
    });
});
</script>
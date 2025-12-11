@php
    // Helper function to determine file type
    if (!function_exists('getFileTypeForDisplay')) {
        function getFileTypeForDisplay($url) {
            if (empty($url)) return 'unknown';
            
            $extension = strtolower(pathinfo($url, PATHINFO_EXTENSION));
            
            $videoExtensions = ['mp4', 'avi', 'mov', 'wmv', 'flv', 'webm', 'mkv', 'm4v'];
            $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp', 'svg'];
            
            if (in_array($extension, $videoExtensions)) {
                return 'video';
            } elseif (in_array($extension, $imageExtensions)) {
                return 'image';
            } else {
                return 'file';
            }
        }
    }
    
    $fileType = getFileTypeForDisplay($value ?? '');
    
    $isPasteable = false;
    if(isset($pasteable) && $pasteable) {
        $isPasteable = true;
    }
@endphp

{{-- <div class="col-lg-{{ $col ?? 12 }} col-sm-12 p-2"> --}}
    <div class="form-group">
        <label for="{{ $id ?? $name }}">{{ $label }} {!! !empty($required) ? '<span class="text-danger">*</span>' : '' !!}</label>
        <div class="input-group {{ $isPasteable ? 'paste-file-group' : '' }}">
            <input type="file"
                   name="{{ $name }}"
                   id="{{ $id ?? $name }}"
                   class="form-control {{ $className ?? '' }} {{ $isPasteable ? 'paste-target-file' : ''}}"
                   {{ !empty($accept) ? "accept=$accept" : '' }}
                   {{ !empty($required) ? 'required' : '' }}>
            @if ($isPasteable)
                <div class="input-group-append">
                    <input type="text" 
                        class="form-control paste-source-field" 
                        placeholder="Paste image here">
                </div>
            @endif
        </div>
        @if(!empty($hint)) <small class="text-muted">{{ $hint }}</small> @endif
        @error($name) <small class="text-danger">{{ $message }}</small> @enderror
        {{-- Image preview area --}}
        <div class="file-preview-container mt-2 d-flex flex-wrap gap-2"></div>
            {{-- Hidden field to track removed existing file --}}
            <input type="hidden" name="{{ $name }}_removed" id="{{ $id ?? $name }}_removed" value="">

        @if(!empty($value))
            <div class="current-files-preview mb-3">
                <label class="form-label text-muted">Current {{ $label }}:</label>
                <div class="current-files-container">
                    <div class="current-file-item d-inline-block me-2 mb-2" data-file-path="{{ preg_replace('/^.*?(uploads\/)/', '$1', $value) }}">
                        <div class="position-relative" style="border: 1px solid #dee2e6; padding: 8px; border-radius: 4px; background: #f8f9fa;">
                            @if($fileType === 'video')
                                <button class="btn btn-sm btn-warning" style="width: 100px;padding: 0px!important;height: 30px!important;" 
                                        onclick="showVideoModal('{{ $label }}', '{{ $value }}')">
                                    <i class="mdi mdi-play" style="font-size: 16px;"></i> Play Video
                                </button>
                            @elseif($fileType === 'image')
                                {{-- <button class="btn btn-sm btn-info" style="width: 100px;padding: 0px!important;height: 30px!important;" 
                                        onclick="showFileModal('{{ $label }}', ['{{ $value }}'])">
                                    <i class="mdi mdi-image" style="font-size: 16px;"></i> View Image
                                </button> --}}
                                <img src="{{ $value }}" alt="Current {{ $label }}" style="max-width: 120px; height: auto; border-radius: 4px; display: block;">
                            @else
                                <button class="btn btn-sm btn-outline-primary" style="width: 100px;padding: 0px!important;height: 30px!important;" 
                                        onclick="showFileModal('{{ $label }}', ['{{ $value }}'])">
                                    <i class="mdi mdi-file" style="font-size: 16px;"></i> View File
                                </button>
                            @endif
                            {{-- Remove button --}}
                            <button type="button" 
                                    class="btn btn-sm btn-danger remove-existing-file" 
                                    title="Remove file"
                                    style="position:absolute;top:-5px;right:-5px;border-radius:50%;padding:0 6px;line-height:1;z-index:10;">×</button>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
{{-- </div> --}}
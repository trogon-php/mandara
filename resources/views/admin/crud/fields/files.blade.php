{{-- <div class="col-lg-{{ $col ?? 12 }} col-sm-12 p-2"> --}}
    @php
        $isPasteable = false;
        if(isset($pasteable) && $pasteable) {
            $isPasteable = true;
        }
    @endphp
    <label for="{{ $id ?? $name }}">{{ $label }} {!! !empty($required) ? '<span class="text-danger">*</span>' : '' !!}</label>
    <div class="input-group {{ $isPasteable ? 'paste-file-group' : 'file-input-group' }}">
        <input type="file"
               name="{{ $name }}[]"
               id="{{ $id ?? $name }}"
               class="form-control {{ $className ?? '' }} {{ $isPasteable ? 'paste-target-file' : ''}}"
               multiple
               {{ !empty($accept) ? "accept=$accept" : '' }}
               {{ !empty($required) ? 'required' : '' }}>
        @if(!empty($hint)) <small class="text-muted">{{ $hint }}</small> @endif
        @error($name) <small class="text-danger">{{ $message }}</small> @enderror
        @if ($isPasteable)
            <div class="input-group-append">
                <input type="text" 
                    class="form-control paste-source-field" 
                    placeholder="Paste image here">
            </div>
        @endif
    </div>
{{-- FIles preview area --}}
<div class="file-preview-container mt-2 d-flex flex-wrap gap-2"></div>

{{-- Hidden field to track removed existing files --}}
<input type="hidden" name="{{ $name }}_removed" id="{{ $id ?? $name }}_removed" multiple value="">

    @if(!empty($value))
        <div class="current-files-preview mb-3">
            <label class="form-label text-muted">Current {{ $label }}:</label>
            <div class="current-files-container">
                @foreach($value as $index => $filePath)
                    @php
                        $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
                        $isImage = in_array($ext, ['jpg','jpeg','png','gif','webp','svg']);
                    @endphp

                    <div class="current-file-item d-inline-block me-2 mb-2" data-file-path="{{ preg_replace('/^.*?(uploads\/)/', '$1', $filePath) }}">
                        <div class="position-relative" style="border: 1px solid #dee2e6; padding: 8px; border-radius: 4px; background: #f8f9fa;">
                            @if($isImage)
                                <img src="{{ $filePath }}" alt="Current {{ $label }}" style="max-width: 120px; height: auto; border-radius: 4px; display: block;">
                            @else
                                <a href="{{ $filePath }}" target="_blank" class="btn btn-outline-secondary btn-sm w-100">
                                    <i class="mdi mdi-file-document"></i> {{ strtoupper($ext) }} File
                                </a>
                            @endif
                            {{-- Remove button --}}
                            <button type="button" 
                                class="btn btn-sm btn-danger remove-existing-file" 
                                title="Remove file"
                                data-file-index="{{ $index }}"
                                style="position:absolute;top:-5px;right:-5px;border-radius:50%;padding:0 6px;line-height:1;z-index:10;">×</button>
                            <small class="text-muted d-block text-center mt-1" style="max-width: 120px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; font-size: 12px;">
                                {{ basename($filePath) }}
                            </small>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
{{-- </div> --}}

@php
    $preset = getImagePreset($presetKey ?? '', $subPreset ?? null);
    $fieldId = $name . '_input';
@endphp
{{-- <div class="col-lg-{{ $col ?? 12 }} col-sm-12 p-2" id="{{ $name }}"> --}}
    <label for="{{ $fieldId }}" class="form-label">{{ $label }} {!! !empty($required) ? '<span class="text-danger">*</span>' : '' !!}</label>
    
    {{-- Current Image Preview (if exists) --}}
    @if(!empty($value))
    
        <div class="current-image-preview mb-3">
            <label class="form-label text-muted">Current Image:</label>
            <div class="current-image-container {{ !empty($circle) ? 'rounded-circle' : '' }}" style="display: inline-block; border: 1px solid #dee2e6; padding: 10px; border-radius: 4px; background: #f8f9fa;">
                <img src="{{ $value }}" alt="Current {{ $label }}" style="max-width: 150px; height: auto; {{ !empty($circle) ? 'border-radius: 50%; width: 150px; height: 150px; object-fit: cover;' : 'border-radius: 4px;' }}">
            </div>
        </div>
    @endif
    
    {{-- File Input for New Image --}}
    <input type="file" class="form-control {{ $className ?? '' }}" id="{{ $fieldId }}" accept="image/*">

    @if($preset)
        <small class="text-muted">Recommended size: {{ $preset['width'] }}x{{ $preset['height'] }}px</small>
    @endif

    {{-- Cropper Preview (for new images) --}}
    <div id="{{ $name }}-preview" class="cropper-preview {{ !empty($circle) ? 'rounded-circle' : '' }}" style="display: none;">
        <img id="{{ $name }}-preview-img" src="" alt="New image preview">
        <button type="button" class="btn btn-sm btn-danger mt-2 d-none" id="remove-{{ $name }}">Remove New Image</button>
    </div>
{{-- </div> --}}

<script type="module">
    // Import cropper only once
    if (!window.initImageCropperLoaded) {
        import('/assets/app/js/imageCropper.js?v=1').then(module => {
            window.initImageCropper = module.initImageCropper;
            window.initImageCropperLoaded = true;

            setupCropper_{{ $name }}();
        });
    } else {
        setupCropper_{{ $name }}();
    }

    // Field-specific cropper init
    function setupCropper_{{ $name }}() {
        if (typeof window.croppedFiles === 'undefined') window.croppedFiles = {};

        window.initImageCropper({
            inputSelector: '#{{ $fieldId }}',
            previewSelector: '#{{ $name }}-preview',
            previewImgSelector: '#{{ $name }}-preview-img',
            removeBtnSelector: '#remove-{{ $name }}',
            aspectRatio: {{ $preset['width'] ?? 300 }} / {{ $preset['height'] ?? 300 }},
            quality: 0.8,
            maxSize: 1 * {{ $preset['width'] ?? 300 }} * {{ $preset['height'] ?? 300 }},
            onFileReady: (file) => { window.croppedFiles['{{ $name }}'] = file; }
        });
    }
</script>

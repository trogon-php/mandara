<div class="form-group">
    <label for="{{ $id ?? $name }}">{{ $label }} {!! !empty($required) ? '<span class="text-danger">*</span>' : '' !!}</label>
    <input type="text"
           name="{{ $name }}"
           id="{{ $id ?? $name }}"
           value="{{ old($name, $value ?? '') }}"
           class="form-control slug-field {{ $className ?? '' }}"
           placeholder="{{ $placeholder ?? '' }}"
           data-related-field="{{ $related_field_id ?? '' }}"
           data-model-name="{{ $model_name ?? '' }}"
           data-exclude-id="{{ $exclude_id ?? '' }}"
           {{ !empty($required) ? 'required' : '' }}>
    <div class="slug-status-message mt-2" id="slug-status-{{ $id ?? $name }}" style="display: none;"></div>
    @error($name) <small class="text-danger">{{ $message }}</small> @enderror
</div>
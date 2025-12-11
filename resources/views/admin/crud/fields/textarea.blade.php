{{-- <div class="col-lg-{{ $col ?? 12 }} col-sm-12 p-2"> --}}
    <div class="form-group">
        <label for="{{ $id ?? $name }}">{{ $label }} {!! !empty($required) ? '<span class="text-danger">*</span>' : '' !!}</label>
        <textarea name="{{ $name }}" id="{{ $id ?? $name }}" class="form-control {{ $className ?? '' }}"
                  placeholder="{{ $placeholder ?? '' }}"
                  {{ !empty($required) ? 'required' : '' }}>{{ old($name, $value ?? '') }}</textarea>
        @error($name) <small class="text-danger">{{ $message }}</small> @enderror
    </div>
{{-- </div> --}}

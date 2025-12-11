{{-- <div class="col-lg-{{ $col ?? 6 }} col-sm-12 p-2"> --}}
    <div class="form-group">
        <div class="form-check {{ isset($switch) && $switch ? 'form-switch' : '' }}">
            {{-- hidden field for if unchecked case --}}
            @if (isset($defaultValue))
                <input type="hidden" name="{{ $name }}" value="{{ $defaultValue }}">
            @endif

            <input type="checkbox" 
                   class="form-check-input {{ $className ?? '' }}" 
                   name="{{ $name }}" 
                   id="{{ $id ?? $name }}" 
                   value="1"
                   {{ old($name, $value ?? false) ? 'checked' : '' }}
                   {{ !empty($required) ? 'required' : '' }}>
            <label class="form-check-label" for="{{ $id ?? $name }}">
                {{ $label }} {!! !empty($required) ? '<span class="text-danger">*</span>' : '' !!}
            </label>
        </div>
        @error($name) <small class="text-danger">{{ $message }}</small> @enderror
    </div>
{{-- </div> --}}

{{-- <div class="col-lg-{{ $col ?? 4 }} col-sm-12 p-2"> --}}
    <div class="form-group">
        <label for="{{ $id ?? $name }}">{{ $label }} {!! !empty($required) ? '<span class="text-danger">*</span>' : '' !!}</label>
        <select class="form-control select2 {{ $className ?? '' }}" name="{{ $name }}" id="{{ $id ?? $name }}" {{ !empty($required) ? 'required' : '' }} {{ !empty($disabled) ? 'disabled' : '' }}>
            <option value="">{{ $placeholder ?? 'Select ' . $label }}</option>
            @foreach($options ?? [] as $key => $text)
                <option value="{{ $key }}" {{ old($name, $value ?? '') == $key ? 'selected' : '' }}>
                    {{ $text }}
                </option>
            @endforeach
        </select>
        @error($name) <small class="text-danger">{{ $message }}</small> @enderror
    </div>
{{-- </div> --}}


{{-- <div class="col-lg-{{ $col ?? 4 }} col-sm-12 p-2"> --}}
    <div class="form-group">
        <label for="{{ $id ?? $name }}_input">{{ $label }} {!! !empty($required) ? '<span class="text-danger">*</span>' : '' !!}</label>
        <select class="form-control {{ $className ?? '' }}" name="{{ $name }}" id="{{ $id ?? $name }}_input" {{ !empty($required) ? 'required' : '' }}>
            @foreach($options as $key => $option)
                @php

                    if(is_array($option)) {
                        $optionLabel = $option['label'] ?? "No Label";
                        $readonly = $option['readonly'] ?? false;
                    } else {
                        $optionLabel = $option;
                    }
                @endphp
                <option value="{{ $key }}"
                {{ old($name, $value ?? '') == $key ? 'selected' : '' }}
                {{ isset($readonly) && $readonly == 'true' ? 'disabled' : '' }}>
                    {{ $optionLabel }}
                </option>
            @endforeach
        </select>
        @error($name) <small class="text-danger">{{ $message }}</small> @enderror
    </div>
{{-- </div> --}}

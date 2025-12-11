{{-- <div class="col-lg-{{ $col ?? 6 }} col-sm-12 p-2"> --}}
    <div class="form-group">
        <label for="{{ $id ?? $name }}">
            {{ $label }} {!! !empty($required) ? '<span class="text-danger">*</span>' : '' !!}
        </label>
        <select class="form-control {{ !empty($select2) ? 'select2' : '' }} {{ $className ?? '' }}"
                name="{{ $name }}[]"
                id="{{ $id ?? $name }}"
                multiple
                data-placeholder="{{ $placeholder ?? 'Select ' . $label }}"
                {{ !empty($required) ? 'required' : '' }}>
            @foreach($options as $key => $text)
                <option value="{{ $key }}"
                    @if(collect(old($name, $value ?? []))->contains($key)) selected @endif>
                    {{ $text }}
                </option>
            @endforeach
        </select>
        @error($name) <small class="text-danger">{{ $message }}</small> @enderror
    </div>
{{-- </div> --}}

{{-- <div class="col-lg-{{ $col ?? 4 }} col-sm-12 p-2"> --}}
    <div class="form-group">
        <label for="{{ $id ?? $name }}">{{ $label }} {!! !empty($required) ? '<span class="text-danger">*</span>' : '' !!}</label>
        <input type="date"
               name="{{ $name }}"
               id="{{ $id ?? $name }}"
               value="{{ old($name, $value ?? '') }}"
               class="form-control {{ $className ?? '' }}"
               {{ !empty($required) ? 'required' : '' }}>
        @error($name) <small class="text-danger">{{ $message }}</small> @enderror
    </div>
{{-- </div> --}}

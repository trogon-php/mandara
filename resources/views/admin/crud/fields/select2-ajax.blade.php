{{-- <div class="col-lg-{{ $col ?? 4 }} col-sm-12 p-2"> --}}
    <div class="form-group">
        <label for="{{ $id ?? $name }}">{{ $label }} {!! !empty($required) ? '<span class="text-danger">*</span>' : '' !!}</label>
        <select class="form-control select2-ajax {{ $className ?? '' }}"
                name="{{ $name }}"
                id="{{ $id ?? $name }}"
                {{ !empty($required) ? 'required' : '' }}
                {{ !empty($disabled) ? 'disabled' : '' }}
                data-placeholder="{{ $placeholder ?? 'Select ' . $label }}"
                data-url="{{ $ajax_url ?? '' }}"
                data-per-page="{{ $per_page ?? 15 }}">
                @if (isset($default) && !empty($default))
                    <option value="{{ $default['key'] }}" selected="selected">{{ $default['label'] }}</option>
                @endif
        </select>
        @error($name) <small class="text-danger">{{ $message }}</small> @enderror
    </div>
{{-- </div> --}}


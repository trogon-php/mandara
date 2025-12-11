<div class="col-md-{{ $col ?? 3 }} col-sm-12 mb-3">
    <label for="{{ $id }}" class="form-label">{{ $label }}</label>
    <select class="form-control select2-ajax"
            name="{{ $name }}" 
            id="{{ $id }}"
            data-placeholder="{{ $placeholder ?? 'All ' . $label }}"
            data-url="{{ $ajax_url ?? '' }}"
            data-per-page="{{ $per_page ?? 15 }}">
            @if (isset($default) && !empty($default))
                <option value="{{ $default['key'] }}" selected="selected">{{ $default['label'] }}</option>
            @endif
    </select>
</div>
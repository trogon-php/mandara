<div class="col-md-{{ $col ?? 3 }} col-sm-12 mb-3">
    <label for="{{ $id }}" class="form-label">{{ $label }}</label>
    <select class="form-control select2" 
            name="{{ $name }}[]" 
            id="{{ $id }}"
            multiple
            data-placeholder="All {{ $label }}">
        @foreach($options as $value => $text)
            <option value="{{ $value }}" 
                    @if(in_array($value, (array)request($name, []))) selected @endif>
                {{ $text }}
            </option>
        @endforeach
    </select>
</div>

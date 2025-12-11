<div class="col-md-{{ $col ?? 3 }} col-sm-12 mb-3">
    <label for="{{ $id }}" class="form-label">{{ $label }}</label>
    <select class="form-control select2" 
            name="{{ $name }}" 
            id="{{ $id }}"
            data-placeholder="All {{ $label }}">
        <option value="">All {{ $label }}</option>
        @if(isset($options) && is_array($options))
            @foreach($options as $value => $text)
            @php
                $requestValue = request($name);
                $isSelected = $requestValue !== null && (string)$requestValue === (string)$value;
            @endphp
            <option value="{{ $value }}" 
                    {{ $isSelected ? 'selected' : '' }}>
                {{ $text }}
            </option>
            @endforeach
        @else
            {{-- Debug: options not set or not array --}}
            <option value="">No options available</option>
        @endif
    </select>
</div>

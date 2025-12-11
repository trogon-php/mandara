<div class="col-md-{{ $col ?? 3 }} col-sm-12 mb-3">
    <label for="{{ $id }}" class="form-label">{{ $label }}</label>
    <input type="text" 
           class="form-control" 
           name="{{ $name }}" 
           id="{{ $id }}"
           value="{{ request($name) }}" 
           placeholder="{{ $placeholder ?? 'Enter ' . $label }}">
</div>

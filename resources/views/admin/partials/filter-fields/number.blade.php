<div class="col-md-{{ $col ?? 2 }} col-sm-12 mb-3">
    <label for="{{ $id }}" class="form-label">{{ $label }}</label>
    <input type="number" 
           class="form-control" 
           name="{{ $name }}" 
           id="{{ $id }}"
           value="{{ request($name) }}" 
           placeholder="{{ $placeholder ?? 'Enter ' . $label }}"
           @if(isset($min)) min="{{ $min }}" @endif
           @if(isset($max)) max="{{ $max }}" @endif
           @if(isset($step)) step="{{ $step }}" @endif>
</div>

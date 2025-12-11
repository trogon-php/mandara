<input type="hidden"
       name="{{ $name }}"
       id="{{ $id ?? $name }}"
       value="{{ $value ?? '' }}">

@if(isset($label) && $label)
<div class="mb-3 p-2 bg-light border rounded" style="font-size: 0.9rem; line-height: 1.4;">
    <div class="d-flex align-items-center">
        <i class="mdi mdi-information-outline me-2 text-primary"></i>
        <span class="text-dark fw-medium">{{ $label }}</span>
    </div>
</div>
@endif








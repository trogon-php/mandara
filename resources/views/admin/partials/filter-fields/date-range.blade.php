<div class="col-md-{{ $col ?? 3 }} col-sm-12 mb-3">
    <label class="form-label">{{ $label }}</label>
    <div class="row g-2">
        <div class="col-6">
            <input type="date" 
                   class="form-control" 
                   name="{{ $fromField ?? 'date_from' }}" 
                   value="{{ request($fromField ?? 'date_from') }}"
                   placeholder="From">
        </div>
        <div class="col-6">
            <input type="date" 
                   class="form-control" 
                   name="{{ $toField ?? 'date_to' }}" 
                   value="{{ request($toField ?? 'date_to') }}"
                   placeholder="To">
        </div>
    </div>
</div>

<div class="col-xl-{{ $col ?? '12' }} col-md-{{ $col ?? '12' }}">
    <div class="card stat-card h-100">
        <div class="card-body">
            <div class="d-flex align-items-center mb-3">
                <div class="flex-shrink-0">
                    <div class="avatar-md rounded-circle bg-primary-subtle p-2">
                        <i class="{{ $iconClass ?? '' }} fs-2" style="color: {{ $iconColor }};"></i>
                    </div>
                </div>
                <div class="flex-grow-1 ms-3">
                    <h6 class="text-muted mb-0">{{ $label }}</h6>
                    <h2 class="mb-0 fw-semibold">{{ $value }}</h2>
                    <p class="text-muted mb-0">{{ $description }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
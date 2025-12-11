<!-- Dashboard Statistics Partial -->
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="fw-semibold mb-0">Quick Overview</h5>
            <div class="dropdown">
                <button class="btn btn-sm btn-soft-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    <i class="ri-calendar-line me-1"></i> This Month
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="#">Today</a></li>
                    <li><a class="dropdown-item" href="#">This Week</a></li>
                    <li><a class="dropdown-item" href="#">This Month</a></li>
                    <li><a class="dropdown-item" href="#">This Year</a></li>
                </ul>
            </div>
        </div>
    </div>
    
    <!-- Total Students -->
    <div class="col-xl-3 col-md-6">
        <div class="card stat-card h-100">
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <div class="flex-shrink-0">
                        <div class="avatar-md rounded-circle bg-primary-subtle p-2">
                            <i class="ri-user-line fs-2" style="color: #357980;"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="text-muted mb-0">Total Students</h6>
                        <h2 class="mb-0 fw-semibold">{{ $stats['total_students'] ?? 0 }}</h2>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Instructors -->
    <div class="col-xl-3 col-md-6">
        <div class="card stat-card h-100">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="avatar-md rounded-circle bg-info-subtle p-2">
                            <i class="ri-user-voice-line fs-2" style="color: #357980;"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="text-muted mb-0">Total Instructors</h6>
                        <h2 class="mb-0 fw-semibold">{{ $stats['total_instructors'] ?? 0 }}</h2>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- New Students Today -->
    <div class="col-xl-3 col-md-6">
        <div class="card stat-card h-100">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="avatar-md rounded-circle bg-success-subtle p-2">
                            <i class="ri-user-add-line fs-2" style="color: #27474A;"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="text-muted mb-0">New Students Today</h6>
                        <h2 class="mb-0 fw-semibold">{{ $stats['new_students_today'] ?? 0 }}</h2>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Income -->
    <div class="col-xl-3 col-md-6">
        <div class="card stat-card h-100">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="avatar-md rounded-circle bg-warning-subtle p-2">
                            <i class="ri-money-dollar-circle-line fs-2" style="color: #357980;"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="text-muted mb-0">Total Income</h6>
                        <h2 class="mb-0 fw-semibold">${{ $stats['total_income'] ?? 0 }}</h2>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>




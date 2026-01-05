@extends('admin.layouts.app')

@section('content')
{!! show_window_title($page_title) !!}

<!-- Welcome Section -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card welcome-card">
            <div class="card-body p-4">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h3 class="mb-2 fw-semibold text-white">Welcome back, {{ $user->name }}!</h3>
                        <p class="mb-0 opacity-75 text-white">Here's your nurse dashboard overview</p>
                    </div>
                    <div class="flex-shrink-0">
                        <div class="avatar-lg bg-white bg-opacity-10 rounded-circle p-3">
                            <i class="ri-nurse-line fs-1 text-white"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Stats -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6">
        <div class="card stat-card h-100">
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <div class="flex-shrink-0">
                        <div class="avatar-md rounded-circle bg-primary-subtle p-2">
                            <i class="ri-rss-line fs-2" style="color: #357980;"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="text-muted mb-0">My Feeds</h6>
                        <h2 class="mb-0 fw-semibold">{{ $my_feeds_count ?? 0 }}</h2>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card stat-card h-100">
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <div class="flex-shrink-0">
                        <div class="avatar-md rounded-circle bg-info-subtle p-2">
                            <i class="ri-play-circle-line fs-2" style="color: #357980;"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="text-muted mb-0">My Reels</h6>
                        <h2 class="mb-0 fw-semibold">{{ $my_reels_count ?? 0 }}</h2>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card stat-card h-100">
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <div class="flex-shrink-0">
                        <div class="avatar-md rounded-circle bg-success-subtle p-2">
                            <i class="ri-book-line fs-2" style="color: #27474A;"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="text-muted mb-0">My Courses</h6>
                        <h2 class="mb-0 fw-semibold">{{ $my_courses_count ?? 0 }}</h2>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card stat-card h-100">
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <div class="flex-shrink-0">
                        <div class="avatar-md rounded-circle bg-warning-subtle p-2">
                            <i class="ri-file-list-line fs-2" style="color: #357980;"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="text-muted mb-0">My Exams</h6>
                        <h2 class="mb-0 fw-semibold">{{ $my_exams_count ?? 0 }}</h2>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-transparent border-bottom">
                <h5 class="card-title mb-0">Quick Actions</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <a href="{{ url('admin/feeds/create') }}" class="btn btn-primary w-100">
                            <i class="ri-add-line me-2"></i>Create Feed
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="{{ url('admin/reels/create') }}" class="btn btn-info w-100">
                            <i class="ri-add-line me-2"></i>Create Reel
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="{{ url('admin/courses') }}" class="btn btn-success w-100">
                            <i class="ri-book-open-line me-2"></i>View Courses
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="{{ url('admin/exams') }}" class="btn btn-warning w-100">
                            <i class="ri-file-list-line me-2"></i>View Exams
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Activity or Other Nurse-Specific Content -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-transparent border-bottom">
                <h5 class="card-title mb-0">Recent Activity</h5>
            </div>
            <div class="card-body">
                <p class="text-muted">Your recent activity will appear here...</p>
                <!-- Add nurse-specific content here -->
            </div>
        </div>
    </div>
</div>

<style>
.welcome-card {
    background: linear-gradient(to left, #508066, #19341a) !important;
    border-radius: 15px;
    margin-bottom: 1.5rem;
    overflow: hidden;
    transition: all 0.3s ease;
}

.welcome-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 30px rgba(59, 116, 168, 0.3);
}

.card {
    border: none;
    box-shadow: 0 0 10px rgba(59, 116, 168, 0.08);
    margin-bottom: 24px;
    border-radius: 12px;
    overflow: hidden;
}
</style>
@endsection
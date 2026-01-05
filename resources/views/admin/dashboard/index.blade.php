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
                        <h3 class="mb-2 fw-semibold">Welcome back, Admin!</h3>
                        <p class="mb-0 opacity-75">Here's your Mandara booking performance overview</p>
                    </div>
                    <div class="flex-shrink-0">
                        <div class="avatar-lg bg-white bg-opacity-10 rounded-circle p-3">
                            <i class="ri-dashboard-line fs-1 text-white"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="fw-semibold mb-0">Quick Actions</h5>
        </div>
    </div>
    
    <!-- Nurses -->
    <div class="col-xl-2 col-md-3 col-sm-4 col-6">
        <a href="{{ route('admin.estore-products.index') }}" class="text-decoration-none">
            <div class="card stat-card quick-action-card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avatar-sm rounded-circle bg-primary-subtle p-2">
                                <i class="ri-nurse-line fs-4" style="color: #357980;"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-2">
                            <h6 class="text-muted mb-0 small">Nurses</h6>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>
    
    <!-- Doctors -->
    <div class="col-xl-2 col-md-3 col-sm-4 col-6">
        <a href="{{ route('admin.estore-products.index') }}" class="text-decoration-none">
            <div class="card stat-card quick-action-card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avatar-sm rounded-circle bg-info-subtle p-2">
                                <i class="ri-hospital-line fs-4" style="color: #357980;"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-2">
                            <h6 class="text-muted mb-0 small">Doctors</h6>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>
    
    <!-- Estore Products -->
    <div class="col-xl-2 col-md-3 col-sm-4 col-6">
        <a href="{{ route('admin.estore-products.index') }}" class="text-decoration-none">
            <div class="card stat-card quick-action-card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avatar-sm rounded-circle bg-success-subtle p-2">
                                <i class="ri-shopping-bag-line fs-4" style="color: #27474A;"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-2">
                            <h6 class="text-muted mb-0 small">Estore Products</h6>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>
    
    <!-- Estore Orders -->
    <div class="col-xl-2 col-md-3 col-sm-4 col-6">
        <a href="{{ route('admin.estore-products.index') }}" class="text-decoration-none">
            <div class="card stat-card quick-action-card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avatar-sm rounded-circle bg-warning-subtle p-2">
                                <i class="ri-shopping-cart-line fs-4" style="color: #357980;"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-2">
                            <h6 class="text-muted mb-0 small">Estore Orders</h6>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>
    
    <!-- Mandara Bookings -->
    <div class="col-xl-2 col-md-3 col-sm-4 col-6">
        <a href="{{ route('admin.mandara-bookings.index') }}" class="text-decoration-none">
            <div class="card stat-card quick-action-card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avatar-sm rounded-circle bg-primary-subtle p-2">
                                <i class="ri-calendar-line fs-4" style="color: #357980;"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-2">
                            <h6 class="text-muted mb-0 small">Bookings</h6>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>
    
    <!-- Clients -->
    <div class="col-xl-2 col-md-3 col-sm-4 col-6">
        <a href="{{ route('admin.clients.index') }}" class="text-decoration-none">
            <div class="card stat-card quick-action-card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avatar-sm rounded-circle bg-info-subtle p-2">
                                <i class="ri-user-line fs-4" style="color: #357980;"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-2">
                            <h6 class="text-muted mb-0 small">Clients</h6>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>
    
    <!-- Cottage Packages -->
    {{-- <div class="col-xl-2 col-md-3 col-sm-4 col-6">
        <a href="{{ route('admin.cottage-packages.index') }}" class="text-decoration-none">
            <div class="card stat-card quick-action-card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avatar-sm rounded-circle bg-success-subtle p-2">
                                <i class="ri-gift-line fs-4" style="color: #27474A;"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-2">
                            <h6 class="text-muted mb-0 small">Cottage Packages</h6>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>
    
    <!-- Meal Packages -->
    <div class="col-xl-2 col-md-3 col-sm-4 col-6">
        <a href="{{ route('admin.meal-packages.index') }}" class="text-decoration-none">
            <div class="card stat-card quick-action-card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avatar-sm rounded-circle bg-warning-subtle p-2">
                                <i class="ri-restaurant-line fs-4" style="color: #357980;"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-2">
                            <h6 class="text-muted mb-0 small">Meal Packages</h6>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>
    
    <!-- Payments -->
    <div class="col-xl-2 col-md-3 col-sm-4 col-6">
        <a href="{{ route('admin.payments.index') }}" class="text-decoration-none">
            <div class="card stat-card quick-action-card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avatar-sm rounded-circle bg-primary-subtle p-2">
                                <i class="ri-money-dollar-circle-line fs-4" style="color: #357980;"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-2">
                            <h6 class="text-muted mb-0 small">Payments</h6>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>
    
    <!-- Notifications -->
    <div class="col-xl-2 col-md-3 col-sm-4 col-6">
        <a href="{{ route('admin.notifications.index') }}" class="text-decoration-none">
            <div class="card stat-card quick-action-card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avatar-sm rounded-circle bg-info-subtle p-2">
                                <i class="ri-notification-line fs-4" style="color: #357980;"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-2">
                            <h6 class="text-muted mb-0 small">Notifications</h6>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div> --}}
</div>

<!-- Key Stats Overview -->
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
    
    <!-- Total Bookings -->
    <div class="col-xl-3 col-md-6">
        <div class="card stat-card h-100">
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <div class="flex-shrink-0">
                        <div class="avatar-md rounded-circle bg-primary-subtle p-2">
                            <i class="ri-calendar-check-line fs-2" style="color: #357980;"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="text-muted mb-0">Total Bookings</h6>
                        <h2 class="mb-0 fw-semibold">{{ $number_of_bookings ?? 0 }}</h2>
                    </div>
                </div>
                <div>
                    <div class="d-flex justify-content-between mb-2">
                        <div>
                            <span class="badge bg-success-subtle px-2 py-1" style="color: #27474A;">
                                <i class="ri-checkbox-circle-line me-1"></i> Approved
                            </span>
                        </div>
                        <div>
                            <span class="fw-semibold fs-5" style="color: #27474A;">{{ $approved_bookings ?? '0' }}</span>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between">
                        <div>
                            <span class="badge bg-light px-2 py-1 text-muted">
                                <i class="ri-time-line me-1"></i> Pending
                            </span>
                        </div>
                        <div>
                            <span class="fw-semibold fs-5 text-muted">{{ $pending_bookings ?? '0' }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Clients -->
    <div class="col-xl-3 col-md-6">
        <div class="card stat-card h-100">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="avatar-md rounded-circle bg-info-subtle p-2">
                            <i class="ri-user-line fs-2" style="color: #357980;"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="text-muted mb-0">Total Clients</h6>
                        <h2 class="mb-0 fw-semibold">{{ $number_of_clients ?? 0 }}</h2>
                        <div class="mt-2">
                            <span class="badge bg-info-subtle p-1 px-2 rounded" style="color: #357980;">
                                <i class="ri-arrow-up-line"></i> 4% from last month
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- New Bookings Today -->
    <div class="col-xl-3 col-md-6">
        <div class="card stat-card h-100">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="avatar-md rounded-circle bg-success-subtle p-2">
                            <i class="ri-calendar-todo-line fs-2" style="color: #27474A;"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="text-muted mb-0">New Bookings Today</h6>
                        <h2 class="mb-0 fw-semibold">{{ $new_bookings_today ?? 0 }}</h2>
                        <div class="mt-2">
                            <span class="badge bg-success-subtle p-1 px-2 rounded" style="color: #27474A;">
                                <i class="ri-arrow-up-line"></i> 8% from yesterday
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- New Bookings This Week -->
    <div class="col-xl-3 col-md-6">
        <div class="card stat-card h-100">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="avatar-md rounded-circle bg-warning-subtle p-2">
                            <i class="ri-calendar-2-line fs-2" style="color: #357980;"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="text-muted mb-0">New Bookings This Week</h6>
                        <h2 class="mb-0 fw-semibold">{{ $new_bookings_week ?? 0 }}</h2>
                        <div class="mt-2">
                            <span class="badge bg-warning-subtle p-1 px-2 rounded" style="color: #357980;">
                                <i class="ri-arrow-up-line"></i> 15% from last week
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Latest Bookings & Latest Users -->
<div class="row mb-4">
    <!-- Latest Bookings -->
    <div class="col-xl-6 col-lg-6">
        <div class="card h-100">
            <div class="card-header bg-transparent border-bottom">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h5 class="card-title mb-0">Latest Bookings</h5>
                    </div>
                    <div class="flex-shrink-0">
                        <a href="{{ route('admin.mandara-bookings.index') }}" class="btn btn-sm btn-soft-primary">
                            <i class="ri-eye-line me-1"></i> View All
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-borderless table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th scope="col">Booking</th>
                                <th scope="col">Client</th>
                                <th scope="col">Package</th>
                                <th scope="col">Amount</th>
                                <th scope="col">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(isset($latest_bookings) && count($latest_bookings) > 0)
                                @foreach($latest_bookings as $booking)
                                <tr>
                                    <td>
                                        <div>
                                            <h6 class="mb-0 fw-medium">{{ $booking->booking_number ?? '-' }}</h6>
                                            <small class="text-muted">{{ \Carbon\Carbon::parse($booking->created_at)->format('d M Y') }}</small>
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            <strong class="d-block">{{ $booking->user->name ?? '-' }}</strong>
                                            @if(isset($booking->user->phone))
                                            <small class="text-muted">{{ $booking->user->phone }}</small>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <span class="fw-medium">{{ $booking->cottagePackage->title ?? '-' }}</span>
                                    </td>
                                    <td>
                                        <span class="fw-semibold">₹ {{ number_format($booking->payable_amount ?? 0, 2) }}</span>
                                    </td>
                                    <td>
                                        @if($booking->approval_status === 'approved')
                                            <span class="badge bg-success-subtle px-2 py-1" style="color: #27474A;">
                                                <i class="ri-checkbox-circle-line me-1"></i> Approved
                                            </span>
                                        @elseif($booking->approval_status === 'rejected')
                                            <span class="badge bg-danger-subtle px-2 py-1 text-danger">
                                                <i class="ri-close-circle-line me-1"></i> Rejected
                                            </span>
                                        @else
                                            <span class="badge bg-warning-subtle px-2 py-1" style="color: #357980;">
                                                <i class="ri-time-line me-1"></i> Pending
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="5" class="text-center py-4">
                                        <div class="mb-3">
                                            <i class="ri-calendar-line text-muted" style="font-size: 3rem; color: #ccc;"></i>
                                        </div>
                                        <p class="text-muted mb-0">No bookings found</p>
                                        <small class="text-muted">Recent bookings will appear here</small>
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Latest Clients -->
    <div class="col-xl-6 col-lg-6">
        <div class="card h-100">
            <div class="card-header bg-transparent border-bottom">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h5 class="card-title mb-0">Latest Clients</h5>
                    </div>
                    <div class="flex-shrink-0">
                        <a href="#" class="btn btn-sm btn-soft-primary">
                            <i class="ri-eye-line me-1"></i> View All
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-borderless table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th scope="col">Client</th>
                                <th scope="col">Contact</th>
                                {{-- <th scope="col">Role</th> --}}
                                <th scope="col">Status</th>
                                <th scope="col">Joined</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(isset($latest_users) && count($latest_users) > 0)
                                @foreach($latest_users as $user)
                                {{-- @dd($user) --}}
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="flex-shrink-0">
                                                @if($user->profile_picture_url)
                                                    <img src="{{ $user->profile_picture_url }}" alt="{{ $user->name }}" class="avatar-sm rounded-circle">
                                                @else
                                                    <div class="avatar-sm bg-primary-subtle rounded-circle d-flex align-items-center justify-content-center">
                                                        <span class="text-primary fw-semibold" style="color: #357980 !important;">
                                                            {{ strtoupper(substr($user->name ?? 'U', 0, 1)) }}
                                                        </span>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="ms-2">
                                                <h6 class="mb-0 fw-medium">{{ $user->name ?? '-' }}</h6>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            @if($user->email)
                                            <small class="d-block text-muted">{{ $user->email }}</small>
                                            @endif
                                            @if($user->phone)
                                            <small class="text-muted">{{ $user->country_code ?? '' }}{{ $user->phone }}</small>
                                            @endif
                                        </div>
                                    </td>
                                    {{-- <td>
                                        @php
                                            $roleLabels = [
                                                1 => 'Admin',
                                                2 => 'Client',
                                                3 => 'Doctor',
                                                4 => 'Nurse',
                                                5 => 'Attendant',
                                                6 => 'Delivery Staff'
                                            ];
                                            $roleLabel = $roleLabels[$user->role_id ?? 2] ?? 'Client';
                                        @endphp
                                        <span class="badge bg-info-subtle px-2 py-1" style="color: #357980;">
                                            {{ $roleLabel }}
                                        </span>
                                    </td> --}}
                                    <td>
                                        @if(($user->status ?? 'active') === 'active')
                                            <span class="badge bg-success-subtle px-2 py-1" style="color: #27474A;">
                                                <i class="ri-checkbox-circle-line me-1"></i> Active
                                            </span>
                                        @else
                                            <span class="badge bg-light px-2 py-1 text-muted">
                                                <i class="ri-close-circle-line me-1"></i> Inactive
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <small class="text-muted">{{ \Carbon\Carbon::parse($user->created_at)->format('d M Y') }}</small>
                                    </td>
                                </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="5" class="text-center py-4">
                                        <div class="mb-3">
                                            <i class="ri-user-line text-muted" style="font-size: 3rem; color: #ccc;"></i>
                                        </div>
                                        <p class="text-muted mb-0">No users found</p>
                                        <small class="text-muted">Recent users will appear here</small>
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Booking Analytics -->
<div class="row mb-4">
    <div class="col-xl-7">
        <div class="card">
            <div class="card-header bg-transparent border-bottom">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h5 class="card-title mb-0">Booking Analytics</h5>
                    </div>
                    <div class="flex-shrink-0">
                        <div class="dropdown">
                            <button class="btn btn-sm btn-soft-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                Last 12 Months
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="#">Last 6 Months</a></li>
                                <li><a class="dropdown-item" href="#">Last 12 Months</a></li>
                                <li><a class="dropdown-item" href="#">This Year</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body pt-3">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h2 class="mb-0">{{ $total_bookings ?? '1,248' }}</h2>
                        <p class="text-muted mb-0">Total Bookings</p>
                    </div>
                    <div>
                        <span class="badge bg-success-subtle p-2 px-3 rounded" style="color: #27474A;">
                            <i class="ri-arrow-up-line me-1"></i> 24.8% growth
                        </span>
                    </div>
                </div>
                <div id="student-registration-chart" class="apex-charts" style="height: 300px;"></div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-5">
        <div class="card">
            <div class="card-header bg-transparent border-bottom">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h5 class="card-title mb-0">Package-wise Bookings</h5>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div id="course-students-chart" class="apex-charts" style="height: 250px;"></div>
                <div class="mt-4">
                    @if(isset($package_data) && !empty($package_data))
                        @foreach($package_data as $index => $package)
                        <div class="d-flex justify-content-between mb-3">
                            <div class="d-flex align-items-center">
                                <i class="ri-checkbox-blank-circle-fill text-primary me-2" style="color: #357980 !important;"></i>
                                <span>{{ $package['title'] }}</span>
                            </div>
                            <div>
                                <span class="fw-medium">{{ $package['bookings'] }}</span>
                                <span class="text-muted ms-2">({{ $package['approved'] ?? 0 }} approved)</span>
                            </div>
                        </div>
                        @endforeach
                    @else
                        <div class="text-center py-4">
                            <div class="mb-3">
                                <i class="ri-home-line text-muted" style="font-size: 3rem; color: #ccc;"></i>
                            </div>
                            <p class="text-muted mb-0">No package data available</p>
                            <small class="text-muted">Package statistics will appear here once bookings start coming in</small>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.welcome-card {
    background: linear-gradient(to left, #508066, #19341a) !important
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

.card-header {
    background-color: transparent;
    border-bottom: 1px solid rgba(59, 116, 168, 0.1);
    padding: 1.25rem 1.5rem;
}

.btn-soft-primary {
    background-color: rgba(59, 116, 168, 0.1);
    color: #3B74A8;
    border: 1px solid rgba(59, 116, 168, 0.2);
    transition: all 0.3s ease;
}

.btn-soft-primary:hover {
    background-color: rgba(59, 116, 168, 0.2);
    color: #1C3E68;
    border-color: #3B74A8;
    transform: translateY(-1px);
}

.dropdown-toggle::after {
    display: none;
}

.badge {
    font-weight: 500;
    padding: 0.5rem 0.8rem;
    border-radius: 30px;
}

.fs-11 {
    font-size: 11px;
}

.fs-13 {
    font-size: 13px;
}

.bg-gradient-primary {
    background: linear-gradient(to right, #3B74A8, #1C3E68) !important;
}

.rounded-pill {
    border-radius: 50rem;
}

a {
    text-decoration: none;
    color: inherit;
}

a:hover {
    text-decoration: none;
}

.text-decoration-none:hover {
    color: inherit;
}

.table>:not(caption)>*>* {
    padding: 1rem 1.25rem;
    border-bottom-width: 1px;
}

.table>:not(:first-child) {
    border-top: 0;
}

.text-primary {
    color: #3B74A8 !important;
}

.text-success {
    color: #1C3E68 !important;
}

.progress {
    background-color: rgba(59, 116, 168, 0.1);
    border-radius: 10px;
    height: 8px;
}

.progress-bar {
    border-radius: 10px;
    background: linear-gradient(to right, #3B74A8, #1C3E68) !important;
}


/* Update badge colors */
.badge.bg-success-subtle {
    background-color: rgba(28, 62, 104, 0.1) !important;
    color: #1C3E68 !important;
}

.badge.bg-info-subtle {
    background-color: rgba(59, 116, 168, 0.1) !important;
    color: #3B74A8 !important;
}

.badge.bg-warning-subtle {
    background-color: rgba(59, 116, 168, 0.1) !important;
    color: #3B74A8 !important;
}

.badge.bg-primary-subtle {
    background-color: rgba(59, 116, 168, 0.1) !important;
    color: #3B74A8 !important;
}

.badge.bg-danger-subtle {
    background-color: rgba(220, 53, 69, 0.1) !important;
    color: #dc3545 !important;
}

/* Update table avatar colors */
.avatar-sm.bg-primary-subtle {
    background-color: rgba(59, 116, 168, 0.1) !important;
}

.avatar-title.bg-primary-subtle {
    background-color: rgba(59, 116, 168, 0.1) !important;
    color: #3B74A8 !important;
}

/* Update chart colors in JavaScript */

/* Quick Action Cards */
.quick-action-card {
    transition: all 0.3s ease;
    cursor: pointer;
}

.quick-action-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 20px rgba(59, 116, 168, 0.15);
}

.quick-action-card .card-body {
    padding: 0.75rem;
}

.quick-action-card a {
    color: inherit;
}

.quick-action-card:hover h6 {
    color: #3B74A8;
}
</style>

<script>
// Booking Analytics Chart
var studentRegOptions = {
    series: [{
        name: 'Total Bookings',
        data: {!! json_encode($booking_registrations ?? [82, 68, 90, 110, 120, 94, 115, 130, 135, 145, 155, 170]) !!}
    }, {
        name: 'Approved Bookings',
        data: {!! json_encode($approved_bookings_monthly ?? [45, 52, 68, 74, 96, 72, 90, 98, 110, 115, 125, 132]) !!}
    }],
    chart: {
        type: 'bar',
        height: 300,
        toolbar: {
            show: false
        },
        fontFamily: "'Inter', sans-serif",
    },
    plotOptions: {
        bar: {
            horizontal: false,
            columnWidth: '60%',
            borderRadius: 5
        },
    },
    colors: ['#3B74A8', '#1C3E68'],
    dataLabels: {
        enabled: false
    },
    stroke: {
        show: true,
        width: 2,
        colors: ['transparent']
    },
    grid: {
        borderColor: '#f1f1f1',
        strokeDashArray: 4
    },
    xaxis: {
        categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
        labels: {
            style: {
                colors: '#888888',
                fontSize: '12px',
                fontFamily: "'Inter', sans-serif",
                fontWeight: 400
            }
        }
    },
    yaxis: {
        title: {
            text: 'Number of Bookings',
            style: {
                color: '#888888',
                fontSize: '12px',
                fontFamily: "'Inter', sans-serif",
                fontWeight: 400
            }
        },
        labels: {
            style: {
                colors: '#888888',
                fontSize: '12px',
                fontFamily: "'Inter', sans-serif",
                fontWeight: 400
            }
        }
    },
    legend: {
        position: 'top',
        horizontalAlign: 'right',
        fontFamily: "'Inter', sans-serif",
        fontSize: '14px',
        offsetY: 0
    },
    fill: {
        opacity: 1
    },
    tooltip: {
        y: {
            formatter: function (val) {
                return val + " bookings"
            }
        }
    }
};

var studentRegChart = new ApexCharts(document.querySelector("#student-registration-chart"), studentRegOptions);
studentRegChart.render();

// Package-wise Bookings Chart
var courseStudentsOptions = {
    series: {!! json_encode($package_chart_data['series'] ?? []) !!},
    chart: {
        type: 'donut',
        height: 250,
        fontFamily: "'Inter', sans-serif",
    },
    labels: {!! json_encode($package_chart_data['labels'] ?? []) !!},
    colors: ['#3B74A8', '#1C3E68', '#5A9FD4', '#2E5A8A', '#4A7BA7'],
    plotOptions: {
        pie: {
            donut: {
                size: '65%',
                background: 'transparent',
                labels: {
                    show: true,
                    name: {
                        show: true,
                        fontSize: '14px',
                        fontFamily: "'Inter', sans-serif",
                        color: '#343a40',
                        offsetY: -10
                    },
                    value: {
                        show: true,
                        fontSize: '18px',
                        fontFamily: "'Inter', sans-serif",
                        color: '#343a40',
                        formatter: function (val) {
                            return val
                        }
                    },
                    total: {
                        show: true,
                        label: 'Total',
                        fontSize: '14px',
                        fontFamily: "'Inter', sans-serif",
                        color: '#888888',
                        formatter: function (w) {
                            return w.globals.seriesTotals.reduce((a, b) => a + b, 0)
                        }
                    }
                }
            }
        }
    },
    dataLabels: {
        enabled: false
    },
    legend: {
        show: false
    },
    responsive: [{
        breakpoint: 480,
        options: {
            chart: {
                height: 250
            },
            legend: {
                show: false
            }
        }
    }],
    stroke: {
        width: 0
    }
};

var courseStudentsChart = new ApexCharts(document.querySelector("#course-students-chart"), courseStudentsOptions);
courseStudentsChart.render();

// Add responsive behavior for mobile
window.addEventListener('resize', function() {
    var width = window.innerWidth;
    if (width < 768) {
        // Adjust cards for mobile view
        document.querySelectorAll('.stat-card .d-flex').forEach(function(el) {
            el.classList.add('flex-column');
            el.classList.add('align-items-start');
        });
    } else {
        // Reset for desktop
        document.querySelectorAll('.stat-card .d-flex').forEach(function(el) {
            el.classList.remove('flex-column');
            el.classList.remove('align-items-start');
        });
    }
});

// Initialize any tooltips
document.addEventListener('DOMContentLoaded', function() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
</script>
@endsection
@extends('admin.layouts.app')

@section('title', 'Coupon Details')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ url('admin/dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.coupons.index') }}">Coupons</a></li>
                        <li class="breadcrumb-item active">Coupon Details</li>
                    </ol>
                </div>
                <h4 class="page-title">Coupon Details</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{ $coupon->title }}</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Coupon Code:</strong></td>
                                    <td>
                                        <span class="badge bg-primary fs-6">{{ $coupon->code }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Status:</strong></td>
                                    <td>
                                        <span class="badge bg-{{ $coupon->status === 'active' ? 'success' : 'secondary' }}">
                                            {{ ucfirst($coupon->status) }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Discount Type:</strong></td>
                                    <td>{{ ucfirst($coupon->discount_type) }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Discount Value:</strong></td>
                                    <td>
                                        <span class="badge bg-{{ $coupon->discount_type === 'percentage' ? 'primary' : 'success' }}">
                                            {{ $coupon->discount_type === 'percentage' ? $coupon->discount_value . '%' : '₹' . $coupon->discount_value }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Start Date:</strong></td>
                                    <td>{{ $coupon->start_date->format('M d, Y') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>End Date:</strong></td>
                                    <td>{{ $coupon->end_date->format('M d, Y') }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Usage Limit:</strong></td>
                                    <td>{{ $coupon->usage_limit ? $coupon->usage_limit : 'Unlimited' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Per User Limit:</strong></td>
                                    <td>{{ $coupon->per_user_limit ? $coupon->per_user_limit : 'Unlimited' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Total Usage:</strong></td>
                                    <td>{{ $coupon->usages->count() }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Created:</strong></td>
                                    <td>{{ $coupon->created_at->format('M d, Y H:i') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Updated:</strong></td>
                                    <td>{{ $coupon->updated_at->format('M d, Y H:i') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    @if($coupon->description)
                    <div class="mt-3">
                        <h6>Description:</h6>
                        <p class="text-muted">{{ $coupon->description }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Packages Section -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Applicable Packages</h5>
                </div>
                <div class="card-body">
                    @if($coupon->packages->count() > 0)
                        <div class="row">
                            @foreach($coupon->packages as $package)
                            <div class="col-md-6 mb-3">
                                <div class="border rounded p-3">
                                    <h6 class="mb-1">{{ $package->title }}</h6>
                                    <p class="text-muted mb-2">{{ Str::limit($package->description, 100) }}</p>
                                    <div class="d-flex justify-content-between">
                                        <span class="badge bg-success">₹{{ $package->price }}</span>
                                        @if($package->offer_price)
                                            <span class="badge bg-warning">Offer: ₹{{ $package->offer_price }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted">This coupon applies to all packages.</p>
                    @endif
                </div>
            </div>

            <!-- Users Section -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Restricted Users</h5>
                </div>
                <div class="card-body">
                    @if($coupon->users->count() > 0)
                        <div class="row">
                            @foreach($coupon->users as $user)
                            <div class="col-md-6 mb-3">
                                <div class="border rounded p-3">
                                            <h6 class="mb-1">{{ $user->name }}{{ $user->phone ? ' [+' . $user->country_code . ' ' . $user->phone . ']' : '' }}</h6>
                                            <p class="text-muted mb-0">{{ $user->email }}</p>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted">This coupon is available to all users.</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Usage Statistics -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Usage Statistics</h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6">
                            <h3 class="text-primary">{{ $coupon->usages->count() }}</h3>
                            <p class="text-muted mb-0">Total Usage</p>
                        </div>
                        <div class="col-6">
                            <h3 class="text-success">{{ $coupon->usages->groupBy('user_id')->count() }}</h3>
                            <p class="text-muted mb-0">Unique Users</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.coupons.edit', $coupon->id) }}" class="btn btn-primary">
                            <i class="mdi mdi-pencil"></i> Edit Coupon
                        </a>
                        <a href="{{ route('admin.coupons.packages.index', $coupon->id) }}" class="btn btn-success">
                            <i class="mdi mdi-package-variant"></i> Manage Packages
                        </a>
                        <a href="{{ route('admin.coupons.users.index', $coupon->id) }}" class="btn btn-warning">
                            <i class="mdi mdi-account-multiple"></i> Manage Users
                        </a>
                        <a href="{{ route('admin.coupons.usage-stats', $coupon->id) }}" class="btn btn-info">
                            <i class="mdi mdi-chart-line"></i> Usage Statistics
                        </a>
                        <form action="{{ route('admin.coupons.toggle-status', $coupon->id) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-{{ $coupon->status === 'active' ? 'warning' : 'success' }} w-100">
                                <i class="mdi mdi-{{ $coupon->status === 'active' ? 'pause' : 'play' }}"></i>
                                {{ $coupon->status === 'active' ? 'Deactivate' : 'Activate' }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Usage -->
    @if($coupon->usages->count() > 0)
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Recent Usage</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>User</th>
                                    <th>Order</th>
                                    <th>Used At</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($coupon->usages->take(10) as $usage)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="flex-grow-1">
                                                <h6 class="mb-0">{{ $usage->user->name }}</h6>
                                                <small class="text-muted">{{ $usage->user->email }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @if($usage->order)
                                            <span class="badge bg-primary">#{{ $usage->order->order_number }}</span>
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                    <td>{{ $usage->used_at->format('M d, Y H:i') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection

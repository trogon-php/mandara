@extends('admin.layouts.app')

@section('title', 'Coupon Packages')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ url('admin/dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.coupons.index') }}">Coupons</a></li>
                        <li class="breadcrumb-item active">Package Management</li>
                    </ol>
                </div>
                <h4 class="page-title">Package Management - {{ $coupon->title }}</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <!-- Attached Packages -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Attached Packages ({{ $coupon->packages->count() }})</h5>
                </div>
                <div class="card-body">
                    @if($coupon->packages->count() > 0)
                        <div class="row">
                            @foreach($coupon->packages as $package)
                            <div class="col-md-6 mb-3">
                                <div class="border rounded p-3">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1">{{ $package->title }}</h6>
                                            <p class="text-muted mb-2">{{ Str::limit($package->description, 100) }}</p>
                                            <div class="d-flex gap-2">
                                                <span class="badge bg-success">₹{{ $package->price }}</span>
                                                @if($package->offer_price)
                                                    <span class="badge bg-warning">Offer: ₹{{ $package->offer_price }}</span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                <i class="mdi mdi-dots-vertical"></i>
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li>
                                                    <form action="{{ route('admin.coupons.packages.destroy', [$coupon->id, $package->id]) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="dropdown-item text-danger" onclick="return confirm('Are you sure?')">
                                                            <i class="mdi mdi-delete"></i> Detach
                                                        </button>
                                                    </form>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="mdi mdi-package-variant-closed text-muted" style="font-size: 3rem;"></i>
                            <h5 class="mt-3">No packages attached</h5>
                            <p class="text-muted">This coupon applies to all packages.</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Available Packages -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Available Packages ({{ $availablePackages->count() }})</h5>
                </div>
                <div class="card-body">
                    @if($availablePackages->count() > 0)
                        <form action="{{ route('admin.coupons.packages.store', $coupon->id) }}" method="POST" id="attach-packages-form">
                            @csrf
                            <div class="row">
                                @foreach($availablePackages as $package)
                                <div class="col-md-6 mb-3">
                                    <div class="border rounded p-3">
                                        <div class="form-check">
                                            <input class="form-check-input package-checkbox" type="checkbox" name="package_ids[]" value="{{ $package->id }}" id="package_{{ $package->id }}">
                                            <label class="form-check-label w-100" for="package_{{ $package->id }}">
                                                <h6 class="mb-1">{{ $package->title }}</h6>
                                                <p class="text-muted mb-2">{{ Str::limit($package->description, 100) }}</p>
                                                <div class="d-flex gap-2">
                                                    <span class="badge bg-success">₹{{ $package->price }}</span>
                                                    @if($package->offer_price)
                                                        <span class="badge bg-warning">Offer: ₹{{ $package->offer_price }}</span>
                                                    @endif
                                                </div>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            <div class="mt-3">
                                <button type="submit" class="btn btn-primary" id="attach-btn" disabled>
                                    <i class="mdi mdi-plus"></i> Attach Selected Packages
                                </button>
                                <button type="button" class="btn btn-outline-secondary" id="select-all-btn">
                                    <i class="mdi mdi-check-all"></i> Select All
                                </button>
                            </div>
                        </form>
                    @else
                        <div class="text-center py-4">
                            <i class="mdi mdi-package-variant text-muted" style="font-size: 3rem;"></i>
                            <h5 class="mt-3">All packages are attached</h5>
                            <p class="text-muted">No more packages available to attach.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Coupon Info -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Coupon Information</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="flex-grow-1">
                            <h6 class="mb-1">{{ $coupon->title }}</h6>
                            <span class="badge bg-primary">{{ $coupon->code }}</span>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Status:</span>
                        <span class="badge bg-{{ $coupon->status === 'active' ? 'success' : 'secondary' }}">
                            {{ ucfirst($coupon->status) }}
                        </span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Discount:</span>
                        <span class="badge bg-{{ $coupon->discount_type === 'percentage' ? 'primary' : 'success' }}">
                            {{ $coupon->discount_type === 'percentage' ? $coupon->discount_value . '%' : '₹' . $coupon->discount_value }}
                        </span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Valid Until:</span>
                        <span>{{ $coupon->end_date->format('M d, Y') }}</span>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.coupons.show', $coupon->id) }}" class="btn btn-outline-primary">
                            <i class="mdi mdi-eye"></i> View Coupon
                        </a>
                        <a href="{{ route('admin.coupons.edit', $coupon->id) }}" class="btn btn-outline-secondary">
                            <i class="mdi mdi-pencil"></i> Edit Coupon
                        </a>
                        <a href="{{ route('admin.coupons.users.index', $coupon->id) }}" class="btn btn-outline-warning">
                            <i class="mdi mdi-account-multiple"></i> Manage Users
                        </a>
                        <a href="{{ route('admin.coupons.index') }}" class="btn btn-outline-info">
                            <i class="mdi mdi-arrow-left"></i> Back to Coupons
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const checkboxes = document.querySelectorAll('.package-checkbox');
    const attachBtn = document.getElementById('attach-btn');
    const selectAllBtn = document.getElementById('select-all-btn');
    let allSelected = false;

    // Update attach button state
    function updateAttachButton() {
        const checkedBoxes = document.querySelectorAll('.package-checkbox:checked');
        attachBtn.disabled = checkedBoxes.length === 0;
        attachBtn.textContent = checkedBoxes.length > 0 
            ? `Attach ${checkedBoxes.length} Package(s)` 
            : 'Attach Selected Packages';
    }

    // Select all functionality
    selectAllBtn.addEventListener('click', function() {
        allSelected = !allSelected;
        checkboxes.forEach(checkbox => {
            checkbox.checked = allSelected;
        });
        updateAttachButton();
        selectAllBtn.innerHTML = allSelected 
            ? '<i class="mdi mdi-close"></i> Deselect All'
            : '<i class="mdi mdi-check-all"></i> Select All';
    });

    // Individual checkbox change
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateAttachButton);
    });

    // Form submission
    document.getElementById('attach-packages-form').addEventListener('submit', function(e) {
        const checkedBoxes = document.querySelectorAll('.package-checkbox:checked');
        if (checkedBoxes.length === 0) {
            e.preventDefault();
            alert('Please select at least one package to attach.');
        }
    });
});
</script>
@endsection



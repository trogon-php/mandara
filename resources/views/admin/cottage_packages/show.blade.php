<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Package Details</h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Title:</label>
                            <p class="text-muted">{{ $package->title }}</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Status:</label>
                            <span class="badge bg-{{ $package->status === 'active' ? 'success' : 'secondary' }}">
                                {{ ucfirst($package->status) }}
                            </span>
                        </div>
                    </div>
                </div>

                @if($package->description)
                <div class="mb-3">
                    <label class="form-label fw-semibold">Description:</label>
                    <p class="text-muted">{{ $package->description }}</p>
                </div>
                @endif

                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Price:</label>

                            @if($package->hasDiscount())
                                <p class="text-muted text-decoration-line-through">₹{{ number_format($package->price, 2) }}</p>
                                <p class="text-success fw-bold mb-0">
                                    Payable: ₹{{ number_format($package->effective_price, 2) }}
                                </p>
                                <p class="text-success mb-0 small">
                                    Discount: ₹{{ number_format($package->discount_amount, 2) }}
                                    ({{ number_format($package->discount_percentage, 2) }}% off)
                                </p>
                            @else
                                <p class="text-muted">₹{{ number_format($package->price, 2) }}</p>
                            @endif
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Booking Amount:</label>
                            <p class="text-muted">₹{{ number_format($package->booking_amount, 2) }}</p>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Tax Included?</label>
                            <p class="text-muted">{{ $package->tax_included ? 'Yes (Included)' : 'No (Excluded)' }}</p>
                        </div>
                    </div>
                </div>

                <div class="row">
                    @if($package->duration_days)
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Duration:</label>
                            <p class="text-muted">{{ $package->duration_days }} days</p>
                        </div>
                    </div>
                    @endif
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Created:</label>
                            <p class="text-muted">{{ $package->created_at->format('M d, Y h:i A') }}</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Last Updated:</label>
                            <p class="text-muted">{{ $package->updated_at->format('M d, Y h:i A') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

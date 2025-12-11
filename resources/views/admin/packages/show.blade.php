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
                            @if($package->hasOffer())
                                <p class="text-muted text-decoration-line-through">₹{{ number_format($package->price, 2) }}</p>
                            @else
                                <p class="text-muted">₹{{ number_format($package->price, 2) }}</p>
                            @endif
                        </div>
                    </div>
                    @if($package->hasOffer())
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Offer Price:</label>
                            <p class="text-success fw-bold">₹{{ number_format($package->offer_price, 2) }}</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Discount:</label>
                            <p class="text-success fw-bold">{{ $package->discount_percentage }}% off</p>
                        </div>
                    </div>
                    @endif
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
                    @if($package->expire_date)
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Expire Date:</label>
                            <p class="text-muted">{{ $package->expire_date->format('M d, Y') }}</p>
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

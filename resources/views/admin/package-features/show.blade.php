<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Package Feature Details</h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Feature Title</label>
                            <p class="form-control-plaintext">{{ $packageFeature->title }}</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Status</label>
                            <p class="form-control-plaintext">
                                <span class="badge bg-{{ $packageFeature->status === 'active' ? 'success' : 'secondary' }}">
                                    {{ ucfirst($packageFeature->status) }}
                                </span>
                            </p>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Package</label>
                            <p class="form-control-plaintext">
                                @if($packageFeature->package)
                                    <a href="{{ route('admin.packages.show', $packageFeature->package->id) }}" class="text-decoration-none">
                                        {{ $packageFeature->package->title }}
                                    </a>
                                @else
                                    <span class="text-muted">No package assigned</span>
                                @endif
                            </p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Sort Order</label>
                            <p class="form-control-plaintext">{{ $packageFeature->sort_order ?? '-' }}</p>
                        </div>
                    </div>
                </div>

                @if($packageFeature->description)
                    <div class="row">
                        <div class="col-12">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Description</label>
                                <p class="form-control-plaintext">{{ $packageFeature->description }}</p>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Created At</label>
                            <p class="form-control-plaintext">{{ $packageFeature->created_at->format('M d, Y h:i A') }}</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Updated At</label>
                            <p class="form-control-plaintext">{{ $packageFeature->updated_at->format('M d, Y h:i A') }}</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Created By</label>
                            <p class="form-control-plaintext">
                                @if($packageFeature->creator)
                                    {{ $packageFeature->creator->name }}
                                @else
                                    <span class="text-muted">System</span>
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.package-features.edit', $packageFeature->id) }}" 
                       class="btn btn-primary" 
                       onclick="showAjaxModal(this.href, 'Edit Package Feature'); return false;">
                        <i class="ri-edit-line"></i> Edit
                    </a>
                    <a href="{{ route('admin.package-features.index') }}" class="btn btn-secondary">
                        <i class="ri-arrow-left-line"></i> Back to List
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>


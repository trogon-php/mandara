
{!! show_window_title('Package Item Details') !!}

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Package Item Information</h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Package:</label>
                            <p class="form-control-plaintext">
                                <span class="badge bg-primary">{{ $item->package->title ?? 'N/A' }}</span>
                            </p>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Item Type:</label>
                            <p class="form-control-plaintext">
                                <span class="badge bg-info">{{ ucfirst(str_replace('_', ' ', $item->item_type)) }}</span>
                            </p>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Item Title:</label>
                            <p class="form-control-plaintext">{{ $item->item_title ?? 'N/A' }}</p>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Status:</label>
                            <p class="form-control-plaintext">
                                @if($item->status === 'active')
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-secondary">Inactive</span>
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Sort Order:</label>
                            <p class="form-control-plaintext">{{ $item->sort_order ?? 0 }}</p>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Created:</label>
                            <p class="form-control-plaintext">{{ $item->created_at ? $item->created_at->format('M d, Y H:i') : 'N/A' }}</p>
                        </div>
                    </div>
                </div>
                
                @if($item->item)
                    <div class="row">
                        <div class="col-12">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Item Details:</label>
                                <div class="card">
                                    <div class="card-body">
                                        @if($item->item_type === 'category')
                                            <h6 class="card-title">{{ $item->item->name ?? 'N/A' }}</h6>
                                            <p class="card-text">{{ $item->item->description ?? 'No description available' }}</p>
                                        @elseif($item->item_type === 'course')
                                            <h6 class="card-title">{{ $item->item->title ?? 'N/A' }}</h6>
                                            <p class="card-text">{{ $item->item->description ?? 'No description available' }}</p>
                                        @elseif($item->item_type === 'course_unit')
                                            <h6 class="card-title">{{ $item->item->title ?? 'N/A' }}</h6>
                                            <p class="card-text">{{ $item->item->description ?? 'No description available' }}</p>
                                        @elseif($item->item_type === 'course_material')
                                            <h6 class="card-title">{{ $item->item->title ?? 'N/A' }}</h6>
                                            <p class="card-text">
                                                <strong>Type:</strong> {{ $item->item->type ?? 'N/A' }}<br>
                                                {{ $item->item->description ?? 'No description available' }}
                                            </p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">System Information</h4>
            </div>
            <div class="card-body">
                <p><strong>Created:</strong> {{ $item->created_at ? $item->created_at->format('d-m-Y, g:i A') : 'N/A' }}</p>
                <p><strong>Updated:</strong> {{ $item->updated_at ? $item->updated_at->format('d-m-Y, g:i A') : 'N/A' }}</p>
                @if($item->createdBy)
                    <p><strong>Created By:</strong> {{ $item->createdBy->name }}</p>
                @endif
                @if($item->updatedBy)
                    <p><strong>Updated By:</strong> {{ $item->updatedBy->name }}</p>
                @endif
            </div>
        </div>
        
        <div class="card mt-3">
            <div class="card-header">
                <h4 class="card-title mb-0">Actions</h4>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('admin.package-items.edit', $item->id) }}" class="btn btn-primary">
                        <i class="mdi mdi-pencil"></i> Edit Package Item
                    </a>
                    <a href="{{ route('admin.packages.show', $item->package_id) }}" class="btn btn-info">
                        <i class="mdi mdi-package-variant"></i> View Package
                    </a>
                    <a href="{{ route('admin.package-items.index') }}" class="btn btn-secondary">
                        <i class="mdi mdi-arrow-left"></i> Back to Package Items
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
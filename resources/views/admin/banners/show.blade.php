<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Banner Details</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6>Title</h6>
                        <p>{{ $item->title }}</p>
                    </div>
                    <div class="col-md-6">
                        <h6>Status</h6>
                        @if($item->status)
                            <span class="badge bg-success">Active</span>
                        @else
                            <span class="badge bg-secondary">Inactive</span>
                        @endif
                    </div>
                </div>
                
                @if($item->description)
                <div class="row">
                    <div class="col-12">
                        <h6>Description</h6>
                        <p>{{ $item->description }}</p>
                    </div>
                </div>
                @endif
                
                <div class="row">
                    <div class="col-md-6">
                        <h6>Action Type</h6>
                        <span class="badge bg-{{ $item->action_type === 'video' ? 'primary' : ($item->action_type === 'link' ? 'info' : ($item->action_type === 'course' ? 'success' : 'secondary')) }}">
                            {{ ucfirst($item->action_type) }}
                        </span>
                    </div>
                    <div class="col-md-6">
                        <h6>Sort Order</h6>
                        <span class="badge bg-light text-dark">{{ $item->sort_order }}</span>
                    </div>
                </div>
                
                @if($item->action_value)
                <div class="row">
                    <div class="col-12">
                        <h6>Action Value</h6>
                        <p>{{ $item->action_value }}</p>
                    </div>
                </div>
                @endif
                
                <div class="row">
                    <div class="col-md-6">
                        <h6>Created</h6>
                        <p>{{ $item->created_at->format('M d, Y H:i') }}</p>
                    </div>
                    <div class="col-md-6">
                        <h6>Updated</h6>
                        <p>{{ $item->updated_at->format('M d, Y H:i') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Banner Image</h5>
            </div>
            <div class="card-body text-center">
                @if($item->image)
                    <img src="{{ $item->image_url }}" alt="{{ $item->title }}" class="img-fluid rounded" style="max-height: 300px;">
                @else
                    <div class="text-muted">
                        <i class="fas fa-image fa-3x"></i>
                        <p>No image uploaded</p>
                    </div>
                @endif
            </div>
        </div>
        
        <div class="card mt-3">
            <div class="card-header">
                <h5 class="card-title mb-0">Actions</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('admin.banners.edit', $item->id) }}" class="btn btn-primary">
                        <i class="fas fa-edit"></i> Edit Banner
                    </a>
                    <button type="button" class="btn btn-success clone-btn" data-id="{{ $item->id }}">
                        <i class="fas fa-copy"></i> Clone Banner
                    </button>
                    <button type="button" class="btn btn-danger delete-btn" data-id="{{ $item->id }}">
                        <i class="fas fa-trash"></i> Delete Banner
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>


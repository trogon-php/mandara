@extends('admin.layouts.app')
@section('content')

{{ show_window_title($page_title) }}

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Role Details</h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h5>Title</h5>
                        <p class="text-muted">{{ $item->title }}</p>
                    </div>
                    <div class="col-md-6">
                        <h5>Status</h5>
                        @if ($item->status)
                            <span class="badge bg-success"><i class="mdi mdi-check"></i> Active</span>
                        @else
                            <span class="badge bg-danger"><i class="mdi mdi-close"></i> Inactive</span>
                        @endif
                    </div>
                </div>
                
                <div class="row mt-3">
                    <div class="col-12">
                        <h5>Description</h5>
                        <p class="text-muted">{{ $item->description }}</p>
                    </div>
                </div>

                @if(in_array($item->id, [1, 2, 3]))
                <div class="row mt-3">
                    <div class="col-12">
                        <div class="alert alert-info">
                            <i class="mdi mdi-shield-check me-2"></i>
                            <strong>System Role:</strong> This is a system role and cannot be edited or deleted.
                        </div>
                    </div>
                </div>
                @endif

                <div class="row mt-3">
                    <div class="col-md-6">
                        <h5>Created</h5>
                        <p class="text-muted">{{ $item->created_at->format('M d, Y H:i') }}</p>
                    </div>
                    <div class="col-md-6">
                        <h5>Last Updated</h5>
                        <p class="text-muted">{{ $item->updated_at->format('M d, Y H:i') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="mt-3">
    <a href="{{ url('admin/roles') }}" class="btn btn-primary">
        <i class="mdi mdi-arrow-left me-1"></i> Back to Roles
    </a>
</div>

@endsection
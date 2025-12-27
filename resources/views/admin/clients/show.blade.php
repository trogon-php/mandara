@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Client Details</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ url('admin/dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.clients.index') }}">Clients</a></li>
                        <li class="breadcrumb-item active">View Client</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Client Information</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <strong>Name:</strong>
                        </div>
                        <div class="col-md-9">
                            {{ $item->name }}
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <strong>Email:</strong>
                        </div>
                        <div class="col-md-9">
                            {{ $item->email ?? 'N/A' }}
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <strong>Phone:</strong>
                        </div>
                        <div class="col-md-9">
                            @if($item->phone)
                                @if($item->country_code)
                                    +{{ $item->country_code }} 
                                @endif
                                {{ $item->phone }}
                            @else
                                N/A
                            @endif
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <strong>Status:</strong>
                        </div>
                        <div class="col-md-9">
                            @if ($item->status == 'active')
                                <span class="badge bg-success">Active</span>
                            @elseif ($item->status == 'pending')
                                <span class="badge bg-warning">Pending</span>
                            @else
                                <span class="badge bg-danger">Blocked</span>
                            @endif
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <strong>Created At:</strong>
                        </div>
                        <div class="col-md-9">
                            {{ $item->created_at->format('d-m-Y, g:i A') }}
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <strong>Updated At:</strong>
                        </div>
                        <div class="col-md-9">
                            {{ $item->updated_at->format('d-m-Y, g:i A') }}
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <a href="{{ route('admin.clients.edit', $item->id) }}" class="btn btn-primary">
                        <i class="mdi mdi-pencil"></i> Edit Client
                    </a>
                    <a href="{{ route('admin.clients.index') }}" class="btn btn-secondary">
                        <i class="mdi mdi-arrow-left"></i> Back to List
                    </a>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Profile Picture</h5>
                </div>
                <div class="card-body text-center">
                    @if($item->profile_picture_url)
                        <img src="{{ $item->profile_picture_url }}" 
                             class="img-thumbnail rounded-circle mb-3"
                             style="width:200px;height:200px;object-fit: cover;" 
                             onerror="this.style.display='none';">
                    @else
                        <div class="d-flex align-items-center justify-content-center bg-light border rounded-circle mx-auto mb-3" 
                             style="width:200px;height:200px;">
                            <i class="mdi mdi-account text-muted" style="font-size: 80px;"></i>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
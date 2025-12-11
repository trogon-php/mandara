@extends('admin.layouts.app')

@section('content')
<!-- start page title -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">{{ $page_title }}</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item">
                        <a href="{{ url('admin/dashboard') }}" class="trogon-link">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ url('admin/client-credentials') }}" class="trogon-link">Client Credentials</a>
                    </li>
                    <li class="breadcrumb-item active">Details</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<!-- Card -->
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex align-items-center justify-content-between">
                    <h4 class="card-title mb-0">Client Credential Details</h4>
                    <div>
                        <a href="{{ url('admin/client-credentials/' . $credential->id . '/edit') }}" class="btn btn-primary btn-sm">
                            <i class="mdi mdi-pencil"></i> Edit
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Provider</label>
                            <div class="d-flex align-items-center">
                                <span class="badge bg-info p-2 me-2" style="font-size: 12px;">
                                    <i class="mdi mdi-{{ $credential->provider === 'vimeo' ? 'video' : ($credential->provider === 'zoom' ? 'video-call' : 'shield-key') }}"></i>
                                </span>
                                <span>{{ $credential->provider_display }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Status</label>
                            <div>
                                @if ($credential->isComplete())
                                    <span class="badge bg-success p-2" style="font-size: 12px;">
                                        <i class="mdi mdi-check"></i> Complete
                                    </span>
                                @else
                                    <span class="badge bg-warning p-2" style="font-size: 12px;">
                                        <i class="mdi mdi-alert"></i> Incomplete
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Title</label>
                            <p class="form-control-plaintext">{{ $credential->title }}</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Credential Key</label>
                            <p class="form-control-plaintext">
                                <code>{{ $credential->credential_key }}</code>
                            </p>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Account Key</label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="account_key" value="{{ $credential->decrypted_account_key }}" readonly>
                                <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('account_key')">
                                    <i class="mdi mdi-eye" id="account_key_icon"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Account Secret</label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="account_secret" value="{{ $credential->decrypted_account_secret }}" readonly>
                                <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('account_secret')">
                                    <i class="mdi mdi-eye" id="account_secret_icon"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                @if($credential->remarks)
                <div class="row">
                    <div class="col-12">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Remarks</label>
                            <p class="form-control-plaintext">{{ $credential->decrypted_remarks }}</p>
                        </div>
                    </div>
                </div>
                @endif

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Created</label>
                            <p class="form-control-plaintext">
                                {{ $credential->created_at->format('d-m-Y, g:i A') }}
                                @if($credential->creator)
                                    <br><small class="text-muted">by {{ $credential->creator->name }}</small>
                                @endif
                            </p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Last Updated</label>
                            <p class="form-control-plaintext">
                                {{ $credential->updated_at->format('d-m-Y, g:i A') }}
                                @if($credential->updater)
                                    <br><small class="text-muted">by {{ $credential->updater->name }}</small>
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function togglePassword(fieldId) {
    const field = document.getElementById(fieldId);
    const icon = document.getElementById(fieldId + '_icon');
    
    if (field.type === 'password') {
        field.type = 'text';
        icon.className = 'mdi mdi-eye-off';
    } else {
        field.type = 'password';
        icon.className = 'mdi mdi-eye';
    }
}
</script>
@endsection

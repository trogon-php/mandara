@if ($list_items)
    @foreach ($list_items as $list_item)
        <tr>
            <td><input type="checkbox" class="form-check-input row-checkbox" value="{{ $list_item->id }}"></td>
            <td>{{ $loop->iteration }}</td>
            <td class="p-4">
                <div class="d-flex align-items-start gap-3">
                    
                    <div class="flex-grow-1">
                        @if($list_item->user)
                            <h6 class="mt-2 mb-0" style="font-size: 14px!important;">
                                {{ $list_item->user->name }}
                            </h6>
                            <small class="text-muted">User ID: #{{ $list_item->user->id }}</small>
                        @else
                            <h6 class="mt-2 mb-0 text-muted" style="font-size: 14px!important;">
                                Guest User
                            </h6>
                            <small class="text-danger">No user account/ Deleted User</small>
                        @endif
                    </div>
                </div>
            </td>
            <td class="p-4">
                <div class="d-flex flex-column gap-2">
                    @if($list_item->email)
                        <div>
                            <i class="mdi mdi-email text-muted"></i>
                            <span class="ms-2">{{ $list_item->email }}</span>
                        </div>
                    @endif
                    @if($list_item->phone)
                        <div>
                            <i class="mdi mdi-phone text-muted"></i>
                            <span class="ms-2">{{ $list_item->country_code }} {{ $list_item->phone }}</span>
                        </div>
                    @endif
                    <div>
                        <i class="mdi mdi-ip-address text-muted"></i>
                        <span class="ms-2 text-muted">{{ $list_item->ip_address ?? 'N/A' }}</span>
                    </div>
                </div>
            </td>
            <td>
                @if($list_item->channel === 'phone')
                    <span class="badge border border-info text-info" style="font-size: 12px; padding: 4px 8px; border-radius: 4px;">
                        <i class="mdi mdi-phone me-1"></i>Phone
                    </span>
                @else
                    <span class="badge border border-primary text-primary" style="font-size: 12px; padding: 4px 8px; border-radius: 4px;">
                        <i class="mdi mdi-email me-1"></i>Email
                    </span>
                @endif
            </td>
            <td>
                <div class="d-flex flex-column gap-2">
                    @if($list_item->otp_code)
                        <div>
                            <span class="ms-1" style="font-size: 16px; color:rgb(27, 107, 145);">{{ $list_item->otp_code }}</span>
                        </div>
                    @endif
                </div>
            </td>
            <td>
                <div>
                    @if($list_item->status === 'verified')
                        <span class="badge border border-success text-success" style="font-size: 12px; padding: 4px 8px; border-radius: 4px;">
                            <i class="mdi mdi-check me-1"></i>Verified
                        </span>
                    @elseif($list_item->status === 'pending')
                        <span class="badge border border-warning text-warning" style="font-size: 12px; padding: 4px 8px; border-radius: 4px;">
                            <i class="mdi mdi-clock me-1"></i>Pending
                        </span>
                    @elseif($list_item->status === 'failed')
                        <span class="badge border border-danger text-danger" style="font-size: 12px; padding: 4px 8px; border-radius: 4px;">
                            <i class="mdi mdi-close me-1"></i>Failed
                        </span>
                    @else
                        <span class="badge border border-secondary text-secondary" style="font-size: 12px; padding: 4px 8px; border-radius: 4px;">
                            <i class="mdi mdi-clock-outline me-1"></i>Expired
                        </span>
                    @endif
                </div>
            </td>
            <td>
                <span class="text-muted"><small>{{ $list_item->created_at->format('d-m-Y, g:i A') }}</small></span>
            </td>
            <td class="text-center">
                @include('admin.crud.action-dropdown', [
                    'deleteUrl'   => route('admin.login-attempts.destroy', $list_item->id),
                    'redirectUrl' => route('admin.login-attempts.index')
                ])
            </td>
        </tr>
    @endforeach
@endif


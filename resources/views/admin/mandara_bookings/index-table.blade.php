@if($list_items)
    @foreach($list_items as $list_item)
        <tr>
            <td><input type="checkbox" class="form-check-input row-checkbox" value="{{ $list_item->id }}"></td>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $list_item->booking_number }}</td>
            <td>{{ $list_item->cottagePackage->title ?? '-' }}</td>
            <td>
                <strong>{{ $list_item->user->name ?? '-' }}</strong><br>
                <small class="text-muted">
                    {{ $list_item->user->phone ?? '-' }}<br>
                    {{ $list_item->user->email ?? '-' }}
                </small>
            </td>
            
            <td>
                {{ $list_item->is_delivered ? 'Delivered' : 'Expected' }}
                <br>
                <small class="text-muted">
                    {{ $list_item->delivery_date ? \Carbon\Carbon::parse($list_item->delivery_date)->format('d M Y') : '-' }}
                </small>
            </td>
            <td>
                <small>{{ \Carbon\Carbon::parse($list_item->date_from)->format('d M Y') }}</small>
            </td>
            <td>
                <small>{{ \Carbon\Carbon::parse($list_item->date_to)->format('d M Y') }}</small>
            </td>
            <td>
                @if($list_item->approval_status === 'pending')
                    <span class="badge bg-info bg-opacity-15 text-white px-2 py-1">
                        <i class="mdi mdi-check-circle me-1"></i>Pending
                    </span>
                @elseif($list_item->approval_status === 'approved')
                    <span class="badge bg-success bg-opacity-15 text-white px-2 py-1">
                        <i class="mdi mdi-close-circle me-1"></i>Approved
                    </span>
                @else
                    <span class="badge bg-danger bg-opacity-15 text-white px-2 py-1">
                        <i class="mdi mdi-close-circle me-1"></i>Rejected
                    </span>
                @endif
            </td>
            <td>
                <div class="d-flex flex-column">
                    <span class="small fw-medium">{{ $list_item->updated_at->format('d M, Y') }}</span>
                    <span class="text-muted small">
                        <i class="mdi mdi-clock-outline" style="font-size: 10px;"></i> 
                        {{ $list_item->updated_at->format('h:i A') }}
                    </span>
                </div>
            </td>
           
            <td>
                <div class="d-flex align-items-center gap-2">
            
                    {{-- Edit / Delete dropdown --}}
                    @include('admin.crud.action-dropdown', [
                        'editUrl'     => url('admin/mandara-bookings/'.$list_item->id.'/edit'),
                        'editTitle'   => 'Update Stay Booking',
                        'deleteUrl'   => route('admin.mandara-bookings.destroy', $list_item->id),
                        'redirectUrl' => route('admin.mandara-bookings.index')
                    ])
            
                   {{-- UNPAID BADGE --}}
                    @if($list_item->booking_payment_status !== 'paid')
                    <span class="badge bg-secondary">
                        Unpaid
                    </span>

                    {{-- PAID CASE --}}
                    @else

                    {{-- WEB BOOKING → AUTO APPROVED --}}
                    @if(!$list_item->is_app_booking)
                   
                        <span class="badge bg-success">
                            Approved
                        </span>

                    {{-- APP BOOKING --}}
                    @else

                        {{-- APPROVED / REJECTED BADGE --}}
                        @if(in_array($list_item->approval_status, ['approved', 'rejected']))
                            <span class="badge {{ $list_item->approval_status === 'approved' ? 'bg-success' : 'bg-danger' }}">
                                {{ ucfirst($list_item->approval_status) }}
                            </span>
                        @endif

                        {{-- APPROVE / REJECT BUTTONS (ONLY WHEN PENDING & APP BOOKING) --}}
                        @if($list_item->approval_status === 'pending')

                            {{-- Approve --}}
                            {{-- <form method="POST"
                                action="{{ route('admin.mandara-bookings.approve', $list_item->id) }}"
                                class="d-inline">
                                @csrf
                                <button type="submit"
                                        class="btn btn-success btn-sm rounded-circle"
                                        title="Approve"
                                        onclick="return confirm('Approve this booking?')">
                                    <i class="ri-check-line"></i>
                                </button>
                            </form> --}}
                            <a href="{{ route('admin.mandara-bookings.create', ['booking_id' => $list_item->id]) }}"
                                class="btn btn-success btn-sm rounded-circle"
                                title="Approve">
                                 <i class="ri-check-line"></i>
                             </a>
                             

                            {{-- Reject --}}
                            <form method="POST"
                                action="{{ route('admin.mandara-bookings.reject', $list_item->id) }}"
                                class="d-inline">
                                @csrf
                                <button type="submit"
                                        class="btn btn-danger btn-sm rounded-circle"
                                        title="Reject"
                                        onclick="return confirm('Reject this booking?')">
                                    <i class="ri-close-line"></i>
                                </button>
                            </form>

                        @endif

                    @endif

                    @endif

            
                </div>
            </td>  
        </tr>
    @endforeach
@endif


@if($list_items)
    @foreach($list_items as $list_item)
        <tr>
            <td><input type="checkbox" class="form-check-input row-checkbox" value="{{ $list_item->id }}"></td>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $list_item->booking_number }}</td>
            <td>
                <strong>{{ $list_item->user->name }}</strong><br>
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
                    <span class="badge bg-success bg-opacity-15 text-white px-2 py-1">
                        <i class="mdi mdi-check-circle me-1"></i>Pending
                    </span>
                @elseif($list_item->approval_status === 'approved')
                    <span class="badge bg-secondary bg-opacity-15 text-white px-2 py-1">
                        <i class="mdi mdi-close-circle me-1"></i>Approved
                    </span>
                @else
                    <span class="badge bg-secondary bg-opacity-15 text-white px-2 py-1">
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
                {{-- Standard Edit/Delete dropdown --}}
                @include('admin.crud.action-dropdown', [
                    'editUrl'     => url('admin/mandara-bookings/'.$list_item->id.'/edit'),
                    'editTitle'   => 'Update Stay Booking',
                    'deleteUrl'   => route('admin.mandara-bookings.destroy', $list_item->id),
                    'redirectUrl' => route('admin.mandara-bookings.index')
                ])
            
                {{-- Approval actions --}}
                @if(
                    $list_item->booking_payment_status === 'paid' &&
                    $list_item->approval_status === 'pending'
                )
                    <div class="mt-1 d-flex gap-1">
                        <form method="POST"
                              action="{{ route('admin.mandara-bookings.approve', $list_item->id) }}">
                            @csrf
                            <button class="btn btn-sm btn-success"
                                    onclick="return confirm('Approve this booking?')">
                                Approve
                            </button>
                        </form>
            
                        <form method="POST"
                              action="{{ route('admin.mandara-bookings.reject', $list_item->id) }}">
                            @csrf
                            <button class="btn btn-sm btn-danger"
                                    onclick="return confirm('Reject this booking?')">
                                Reject
                            </button>
                        </form>
                    </div>
            
                @endif
            </td>
            
        </tr>
    @endforeach
@endif


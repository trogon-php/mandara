@foreach($list_items as $referral)
    <tr>
        <td>{{ $referral->id }}</td>
        <td>
            <div class="d-flex flex-column">
                <h6 class="mb-1">{{ $referral->referrer->name ?? 'N/A' }}</h6>
                <small class="text-muted">{{ $referral->referrer->email ?? 'N/A' }}</small>
                @if($referral->referrer && $referral->referrer->phone)
                    <small class="text-muted">{{ $referral->referrer->phone }}</small>
                @endif
            </div>
        </td>
        <td>
            @if($referral->referred)
                <div class="d-flex flex-column">
                    <h6 class="mb-1">{{ $referral->referred->name }}</h6>
                    <small class="text-muted">{{ $referral->referred->email }}</small>
                </div>
            @else
                <span class="badge bg-secondary">Not Used Yet</span>
            @endif
        </td>
        <td>
            <code>{{ $referral->referral_code }}</code>
        </td>
        <td>
            <span class="badge bg-success">
                <i class="ri-coin-line"></i> {{ $referral->reward_coins }}
            </span>
        </td>
        <td>
            @php
                $statusColors = [
                    'pending' => 'warning',
                    'completed' => 'info',
                    'rewarded' => 'success',
                    'cancelled' => 'danger'
                ];
                $statusColor = $statusColors[$referral->status] ?? 'secondary';
            @endphp
            <span class="badge bg-{{ $statusColor }}">
                {{ ucfirst($referral->status) }}
            </span>
        </td>
        <td>
            <div class="d-flex flex-column">
                <small>{{ $referral->created_at->format('M d, Y') }}</small>
                <small class="text-muted">{{ $referral->created_at->format('h:i A') }}</small>
            </div>
        </td>
        <td>
            @include('admin.crud.action-dropdown', [
                'editUrl' => route('admin.reports.referrals.edit', $referral->id),
                'editTitle' => 'Edit Referral',
                'deleteUrl' => route('admin.reports.referrals.destroy', $referral->id),
                'redirectUrl' => url('admin/reports/referrals'),
            ])
        </td>
    </tr>
@endforeach




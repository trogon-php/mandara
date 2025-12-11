@foreach($list_items as $referrer)
    <tr>
        <td>{{ $loop->iteration + ($list_items->currentPage() - 1) * $list_items->perPage() }}</td>
        <td>
            <div class="d-flex align-items-center">
                <div class="flex-grow-1">
                    <h6 class="mb-0">{{ $referrer->name }}</h6>
                </div>
            </div>
        </td>
        <td>
            <div class="d-flex flex-column">
                <small class="text-muted">
                    <i class="ri-mail-line"></i> {{ $referrer->email }}
                </small>
                @if($referrer->phone)
                    <small class="text-muted">
                        <i class="ri-phone-line"></i> {{ $referrer->phone }}
                    </small>
                @endif
            </div>
        </td>
        <td>
            <span class="badge bg-primary">
                <i class="ri-user-line"></i> {{ $referrer->total_referrals }}
            </span>
        </td>
        <td>
            <span class="badge bg-success">
                <i class="ri-coin-line"></i> {{ $referrer->total_reward_points }}
            </span>
        </td>
        <td>
            <a href="{{ route('admin.reports.referrals.index', ['referrer_id' => $referrer->id]) }}" 
               class="btn btn-sm btn-outline-primary" 
               title="View Referred Users">
                <i class="ri-user-search-line"></i> View Referred Users
            </a>
        </td>
    </tr>
@endforeach




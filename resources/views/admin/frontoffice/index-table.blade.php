@if ($list_items)
    @foreach ($list_items as $list_item)
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>
                <div class="d-flex align-items-start gap-3">
                    <div class="flex-shrink-0">
                        @if($list_item->profile_picture_url)
                            <img src="{{ $list_item->profile_picture_url }}" 
                                 class="img-thumbnail rounded-circle"
                                 style="width:50px;height:50px;object-fit: cover;" 
                                 onerror="this.style.display='none';">
                        @else
                            <div class="d-flex align-items-center justify-content-center bg-light border rounded-circle" 
                                 style="width:50px;height:50px;">
                                <i class="mdi mdi-account text-muted" style="font-size: 24px;"></i>
                            </div>
                        @endif
                    </div>
                    <div class="flex-grow-1">
                        <h6 class="mb-1 mt-1" style="font-size: 15px!important;">{{ $list_item->name }}</h6>
                        @if($list_item->email)
                            <small class="text-muted d-block">
                                <i class="mdi mdi-email"></i> {{ $list_item->email }}
                            </small>
                        @endif
                    </div>
                </div>
            </td>
            <td>
                @if($list_item->phone)
                    <div class="mb-1">
                        <i class="mdi mdi-phone"></i> 
                        @if($list_item->country_code)
                            +{{ $list_item->country_code }} 
                        @endif
                        {{ $list_item->phone }}
                    </div>
                @endif
                @if($list_item->email)
                    <div>
                        <i class="mdi mdi-email"></i> 
                        <small>{{ $list_item->email }}</small>
                    </div>
                @endif
            </td>
            <td>
                @if ($list_item->status == 'active')
                    <span class="badge bg-success p-2" style="font-size: 11px!important;min-width: 100px!important;border-radius: 50px!important;">
                        <i class="mdi mdi-check"></i> Active
                    </span>
                @elseif ($list_item->status == 'pending')
                    <span class="badge bg-warning p-2" style="font-size: 11px!important;min-width: 100px!important;border-radius: 50px!important;">
                        <i class="mdi mdi-clock"></i> Pending
                    </span>
                @else
                    <span class="badge bg-danger p-2" style="font-size: 11px!important;min-width: 100px!important;border-radius: 50px!important;">
                        <i class="mdi mdi-close"></i> Blocked
                    </span>
                @endif
            </td>
            <td>
                <span class="text-muted"><small>{{ $list_item->created_at->format('d-m-Y') }}</small></span>
                <br>
                <span class="text-muted"><small>{{ $list_item->created_at->format('g:i A') }}</small></span>
            </td>
            <td>
                @include('admin.crud.action-dropdown', [
                    'editUrl'     => url('admin/front-office/'.$list_item->id.'/edit'),
                    'editTitle'   => 'Update Front Office',
                    'showUrl'     => url('admin/front-office/'.$list_item->id),
                    'showTitle'   => 'View Front Office',
                    'deleteUrl'   => route('admin.front-office.destroy', $list_item->id),
                    'redirectUrl' => route('admin.front-office.index')
                ])
            </td>
        </tr>
    @endforeach
@endif
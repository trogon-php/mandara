@if ($list_items)
    @foreach ($list_items as $list_item)
        <tr>
            <td><input type="checkbox" class="form-check-input row-checkbox" value="{{ $list_item->id }}"></td>
            <td>
                <span class="badge border border-success text-success p-2" style="font-size: 11px!important;min-width: 80px!important;border-radius: 50px!important;">
                    <i class="mdi mdi-{{ $list_item->provider === 'vimeo' ? 'video' : ($list_item->provider === 'zoom' ? 'video-call' : 'shield-key') }}"></i> 
                    {{ $list_item->provider_display }}
                </span>
            </td>
            <td>
                <div class="">
                    <h6 class="mb-1" style="font-size: 15px!important;">
                        {{ $list_item->title }}
                        @if(in_array($list_item->credential_key, ['vimeo_primary', 'zoom_primary', '2factor_primary']))
                            <span class="badge border border-warning text-warning ms-2" style="font-size: 10px;">PRIMARY</span>
                        @endif
                    </h6>
                    <code class="text-info">{{ $list_item->credential_key }}</code>
                </div>
            </td>
            <td>
                <div class="d-flex flex-column gap-1">
                    <div>
                        <small class="text-muted">Key:</small>
                        <code style="font-size: 12px;">{{ $list_item->masked_account_key }}</code>
                    </div>
                    <div>
                        <small class="text-muted">Secret:</small>
                        <code style="font-size: 12px;">{{ $list_item->masked_account_secret }}</code>
                    </div>
                </div>
            </td>
            <td>
                @if($list_item->remarks)
                    <small class="text-muted">{{ $list_item->decrypted_remarks ?? '' }}</small>
                @else
                    <span class="text-muted">-</span>
                @endif
            </td>
            <td>
                <span class="text-muted"><small>Created by:</small></span>
                <br>{{ $list_item->creator->name ?? 'System' }} <br>
                <span class="text-muted mt-1"><small>{{ $list_item->created_at->format('d-m-Y, g:i A') }}</small></span>
            </td>
            <td>
                @php
                    $isPrimary = in_array($list_item->credential_key, ['vimeo_primary', 'zoom_primary', '2factor_primary']);
                @endphp
                @include('admin.crud.action-dropdown', [
                    'editUrl'     => url('admin/client-credentials/'.$list_item->id.'/edit'),
                    'editTitle'   => 'Edit Credential',
                    'showUrl'     => url('admin/client-credentials/'.$list_item->id),
                    'showTitle'   => 'View Details',
                    'deleteUrl'   => $isPrimary ? null : route('admin.client-credentials.destroy', $list_item->id),
                    'deleteTitle' => $isPrimary ? 'Cannot delete primary credential' : 'Delete Credential',
                    'redirectUrl' => route('admin.client-credentials.index'),
                    'customActions' => []
                ])
            </td>
        </tr>
    @endforeach
@endif


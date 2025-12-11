@if ($list_items)
    @foreach ($list_items as $list_item)
        <tr>    
            <td>
                @if(in_array($list_item->id, [1, 2, 3]))
                    <input type="checkbox" class="form-check-input row-checkbox" value="{{ $list_item->id }}" disabled title="System roles cannot be selected for bulk operations">
                @else
                    <input type="checkbox" class="form-check-input row-checkbox" value="{{ $list_item->id }}">
                @endif
            </td>     
            <td>
                <div class="text-muted"><small>Role ID:</small></div>
                <span style="font-size: 16px;font-weight: 600;" class="text-primary">{{ $list_item->id }}</span>
            </td>
            <td>
                <div style="font-size: 16px;font-weight: 600;" class="text-primary">{{ $list_item->title }}</div>
                <div class="text-muted mt-1"><small>{{ $list_item->description }}</small></div>
                @if(in_array($list_item->id, [1, 2, 3]))
                    <div class="mt-1">
                        <span class="badge border border-warning text-warning"><i class="mdi mdi-shield-check"></i> System Role</span>
                    </div>
                @endif
            </td>
             <td>
                @if ($list_item->status)
                    <span class="badge bg-success"><i class="mdi mdi-check"></i> Active</span>
                @else
                    <span class="badge bg-danger"><i class="mdi mdi-close"></i> Inactive</span>
                @endif
            </td>
            <td><small>{{ $list_item->updated_at->diffForHumans() }}</small></td>
            <td>
                @if(in_array($list_item->id, [1, 2, 3]))
                    {{-- System roles - limited actions --}}
                    @include('admin.crud.action-dropdown', [
                        'cloneUrl'    => url('admin/roles/'.$list_item->id.'/clone'),
                        'cloneTitle'  => 'Clone Role',
                        'customActions' => [
                            [
                                'url' => 'javascript:void(0)',
                                'title' => 'System Role - Cannot Edit/Delete',
                                'icon' => 'mdi mdi-shield-check',
                                'class' => 'text-muted disabled'
                            ]
                        ]
                    ])
                @else
                    {{-- Regular roles - full actions --}}
                    @include('admin.crud.action-dropdown', [
                        'cloneUrl'    => url('admin/roles/'.$list_item->id.'/clone'),
                        'cloneTitle'  => 'Clone Role',
                        'editUrl'     => url('admin/roles/'.$list_item->id.'/edit'),
                        'editTitle'   => 'Update Role',
                        'deleteUrl'   => route('admin.roles.destroy', $list_item->id),
                        'redirectUrl' => route('admin.roles.index')
                    ])
                @endif
            </td>
        </tr>
    @endforeach
@endif

@if($list_items)
    @foreach($list_items as $list_item)
        <tr>
            <td><input type="checkbox" class="form-check-input row-checkbox" value="{{ $list_item->id }}"></td>
            <td>{{ $loop->iteration }}</td>
            <td>
                <div class="fw-bold">{{ $list_item->title }}</div>
            </td>
            <td>
                <div class="text-wrap">
                    {{ Str::limit($list_item->description, 50) }}
                </div>
            </td>
            {{-- <td>
                @forelse($list_item->items as $item)
                    <span class="badge bg-secondary me-1">
                        {{ $item->title }}
                    </span>
                @empty
                    <span class="text-muted">No items</span>
                @endforelse
            </td> --}}



            <td class="p-3">
                <div class="enrolled-courses-container">
            
                    <!-- Items List -->
                    <div class="courses-list mb-2" style="max-height: 100px; overflow-y: auto;">
                        @forelse($list_item->items->take(2) as $item)
                            <div class="course-item d-flex align-items-center mb-1 p-1 rounded"
                                 style="background-color: #f8f9fa; border-left: 3px solid rgb(112, 170, 163);">
                                <span class="text-truncate fw-medium"
                                      style="max-width: 180px; font-size: 12px!important;"
                                      title="{{ $item->title }}">
                                    {{ $item->title }}
                                </span>
                            </div>
                        @empty
                            <div class="text-muted text-center small">
                                No items
                            </div>
                        @endforelse
                    </div>
            
                    <!-- +X more indicator -->
                    @if($list_item->items->count() > 2)
                        <div class="text-muted text-center small">
                            +{{ $list_item->items->count() - 2 }} more
                        </div>
                    @endif
            
                    <!-- Manage Items (Edit shortcut) -->
                    <div class="text-center mt-1">
                       
                        <a href="javascript:void(0)"
                             onclick="showAjaxModal('{{ url('admin/amenities/'.$list_item->id.'/edit') }}', 'Edit Amenity & Manage Items')"
                           class="btn btn-outline-primary d-inline-flex align-items-center gap-1"
                           style="font-size: 10px; padding: 2px 10px;"
                           title="Edit amenity & manage items">
                            <i class="mdi mdi-pencil-outline"></i>
                            {{ $list_item->items->count() ? 'Manage Items' : 'Add Items' }}
                        </a>
                    </div>
            
                </div>
            </td>
            
            
            <td>
                @if($list_item->status == 'active')
                    <span class="badge bg-success"> Active</span>
                @else
                    <span class="badge bg-danger"> Inactive</span>
                @endif
            </td>
            <td>
                @include('admin.crud.action-dropdown', [
                    'editUrl'=>url('admin/amenities/'.$list_item->id.'/edit'),
                    'editTitle' => 'Update Amenity',
                    'deleteUrl'=>route('admin.amenities.destroy', $list_item->id),
                    'deleteTitle' => 'Delete Amenity',
                    'redirectUrl'=>route('admin.amenities.index')
                ])
            </td>
        </tr>
    @endforeach
@endif



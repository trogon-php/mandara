@if($list_items)
    @foreach($list_items as $list_item)
        <tr>
            <td><input type="checkbox" class="form-check-input row-checkbox" value="{{ $list_item->id }}"></td>
            <td>{{ $loop->iteration }}</td>
            <td>
                <div class="fw-bold">{{ $list_item->question }}</div>
                @if(!empty($list_item->options))
                <small class="text-muted">
                    {{ Str::limit($list_item->options_text, 50) }}
                </small>
            @endif
            </td>
            <td class="p-3">
                <div class="enrolled-courses-container">
            
                    <!-- Options List -->
                    <div class="courses-list mb-2" style="max-height: 100px; overflow-y: auto;">
                        @php
                            // Normalize options (JSON / array safe)
                            $options = $list_item->options;
            
                            if (is_string($options)) {
                                $options = json_decode($options, true) ?? [];
                            }
                        @endphp
            
                        @forelse(collect($options)->take(2) as $option)
                            <div class="course-item d-flex align-items-center mb-1 p-1 rounded"
                                 style="background-color: #f8f9fa; border-left: 3px solid rgb(112, 170, 163);">
                                <span class="text-truncate fw-medium"
                                      style="max-width: 180px; font-size: 12px!important;"
                                      title="{{ $option['option_text'] ?? '' }}">
                                    {{ $option['option_text'] ?? '' }}
                                </span>
                            </div>
                        @empty
                            <div class="text-muted text-center small">
                                No options
                            </div>
                        @endforelse
                    </div>
            
                    <!-- +X more indicator -->
                    @if(collect($options)->count() > 2)
                        <div class="text-muted text-center small">
                            +{{ collect($options)->count() - 2 }} more
                        </div>
                    @endif
            
                </div>
            </td>
            
            <td>
                @if($list_item->require_remark)
                    <span class="badge bg-success"><i class="mdi mdi-check"></i> Yes</span>
                @else
                    <span class="badge bg-danger"><i class="mdi mdi-close"></i> No</span>
                @endif
            </td>
            <td>
                @include('admin.crud.action-dropdown', [
                    'editUrl'=>url('admin/mandara-booking-questions/'.$list_item->id.'/edit'),
                    'editTitle' => 'Update Mandara Booking Question',
                    'deleteUrl'=>route('admin.mandara-booking-questions.destroy', $list_item->id),
                    'deleteTitle' => 'Delete Mandara Booking Question',
                    'redirectUrl'=>route('admin.mandara-booking-questions.index')
                ])
            </td>
        </tr>
    @endforeach
@endif



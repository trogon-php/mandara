@if($list_items)
    @foreach($list_items as $list_item)
        <tr class="assignment-row">
            <td>
                <input type="checkbox" class="form-check-input row-checkbox" value="{{ $list_item->id }}">
            </td>
            <td>
                <div class="assignment-details">
                    <div class="assignment-title">
                        <strong>{{ $list_item->title ?? $list_item->name ?? 'N/A' }}</strong>
                    </div>
                    @if(isset($list_item->description) && $list_item->description)
                        <div class="assignment-description text-muted small mt-1">
                            {{ Str::limit(strip_tags($list_item->description), 80) }}
                        </div>
                    @endif
                    <div class="assignment-meta mt-2 d-flex flex-wrap gap-2">
                        @if(isset($list_item->course) && $list_item->course)
                            <span class="badge bg-light text-dark">
                                <i class="fas fa-book me-1"></i>{{ $list_item->course->title ?? 'N/A' }}
                            </span>
                        @endif
                        @php
                            $fileCount = 0;
                            if (isset($list_item->files) && is_array($list_item->files)) {
                                $fileCount = count($list_item->files);
                            } elseif (isset($list_item->files) && !empty($list_item->files)) {
                                $fileCount = 1;
                            }
                        @endphp
                        <span class="badge bg-primary-subtle text-primary-emphasis">
                            <i class="fas fa-paperclip me-1"></i>{{ $fileCount }} {{ $fileCount == 1 ? 'File' : 'Files' }}
                        </span>
                    </div>
                </div>
            </td>
            <td>
                <div class="max-marks">
                    <span class="badge bg-info-subtle text-info-emphasis px-3 py-2">
                        <i class="fas fa-star me-1"></i>{{ number_format($list_item->max_marks ?? 0, 2) }}
                    </span>
                </div>
            </td>
            <td>
                <div class="due-date">
                    @if($list_item->due_date)
                        <div class="d-flex align-items-center">
                            <i class="fas fa-calendar-alt text-warning me-2"></i>
                            <div>
                                <div class="fw-medium">{{ \Carbon\Carbon::parse($list_item->due_date)->format('M d, Y') }}</div>
                                <div class="text-muted small">{{ \Carbon\Carbon::parse($list_item->due_date)->format('h:i A') }}</div>
                            </div>
                        </div>
                        @php
                            $isOverdue = \Carbon\Carbon::parse($list_item->due_date)->isPast();
                        @endphp
                        @if($isOverdue)
                            <span class="badge bg-danger-subtle text-danger-emphasis mt-1">
                                <i class="fas fa-exclamation-triangle me-1"></i>Overdue
                            </span>
                        @elseif(\Carbon\Carbon::parse($list_item->due_date)->diffInDays(now()) <= 3)
                            <span class="badge bg-warning-subtle text-warning-emphasis mt-1">
                                <i class="fas fa-clock me-1"></i>Due Soon
                            </span>
                        @endif
                    @else
                        <span class="text-muted">Not set</span>
                    @endif
                </div>
            </td>
            <td>
                @php
                    $statusValue = $list_item->status ?? 0;
                    $statusText = is_numeric($statusValue) 
                        ? ($statusValue ? 'Published' : 'Draft') 
                        : ucfirst($statusValue);
                    $statusClass = is_numeric($statusValue)
                        ? ($statusValue ? 'success' : 'secondary')
                        : (in_array(strtolower($statusValue), ['published', 'active']) ? 'success' : (strtolower($statusValue) === 'closed' ? 'danger' : 'secondary'));
                @endphp
                <span class="badge bg-{{ $statusClass }}-subtle text-{{ $statusClass }}-emphasis px-3 py-2">
                    <i class="fas fa-circle me-1" style="font-size: 0.5rem;"></i>{{ $statusText }}
                </span>
            </td>
            <td>
                @include('admin.crud.action-dropdown', [
                    'cloneUrl'    => url('admin/assignments/'.$list_item->id.'/clone'),
                    'cloneTitle'  => 'Clone Assignment',
                    'editUrl'=>url('admin/assignments/'.$list_item->id.'/edit'),
                    'editTitle'   => 'Update Assignment',
                    'deleteUrl'=>route('admin.assignments.destroy', $list_item->id),
                    'redirectUrl'=>route('admin.assignments.index')
                ])
            </td>
        </tr>
    @endforeach
@else
    <tr>
        <td colspan="6" class="text-center py-5">
            <div class="text-muted">
                <i class="fas fa-inbox fa-2x mb-3"></i>
                <p class="mb-0">No assignments found</p>
            </div>
        </td>
    </tr>
@endif

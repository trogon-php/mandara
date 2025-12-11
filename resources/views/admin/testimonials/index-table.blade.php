@if ($list_items)
    @foreach ($list_items as $list_item)
        <tr>
            <td><input type="checkbox" class="form-check-input row-checkbox" value="{{ $list_item->id }}"></td>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $list_item->content }}</td>
            <td>
                @if ($list_item->status)
                    <span class="badge bg-success"><i class="mdi mdi-check"></i> Active</span>
                @else
                    <span class="badge bg-danger"><i class="mdi mdi-close"></i> Inactive</span>
                @endif
            </td>
            <td>
                <div class="d-flex align-items-center">
                    <img src="{{ $list_item->profile_image_url }}" class="img-thumbnail rounded-circle me-2"
                         style="width:50px;height:50px;" onerror="this.style.display='none';">
                    <div>
                        <h6 class="mb-0">{{ $list_item->user_name }}</h6>
                        <small class="text-muted">{{ $list_item->designation }}</small>
                    </div>
                </div>
            </td>
            <td>
                @if($list_item->rating)
                    @for($i=1; $i<=5; $i++)
                        <i class="{{ $i <= $list_item->rating ? 'fas fa-star text-warning' : 'far fa-star text-muted' }}"></i>
                    @endfor
                    <span class="ms-1 text-muted">({{ $list_item->rating }})</span>
                @else
                    <span class="text-muted">No Rating</span>
                @endif
            </td>
            <td><small>{{ $list_item->updated_at->format('d-m-Y, g:i A') }}</small></td>
            <td>
                @include('admin.crud.action-dropdown', [
                    'cloneUrl'    => url('admin/testimonials/'.$list_item->id.'/clone'),
                    'cloneTitle'  => 'Clone Testimonial',
                    'editUrl'     => url('admin/testimonials/'.$list_item->id.'/edit'),
                    'editTitle'   => 'Update Testimonial',
                    'deleteUrl'   => route('admin.testimonials.destroy', $list_item->id),
                    'redirectUrl' => route('admin.testimonials.index')
                ])
            </td>
        </tr>
    @endforeach
@endif
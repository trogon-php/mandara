@if($list_items)
    @foreach($list_items as $list_item)
        <tr>
            <td><input type="checkbox" class="form-check-input row-checkbox" value="{{ $list_item->id }}"></td>
            <td>{{ $loop->iteration }}</td>
            <td>
                @if($list_item->images && count($list_item->images) > 0)
                    @php
                        $images = is_array($list_item->images_url) ? $list_item->images_url : json_decode($list_item->images_url, true) ?? [];
                        $totalImages = count($images);
                        $maxVisible = 3; // Show max 3 layers
                        $visibleImages = array_slice($images, 0, $maxVisible);
                    @endphp
                    <div class="position-relative" style="width: 70px; height: 70px; margin-left: {{ ($totalImages - 1) * 4 }}px;">
                        @foreach($visibleImages as $index => $imagePath)
                            @if($imagePath)
                                @php
                                    $zIndex = $maxVisible - $index; // First image on top
                                    $offset = $index * 4; // 4px offset per layer
                                    // images_url already contains full URLs, use directly
                                    $imageUrl = is_string($imagePath) ? $imagePath : ($imagePath['thumb'] ?? $imagePath['original'] ?? $imagePath);
                                @endphp
                                <div class="position-absolute" 
                                     style="
                                         left: {{ $offset }}px;
                                         top: {{ $offset }}px;
                                         width: 70px;
                                         height: 70px;
                                         z-index: {{ $zIndex }};
                                         cursor: pointer;
                                         transition: transform 0.2s;
                                     "
                                     onmouseover="this.style.transform='scale(1.1)'; this.style.zIndex='10';"
                                     onmouseout="this.style.transform='scale(1)'; this.style.zIndex='{{ $zIndex }}';"
                                     onclick="viewCottageImages({{ $list_item->id }}, {{ $index }})">
                                    <img src="{{ $imageUrl }}" 
                                         alt="{{ $list_item->title }} - Image {{ $index + 1 }}" 
                                         class="rounded border" 
                                         style="
                                             width: 100%; 
                                             height: 100%; 
                                             object-fit: cover;
                                             box-shadow: 0 2px 4px rgba(0,0,0,0.2);
                                             border: 2px solid #fff !important;
                                             display: block;
                                         "
                                         loading="lazy"
                                         onerror="console.error('Image failed to load:', this.src); this.onerror=null; this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                    <div class="d-flex align-items-center justify-content-center bg-light border rounded" 
                                         style="
                                             width: 100%; 
                                             height: 100%; 
                                             position: absolute; 
                                             top: 0; 
                                             left: 0; 
                                             display: none;
                                             box-shadow: 0 2px 4px rgba(0,0,0,0.2);
                                         ">
                                        <i class="mdi mdi-image-off text-muted" style="font-size: 24px;"></i>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                        @if($totalImages > $maxVisible)
                            <div class="position-absolute" 
                                 style="
                                     left: {{ ($maxVisible) * 4 }}px;
                                     top: {{ ($maxVisible) * 4 }}px;
                                     width: 70px;
                                     height: 70px;
                                     z-index: 0;
                                     background: rgba(0,0,0,0.1);
                                     border: 2px dashed #ccc;
                                     border-radius: 4px;
                                     display: flex;
                                     align-items: center;
                                     justify-content: center;
                                 ">
                                <span class="badge bg-dark" style="font-size: 10px;">
                                    +{{ $totalImages - $maxVisible }}
                                </span>
                            </div>
                        @endif
                    </div>
                @else
                    <span class="text-muted">No Image</span>
                @endif
            </td>
            <td>
                <div class="fw-bold">{{ $list_item->title }}</div>
                @if($list_item->short_description)
                    <small class="text-muted">{{ Str::limit($list_item->short_description, 50) }}</small>
                @endif
            </td>
            <td>
                @if($list_item->category)
                    <span class="badge bg-info">{{ $list_item->category->title }}</span>
                @else
                    <span class="text-muted">-</span>
                @endif
            </td>
            <td>
                <div class="d-flex gap-2">
                    @if($list_item->capacity)
                        <span class="badge bg-secondary" title="Capacity">
                            <i class="mdi mdi-account-group"></i> {{ $list_item->capacity }}
                        </span>
                    @endif
                    @if($list_item->bedrooms)
                        <span class="badge bg-secondary" title="Bedrooms">
                            <i class="mdi mdi-bed"></i> {{ $list_item->bedrooms }}
                        </span>
                    @endif
                    @if($list_item->bathrooms)
                        <span class="badge bg-secondary" title="Bathrooms">
                            <i class="mdi mdi-shower"></i> {{ $list_item->bathrooms }}
                        </span>
                    @endif
                </div>
            </td>
            <td>
                @if ($list_item->status)
                    <span class="badge bg-success"><i class="mdi mdi-check"></i> Active</span>
                @else
                    <span class="badge bg-danger"><i class="mdi mdi-close"></i> Inactive</span>
                @endif
            </td>
            <td><small>{{ $list_item->updated_at->format('d-m-Y, g:i A') }}</small></td>
            <td>
                @include('admin.crud.action-dropdown', [
                    'cloneUrl'    => url('admin/cottages/'.$list_item->id.'/clone'),
                    'cloneTitle'  => 'Clone Cottage',
                    'editUrl'=>url('admin/cottages/'.$list_item->id.'/edit'),
                    'editTitle' => 'Update Cottage',
                    'deleteUrl'=>route('admin.cottages.destroy', $list_item->id),
                    'deleteTitle' => 'Delete Cottage',
                    'redirectUrl'=>route('admin.cottages.index')
                ])
            </td>
        </tr>
    @endforeach
@endif

@push('scripts')
<script>
function viewCottageImages(cottageId, startIndex) {
    // You can implement a lightbox/modal here to view all images
    console.log('View cottage images:', cottageId, 'Starting from index:', startIndex);
}
</script>
@endpush

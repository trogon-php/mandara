@foreach($list_items as $package)
    <tr>
        <td>
            <input type="checkbox" class="form-check-input row-checkbox" value="{{ $package->id }}">
        </td>
        <td>{{ $package->id }}</td>
        <td>
            <div class="d-flex align-items-center">
                <div class="flex-grow-1">
                    <h6 class="mb-1">{{ $package->title }}</h6>
                    @if($package->description)
                        <p class="text-muted mb-0 small">{{ Str::limit($package->description, 50) }}</p>
                    @endif
                    @if($package->hasOffer())
                        <span class="badge bg-success">Special Offer</span>
                    @endif
                </div>
            </div>
        </td>
        <td>
            <div class="d-flex flex-column">
                @if($package->hasOffer())
                    <span class="text-decoration-line-through text-muted">₹{{ number_format($package->price, 2) }}</span>
                    <span class="text-success fw-bold">₹{{ number_format($package->offer_price, 2) }}</span>
                    <span class="text-success small">{{ $package->discount_percentage }}% off</span>
                @else
                    <span class="fw-bold">₹{{ number_format($package->price, 2) }}</span>
                @endif
            </div>
        </td>
        <td>
            @if($package->duration_days)
                <span class="badge bg-info">{{ $package->duration_days }} days</span>
            @else
                <span class="text-muted">-</span>
            @endif
        </td>
        <td>
            <div class="d-flex flex-column">
                <div class="mb-2">
                    <button class="btn btn-sm btn-outline-primary" 
                            onclick="showAjaxModal('{{ route('admin.package-items.create', ['package_id' => $package->id]) }}', 'Add Package Items')">
                        <i class="ri-add-line"></i> Add Items
                    </button>
                </div>
                <div id="package-items-{{ $package->id }}" class="package-items-list">
                    @if($package->items && $package->items->count() > 0)
                        @php
                            $categories = $package->items->where('item_type', 'category')->count();
                            $courses = $package->items->where('item_type', 'course')->count();
                            $units = $package->items->where('item_type', 'course_unit')->count();
                            $materials = $package->items->where('item_type', 'course_material')->count();
                        @endphp
                        
                        <div class="mb-2">
                            <div class="d-flex flex-wrap gap-1 mb-2">
                                @if($categories > 0)
                                    <span class="badge bg-primary">{{ $categories }} Categories</span>
                                @endif
                                @if($courses > 0)
                                    <span class="badge bg-info">{{ $courses }} Courses</span>
                                @endif
                                @if($units > 0)
                                    <span class="badge bg-warning">{{ $units }} Units</span>
                                @endif
                                @if($materials > 0)
                                    <span class="badge bg-secondary">{{ $materials }} Materials</span>
                                @endif
                            </div>
                            
                            @if($package->items->count() <= 3)
                                {{-- Show individual items if few --}}
                                @foreach($package->items->take(3) as $item)
                                    <div class="d-flex justify-content-between align-items-center mb-1 p-2 bg-light rounded">
                                        <div class="flex-grow-1">
                                            <small class="fw-semibold">{{ $item->item_title }}</small>
                                            <br><small class="text-muted">{{ ucfirst(str_replace('_', ' ', $item->item_type)) }}</small>
                                            <span class="badge bg-{{ $item->status === 'active' ? 'success' : 'secondary' }} ms-1">
                                                {{ ucfirst($item->status) }}
                                            </span>
                                        </div>
                                        <div class="d-flex gap-1">
                                            <button class="btn btn-xs btn-outline-secondary" 
                                                    onclick="showAjaxModal('{{ route('admin.package-items.edit', [$package->id, $item->id]) }}', 'Edit Item')">
                                                <i class="ri-edit-line"></i>
                                            </button>
                                            <button class="btn btn-xs btn-outline-danger" 
                                                    onclick="confirmDelete('{{ route('admin.package-items.destroy', [$package->id, $item->id]) }}', '')">
                                                <i class="ri-delete-bin-line"></i>
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                {{-- Show summary if many items --}}
                                <div class="text-center">
                                    <small class="text-muted">
                                        {{ $package->items->count() }} items total
                                        <br>
                                        <button class="btn btn-xs btn-outline-primary mt-1" 
                                                onclick="showPackageDetails({{ $package->id }})">
                                            <i class="ri-eye-line"></i> View All
                                        </button>
                                    </small>
                                </div>
                            @endif
                        </div>
                    @else
                        <small class="text-muted">No items added</small>
                    @endif
                </div>
            </div>
        </td>
        <td>
            <span class="badge bg-{{ $package->status === 'active' ? 'success' : 'secondary' }}">
                {{ ucfirst($package->status) }}
            </span>
        </td>
        <td>
            <div class="d-flex flex-column">
                <span class="text-muted small">{{ $package->created_at->format('M d, Y') }}</span>
                <span class="text-muted small">{{ $package->created_at->format('h:i A') }}</span>
            </div>
        </td>
        <td>
            @include('admin.crud.action-dropdown', [
                'editUrl' => route('admin.packages.edit', $package->id),
                'editTitle' => 'Edit Package',
                'cloneUrl' => route('admin.packages.clone', $package->id),
                'cloneTitle' => 'Clone Package',
                'deleteUrl' => route('admin.packages.destroy', $package->id),
                'redirectUrl' => url('admin/packages')
            ])
        </td>
    </tr>
@endforeach

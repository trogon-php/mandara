{{-- Repeater field template --}}
<div class="form-group">
    {{-- <label for="{{ $id ?? $name }}">{{ $label }} {!! !empty($required) ? '<span class="text-danger">*</span>' : '' !!}</label> --}}
    
    <div id="{{ $id ?? $name }}-container" class="repeater-container">
        @if(isset($value) && is_array($value) && count($value) > 0)
            @foreach($value as $index => $item)
                <div class="repeater-item" data-index="{{ $index }}">
                    <div class="card mb-3">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h6 class="mb-0 text-primary fw-bold">
                                <i class="{{ $itemIcon ?? 'fas fa-grip-vertical' }} me-2"></i>
                                {{ $itemLabel ?? 'Item' }} {{ $index + 1 }}
                            </h6>
                            <div class="btn-group btn-group-sm">
                                <button type="button" class="btn btn-outline-secondary move-up-btn" title="Move Up">
                                    <i class="{{ $moveUpIcon ?? 'fas fa-arrow-up' }}"></i>
                                </button>
                                <button type="button" class="btn btn-outline-secondary move-down-btn" title="Move Down">
                                    <i class="{{ $moveDownIcon ?? 'fas fa-arrow-down' }}"></i>
                                </button>
                                <button type="button" class="btn btn-outline-danger remove-item-btn" title="Remove Item">
                                    <i class="{{ $removeIcon ?? 'fas fa-trash' }}"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                @foreach($fields as $field)
                                    <div class="col-lg-{{ $field['col'] ?? 12 }} col-sm-12 mb-3">
                                        @include('admin.crud.fields.' . $field['type'], array_merge($field, [
                                            'name' => $name . '[' . $index . '][' . $field['name'] . ']',
                                            'id' => $name . '_' . $index . '_' . $field['name'],
                                            'value' => $item[$field['name']] ?? old($name . '.' . $index . '.' . $field['name'], $field['value'] ?? ''),
                                        ]))
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        @else
            {{-- Default empty item --}}
            <div class="repeater-item" data-index="0">
                <div class="card mb-3">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 text-primary fw-bold">
                            <i class="{{ $itemIcon ?? 'fas fa-grip-vertical' }} me-2"></i>
                            {{ $itemLabel ?? 'Item' }} 1
                        </h6>
                        <div class="btn-group btn-group-sm">
                            <button type="button" class="btn btn-outline-secondary move-up-btn" title="Move Up" style="display: none;">
                                <i class="{{ $moveUpIcon ?? 'fas fa-arrow-up' }}"></i>
                            </button>
                            <button type="button" class="btn btn-outline-secondary move-down-btn" title="Move Down" style="display: none;">
                                <i class="{{ $moveDownIcon ?? 'fas fa-arrow-down' }}"></i>
                            </button>
                            <button type="button" class="btn btn-outline-danger remove-item-btn" title="Remove Item" style="display: none;">
                                <i class="{{ $removeIcon ?? 'fas fa-trash' }}"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @foreach($fields as $field)
                                <div class="col-lg-{{ $field['col'] ?? 12 }} col-sm-12 mb-3">
                                    @include('admin.crud.fields.' . $field['type'], array_merge($field, [
                                        'name' => $name . '[0][' . $field['name'] . ']',
                                        'id' => $name . '_0_' . $field['name'],
                                        'value' => old($name . '.0.' . $field['name'], $field['value'] ?? ''),
                                    ]))
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
    
    <div class="mt-3 text-center">
        <button type="button" class="btn btn-outline-primary" id="add-{{ $id ?? $name }}-btn">
            <i class="{{ $addIcon ?? 'fas fa-plus' }}"></i> Add Another {{ $itemLabel ?? 'Item' }}
        </button>
    </div>
    
    @error($name) <small class="text-danger">{{ $message }}</small> @enderror
</div>

<script>
$(document).ready(function() {
    const container = $('#{{ $id ?? $name }}-container');
    const addBtn = $('#add-{{ $id ?? $name }}-btn');
    let itemIndex = container.find('.repeater-item').length;
    
    // Icon classes from PHP
    const itemIcon = '{{ $itemIcon ?? "fas fa-grip-vertical" }}';
    const moveUpIcon = '{{ $moveUpIcon ?? "fas fa-arrow-up" }}';
    const moveDownIcon = '{{ $moveDownIcon ?? "fas fa-arrow-down" }}';
    const removeIcon = '{{ $removeIcon ?? "fas fa-trash" }}';
    const addIcon = '{{ $addIcon ?? "fas fa-plus" }}';
    
    // Add new item with slide animation
    addBtn.on('click', function() {
        addRepeaterItem();
    });
    
    // Remove item with slide animation
    container.on('click', '.remove-item-btn', function() {
        const item = $(this).closest('.repeater-item');
        item.slideUp(300, function() {
            $(this).remove();
            updateRepeaterItems();
        });
    });
    
    // Move up with slide animation
    container.on('click', '.move-up-btn', function() {
        const item = $(this).closest('.repeater-item');
        const prevItem = item.prev('.repeater-item');
        if (prevItem.length) {
            // Slide up current item
            item.slideUp(200, function() {
                // Insert before previous item
                item.insertBefore(prevItem);
                // Slide down the moved item
                item.slideDown(200, function() {
                    updateRepeaterItems();
                });
            });
        }
    });
    
    // Move down with slide animation
    container.on('click', '.move-down-btn', function() {
        const item = $(this).closest('.repeater-item');
        const nextItem = item.next('.repeater-item');
        if (nextItem.length) {
            // Slide up current item
            item.slideUp(200, function() {
                // Insert after next item
                item.insertAfter(nextItem);
                // Slide down the moved item
                item.slideDown(200, function() {
                    updateRepeaterItems();
                });
            });
        }
    });
    
    function addRepeaterItem() {
        // Clone the first item and update its attributes
        const firstItem = container.find('.repeater-item').first();
        const newItem = firstItem.clone();
        
        // Update the new item
        newItem.attr('data-index', itemIndex);
        newItem.find('h6').html('<i class="' + itemIcon + ' me-2"></i>{{ $itemLabel ?? "Item" }} ' + (itemIndex + 1));
        
        // Clear all input values
        newItem.find('input, select, textarea').val('');
        
        // Update field names and IDs
        newItem.find('input, select, textarea').each(function() {
            const field = $(this);
            const name = field.attr('name');
            const id = field.attr('id');
            
            if (name) {
                const newName = name.replace(/\[\d+\]/, '[' + itemIndex + ']');
                field.attr('name', newName);
            }
            
            if (id) {
                const newId = id.replace(/_\d+_/, '_' + itemIndex + '_');
                field.attr('id', newId);
            }
        });
        
        // Update labels
        newItem.find('label').each(function() {
            const label = $(this);
            const forAttr = label.attr('for');
            if (forAttr) {
                const newFor = forAttr.replace(/_\d+_/, '_' + itemIndex + '_');
                label.attr('for', newFor);
            }
        });
        
        // Hide the new item initially
        newItem.hide();
        container.append(newItem);
        
        // Slide down the new item
        newItem.slideDown(300, function() {
            itemIndex++;
            updateRepeaterItems();

            // Re-attach file preview functionality to new file inputs
            reattachFilePreviewHandlers(newItem);
        });
    }
    
    function updateRepeaterItems() {
        const items = container.find('.repeater-item');
        
        items.each(function(index) {
            const item = $(this);
            item.attr('data-index', index);
            
            // Update title with dynamic icon
            item.find('h6').html('<i class="' + itemIcon + ' me-2"></i>{{ $itemLabel ?? "Item" }} ' + (index + 1));
            
            // Update field names and IDs
            item.find('input, select, textarea').each(function() {
                const field = $(this);
                const name = field.attr('name');
                const id = field.attr('id');
                
                if (name) {
                    const newName = name.replace(/\[\d+\]/, '[' + index + ']');
                    field.attr('name', newName);
                }
                
                if (id) {
                    const newId = id.replace(/_\d+_/, '_' + index + '_');
                    field.attr('id', newId);
                }
            });
            
            // Update labels
            item.find('label').each(function() {
                const label = $(this);
                const forAttr = label.attr('for');
                if (forAttr) {
                    const newFor = forAttr.replace(/_\d+_/, '_' + index + '_');
                    label.attr('for', newFor);
                }
            });
        });
        
        // Update button visibility with fade animation
        items.each(function(index) {
            const item = $(this);
            const moveUpBtn = item.find('.move-up-btn');
            const moveDownBtn = item.find('.move-down-btn');
            const removeBtn = item.find('.remove-item-btn');
            
            // Show/hide move up button with fade
            if (index === 0) {
                moveUpBtn.fadeOut(150);
            } else {
                moveUpBtn.fadeIn(150);
            }
            
            // Show/hide move down button with fade
            if (index === items.length - 1) {
                moveDownBtn.fadeOut(150);
            } else {
                moveDownBtn.fadeIn(150);
            }
            
            // Show/hide remove button with fade
            if (items.length > 1) {
                removeBtn.fadeIn(150);
            } else {
                removeBtn.fadeOut(150);
            }
        });
    }
    
    // Initialize
    updateRepeaterItems();
    function reattachFilePreviewHandlers(container) {
        // Find all file inputs with paste-target-file class in the container
        const fileInputs = container.find('input.paste-target-file');
        
        fileInputs.each(function() {
            const fileInput = $(this)[0]; // Convert jQuery to DOM element
            
            // Remove existing listeners to prevent duplicates
            fileInput.removeEventListener("change", handleFileChange);
            
            // Add the change listener - this triggers renderPreview
            fileInput.addEventListener("change", handleFileChange);
            
            // Note: Paste functionality is already handled globally via event delegation
            // in footer_includes.blade.php (line 299), so no need to re-attach paste handlers
        });
    }
});
</script>
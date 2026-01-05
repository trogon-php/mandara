{{-- Repeater field template --}}
<div class="form-group">
    <div id="{{ $id ?? $name }}-container" class="repeater-container">
        {{-- handling if data already exists case (in edit or old input validation errors) --}}
        @if (isset($options_data) && count($options_data) > 0)
            @foreach ($options_data as $index => $option)
                <div class="form-group repeater-item" data-index="{{ $index }}">
                    <div class="input-group paste-file-group">
                        {{-- <div class="input-group-prepend"> --}}
                            {{-- <div class="input-group-text">
                                <div class="form-check">
                                    <input type="hidden" name="options[{{ $index }}][is_correct]" value="0">
                                    <input type="checkbox" 
                                        class="form-check-input option-checkbox"
                                        name="options[{{ $index }}][is_correct]"
                                        id="options_{{ $index }}_is_correct"
                                        {{ $option['is_correct'] == 'true' ? 'checked' : '' }} 
                                        value="1"
                                        style="width: 18px; height: 16px;">
                                </div>
                            </div> --}}
                            {{--
                        </div> --}}
                        <input type="text"
                            name="options[{{ $index }}][option_text]"
                            id="options_{{ $index }}_option_text"
                            value="{{ $option['option_text'] }}"
                            class="form-control"
                            placeholder="Option {{ $index + 1 }}"
                            required=""
                            style="width: 226px;">
                        <input type="file"
                            name="options[{{ $index }}][option_image]"
                            id="options_{{ $index }}_option_image"
                            class="form-control paste-target-file"
                            accept="image/*">
                        <input type="text"
                            class="form-control paste-source-field"
                            placeholder="Paste image here">
                        <div class="input-group-append">
                            <button type="button" class="btn btn-outline-danger remove-item-btn" title="Remove Item"
                                style="border-radius: 5px;">
                                <i class="{{ $removeIcon ?? 'fas fa-trash' }}"></i>
                            </button>
                        </div>
                    </div>

                    <div class="file-preview-container mt-2 d-flex flex-wrap gap-2"></div>

                    <input type="hidden" name="options[{{ $index }}][option_image_removed]_removed"
                        id="options_{{ $index }}_option_image_removed" value="">

                    @if(!empty($option['option_image']))
                        <div class="current-files-preview mb-3">
                            <label class="form-label text-muted">Current option image:</label>
                            <div class="current-files-container">
                                <div class="current-file-item d-inline-block me-2 mb-2" data-file-path="{{ $option['option_image'] }}">
                                    <div class="position-relative" style="border: 1px solid #dee2e6; padding: 8px; border-radius: 4px; background: #f8f9fa;">
                                        <img src="{{ $option['option_image_url'] }}" alt="Current option image" style="max-width: 120px; height: auto; border-radius: 4px; display: block;">
                                        {{-- Remove button --}}
                                        <button type="button" 
                                                class="btn btn-sm btn-danger remove-existing-file" 
                                                title="Remove file"
                                                style="position:absolute;top:-5px;right:-5px;border-radius:50%;padding:0 6px;line-height:1;z-index:10;">×</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            @endforeach
        @else
            @for ($i = 0; $i < 4; $i++)
                <div class="form-group repeater-item" data-index="{{ $i }}">
                    <div class="input-group paste-file-group">
                        {{-- <div class="input-group-prepend"> --}}
                            <div class="input-group-text">
                                <div class="form-check">
                                    <input type="hidden" name="options[{{ $i }}][is_correct]" value="0">
                                    <input type="checkbox" class="form-check-input option-checkbox"
                                        name="options[{{ $i }}][is_correct]" id="options_{{ $i }}_is_correct" value="1"
                                        style="width: 18px; height: 16px;">
                                </div>
                            </div>
                            {{--
                        </div> --}}
                        <input type="text" name="options[{{ $i }}][option_text]" id="options_{{ $i }}_option_text" value=""
                            class="form-control" placeholder="Option {{ $i + 1 }}" required="" style="width: 226px;">
                        <input type="file" name="options[{{ $i }}][option_image]" id="options_{{ $i }}_option_image"
                            class="form-control paste-target-file" accept="image/*">
                        <input type="text" class="form-control paste-source-field" placeholder="Paste image here">
                        <div class="input-group-append">
                            <button type="button" class="btn btn-outline-danger remove-item-btn" title="Remove Item"
                                style="border-radius: 5px;">
                                <i class="{{ $removeIcon ?? 'fas fa-trash' }}"></i>
                            </button>
                        </div>
                    </div>

                    <div class="file-preview-container mt-2 d-flex flex-wrap gap-2"></div>

                    <input type="hidden" name="options[{{ $i }}][option_image]_removed"
                        id="options_{{ $i }}_option_image_removed" value="">
                </div>
            @endfor
        @endif
    </div>

    <div class="mt-3 text-center">
        <button type="button" class="btn btn-outline-primary" id="add-{{ $id ?? $name }}-btn">
            <i class="{{ $addIcon ?? 'fas fa-plus' }}"></i> Add Another {{ $itemLabel ?? 'Item' }}
        </button>
    </div>

    @error($name)
        <small class="text-danger">{{ $message }}</small>
    @enderror
</div>

<script>
    $(document).ready(function () {
        const container = $('#{{ $id ?? $name }}-container');
        const addBtn = $('#add-{{ $id ?? $name }}-btn');
        let itemIndex = container.find('.repeater-item').length;

        // Icon classes from PHP
        const itemIcon = '{{ $itemIcon ?? 'fas fa-grip-vertical' }}';
        const removeIcon = '{{ $removeIcon ?? 'fas fa-trash' }}';
        const addIcon = '{{ $addIcon ?? 'fas fa-plus' }}';

        // Add new item with slide animation
        addBtn.on('click', function () {
            addRepeaterItem();
        });

        // Remove item with slide animation
        container.on('click', '.remove-item-btn', function () {
            const item = $(this).closest('.repeater-item');
            item.slideUp(300, function () {
                $(this).remove();
                updateRepeaterItems();
            });
        });

        function addRepeaterItem() {
            // Clone the first item and update its attributes
            const firstItem = container.find('.repeater-item').first();
            const newItem = firstItem.clone();

            // if style is exist, remove it
            if (newItem.attr('style')) {
                newItem.removeAttr('style');
            }
            // Update the new item
            newItem.attr('data-index', itemIndex);

            // Clear all input values
            newItem.find('input, select, textarea').val('');
            newItem.find('input[type="checkbox"]').prop('checked', false);
            newItem.find('input[type="checkbox"]').val('1');
            newItem.find('input[type="hidden"]').val('0');

            // Update field names and IDs
            newItem.find('input, select, textarea').each(function () {
                const field = $(this);
                const name = field.attr('name');
                const id = field.attr('id');

                if (name) {
                    const newName = name.replace(/\[\d+\]/, '[' + itemIndex + ']');
                    field.attr('name', newName);
                }

                if (id) {
                    const newId = id.replace(/\d+/, '' + itemIndex + '');
                    field.attr('id', newId);
                }
            });

            // Update labels
            newItem.find('label').each(function () {
                const label = $(this);
                const forAttr = label.attr('for');
                if (forAttr) {
                    const newFor = forAttr.replace(/\d+/, '' + itemIndex + '');
                    label.attr('for', newFor);
                }
            });

            // Update placeholder text
            newItem.find('input[type="text"][placeholder^="Option"]').attr('placeholder', 'Option ' + (itemIndex + 1));

            // Hide the new item initially
            newItem.hide();
            container.append(newItem);

            // file preview container set empty
            newItem.find('.file-preview-container').empty();

            // Slide down the new item
            newItem.slideDown(300, function () {
                itemIndex++;
                updateRepeaterItems();

                // Re-attach file preview functionality to new file inputs
                reattachFilePreviewHandlers(newItem);
            });

        }

        function updateRepeaterItems() {
            const items = container.find('.repeater-item');

            items.each(function (index) {
                const item = $(this);
                item.attr('data-index', index);

                // Update field names and IDs
                item.find('input, select, textarea').each(function () {
                    const field = $(this);
                    const name = field.attr('name');
                    const id = field.attr('id');

                    if (name) {
                        const newName = name.replace(/\[\d+\]/, '[' + index + ']');
                        field.attr('name', newName);
                    }

                    if (id) {
                        const newId = id.replace(/\d+/, '' + index + '');
                        field.attr('id', newId);
                    }
                });

                // Update labels
                item.find('label').each(function () {
                    const label = $(this);
                    const forAttr = label.attr('for');
                    if (forAttr) {
                        const newFor = forAttr.replace(/\d+/, '' + index + '');
                        label.attr('for', newFor);
                    }
                });

                // Update placeholder text
                item.find('input[type="text"][placeholder^="Option"]').attr('placeholder', 'Option ' + (index + 1));
            });

            // Update button visibility with fade animation
            items.each(function (index) {
                const item = $(this);
                const removeBtn = item.find('.remove-item-btn');

                // Show/hide remove button with fade
                if (items.length > 1) {
                    removeBtn.fadeIn(150);
                } else {
                    removeBtn.fadeOut(150);
                }
            });

        }
        function reattachFilePreviewHandlers(container) {
            // Find all file inputs with paste-target-file class in the container
            const fileInputs = container.find('input.paste-target-file');

            fileInputs.each(function () {
                const fileInput = $(this)[0]; // Convert jQuery to DOM element

                // Remove existing listeners to prevent duplicates
                fileInput.removeEventListener("change", handleFileChange);

                // Add the change listener - this triggers renderPreview
                fileInput.addEventListener("change", handleFileChange);

                // Note: Paste functionality is already handled globally via event delegation
                // in footer_includes.blade.php (line 299), so no need to re-attach paste handlers
            });
        }
        // Initial set up
        updateRepeaterItems();
        // re-initialize file preview handlers
        reattachFilePreviewHandlers(container);
    });
</script>
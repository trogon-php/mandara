<script src="{{ url('assets/app/js/pages/form-wizard.init.js') }}"></script>



<!-- JAVASCRIPT -->
<script src="{{ url('assets/app/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ url('assets/app/libs/simplebar/simplebar.min.js') }}"></script>
<script src="{{ url('assets/app/libs/node-waves/waves.min.js') }}"></script>
<script src="{{ url('assets/app/libs/feather-icons/feather.min.js') }}"></script>
<script src="{{ url('assets/app/js/pages/plugins/lord-icon-2.1.0.js') }}"></script>
<script src="{{ url('assets/app/js/plugins.js') }}"></script>






<!-- Sweet Alerts js -->
<script src="{{ url('assets/app/libs/sweetalert2/sweetalert2.min.js') }}"></script>
<script type='text/javascript' src='https://cdn.jsdelivr.net/npm/toastify-js'></script>
<script src="{{ url('assets/app/libs/choices.js/public/assets/scripts/choices.min.js') }}"></script>
<script src="{{ url('assets/app/libs/flatpickr/flatpickr.min.js') }}"></script>

<!-- apexcharts -->

<script src="{{ url('assets/app/libs/apexcharts/apexcharts.min.js') }}"></script>

<!-- Vector map-->
<script src="{{ url('assets/app/libs/jsvectormap/js/jsvectormap.min.js') }}"></script>
<script src="{{ url('assets/app/libs/jsvectormap/maps/world-merc.js') }}"></script>

<!--Swiper slider js-->
<script src="{{ url('assets/app/libs/swiper/swiper-bundle.min.js') }}"></script>

<!-- Dashboard init -->
<script src="{{ url('assets/app/js/pages/dashboard-ecommerce.init.js') }}"></script>

<!-- App js -->
<script src="{{ url('assets/app/js/app.js') }}"></script>
<script src="{{ url('assets/app/js/common.js') }}"></script>

<!-- glightbox js -->
<script src="{{ url('assets/libs/glightbox/js/glightbox.min.js') }}"></script>

<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.print.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.html5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>

<script src="{{ url('assets/app/js/pages/password-addon.init.js') }}"></script>


<script type="text/javascript">
    function checkCategoryType(category_type) {
        if (category_type > 0) {
            $('#thumbnail-picker-area').hide();
            $('#icon-picker-area').hide();
        }else {
            $('#thumbnail-picker-area').show();
            $('#icon-picker-area').show();
        }
    }
</script>
<script>
    // Function to collect all filters from container
    function getFilters(containerId) {
        let filters = {};
        $('#' + containerId + ' [name]').each(function() {
            let name = $(this).attr('name');
            let value = $(this).val();
            filters[name] = value;
        });
        return filters;
    }

    function initializeTables(container = document) {
        try {
            // Check if DataTable is already initialized
            $(container).find('.data_table_basic').each(function() {
                if (!$.fn.DataTable.isDataTable(this)) {
                    new DataTable(this, {
                        dom: "Bfrtip",
                        buttons: ["csv", "excel", "print", "pdf"],
                        "pagingType": "full_numbers",
                        "scrollCollapse": true,
                        "paging": true,
                        "responsive": true,
                        "processing": true,
                        "stateSave": true,
                        "pageLength": 20,
                        "drawCallback": function(settings) {
                            // Re-initialize bulk delete checkboxes after each draw (pagination, search, etc.)
                            initBulkDeleteCheckboxes();
                        }
                    });
                    initBulkDeleteCheckboxes();
                }
            });
        } catch (error) {
            console.error('DataTables initialization error:', error);
        }
    }
    
    function initializeBackendPaginationTables(container = document) {
        try {
            // Initialize tables with backend pagination (no DataTable pagination)
            $(container).find('.data_table_backend_pagination').each(function() {
                if (!$.fn.DataTable.isDataTable(this)) {
                    new DataTable(this, {
                        dom: "Bfrtip",
                        buttons: ["csv", "excel", "print", "pdf"],
                        "paging": false,  // Disable DataTable pagination
                        "searching": true,  // Keep search functionality
                        "ordering": true,   // Keep sorting functionality
                        "responsive": true,
                        "processing": false,
                        "stateSave": false,
                        "info": false,     // Hide "Showing X to Y of Z entries"
                        "drawCallback": function(settings) {
                            // Re-initialize bulk delete checkboxes after each draw
                            initBulkDeleteCheckboxes();
                        }
                    });
                    initBulkDeleteCheckboxes();
                }
            });
        } catch (error) {
            console.error('Backend pagination DataTables initialization error:', error);
        }
    }
    
    function initializeDataTableServerRenderTables(container = document) {
        try {
            $(container).find('.data_table_server_render').each(function() {

                let ajaxUrl = $(this).data('ajax-url');
                let exportBtn = $(this).data('export-btn');

                if (!$.fn.DataTable.isDataTable(this)) {
                    const hasBulkCheckbox = $(this).find('thead input#select-all-bulk').length > 0;

                    let config = {
                        dom: exportBtn ? '<"top"Bf>rt<"bottom d-flex justify-content-between align-items-center"ilp><"clear">' : 'frt<"bottom d-flex justify-content-between align-items-center"ilp><"clear">',
                        buttons: ["csv", "excel", "print", "pdf"],
                        serverSide: true,
                        searching: true,
                        ordering: true,
                        responsive: true,
                        processing: true,
                        stateSave: false,
                        lengthMenu: [
                            [10, 25, 50, 100, -1],
                            [10, 25, 50, 100, "All"]
                        ],
                        pageLength: 10,
                        info: true,
                        ajax: {
                            url: ajaxUrl,
                            type: 'GET',
                            data: function (data) {
                                data.filters = getFilters?.('table-filter-container');
                            },
                        },
                        // Key part: reinitialize your bulk checkboxes after every draw
                        drawCallback: function(settings) {
                            if (typeof initBulkDeleteCheckboxes === 'function') {
                                initBulkDeleteCheckboxes(this.api().table().container());
                            }
                        },
                    };

                    // Disable sorting/searching for bulk checkbox column
                    if (hasBulkCheckbox) {
                        let checkboxColumnIndex = $(this).find('thead th').index($(this).find('thead th:has(#select-all-bulk)'));
                        if (checkboxColumnIndex >= 0) {
                            config.columnDefs = [
                                {
                                    targets: checkboxColumnIndex,
                                    orderable: false,
                                    searchable: false
                                }
                            ];
                        }
                    }

                    // Initialize table
                    let table = new DataTable(this, config);

                    // Filter button triggers reload
                    $('#tableFilterBtn').off('click').on('click', function (e) {
                        e.preventDefault();
                        table.ajax.reload();
                    });
                }
            });
        } catch (error) {
            console.error('Server Render DataTables initialization error:', error);
        }
    }
    function initializeSelect2(container = document) {
        $(container).find('.select2').each(function () {
            // console.log('Initializing Select2 field:', $(this));
            const $select = $(this);
            const parentModal = $select.closest('.modal');

            $select.select2({
                width: '100%',
                dropdownParent: parentModal.length ? parentModal : $(document.body)
            });
        });
    }
    // initialization function select2 ajax
    function initializeSelect2Ajax(container = document) {
        $(container).find('.select2-ajax').each(function () {
            const $select = $(this);
            
            // Skip if already initialized
            if ($select.hasClass('select2-hidden-accessible')) {
                console.log('Select2 AJAX field already initialized:', $select.attr('id'));
                return;
            }

            console.log('Initializing AJAX Select2 field:', $(this));
            console.log('All data attributes:', $select.data());

            const parentModal = $select.closest('.modal');
            const parentCanvas = $select.closest('.offcanvas');
            
            // Get data attributes
            const ajaxUrl = $select.data('url');
            const placeholder = $select.data('placeholder') || 'Select an option';
            const perPage = $select.data('per-page') || 15;
            
            if (!ajaxUrl) {
                console.warn('Select2 AJAX field missing data-url attribute:', $select.attr('id'));
                return;
            }

            // console.log("ajaxUrl", ajaxUrl);
            // console.log("placeholder", placeholder);
            // console.log("perPage", perPage);
            // console.log("parentModal", parentModal);
            // console.log("parentCanvas", parentCanvas);
            // console.log("select", $select);

            $select.select2({
                width: '100%',
                placeholder: placeholder,
                allowClear: true,
                dropdownParent: parentModal.length ? parentModal : (parentCanvas.length ? parentCanvas : $(document.body)),
                ajax: {
                    url: ajaxUrl,
                    dataType: 'json',
                    delay: 700,
                    data: function (params) {
                        return {
                            search: params.term,
                            page: params.page || 1,
                            per_page: perPage
                        };
                    },
                    processResults: function (data, params) {
                        params.page = params.page || 1;
                        
                        return {
                            results: data.data.map(function(item) {
                                return {
                                    id: item.id,
                                    text: item.label
                                };
                            }),
                            pagination: {
                                more: data.pagination.more
                            }
                        };
                    },
                    cache: true
                },
                minimumInputLength: 1
            });
        });
    }
    

    document.addEventListener("DOMContentLoaded", function () {
        initializeTables();
        initializeBackendPaginationTables();
        initializeDataTableServerRenderTables();
        initializeSelect2();
        // initializeSelect2Ajax();
    });

    // ######---pasteable file fields handling---#######
    document.addEventListener("paste", async function (e) {
        const active = document.activeElement;
        if (!active.classList.contains("paste-source-field")) return;

        const group = active.closest(".paste-file-group");
        if (!group) return;

        const fileInput = group.querySelector(".paste-target-file");
        if (!fileInput) return;

        const items = e.clipboardData?.items || [];
        const filesToAdd = [];

        for (const item of items) {
            if (item.kind === "file") {
                const file = item.getAsFile();
                if (file) filesToAdd.push(file);
            } else if (item.type === "text/plain") {
                const text = await new Promise(res => item.getAsString(res));
                if (/^https?:\/\/.*\.(png|jpg|jpeg|gif|webp)$/i.test(text)) {
                    try {
                        const res = await fetch(text);
                        const blob = await res.blob();
                        const name = text.split("/").pop();
                        filesToAdd.push(new File([blob], name, { type: blob.type }));
                    } catch (err) {
                        console.error("Image URL fetch failed:", err);
                    }
                }
            }
        }

        if (filesToAdd.length) {
            // Check if this is a single file input (no 'multiple' attribute)
            const isSingleFile = !fileInput.hasAttribute('multiple');
            
            if (isSingleFile) {
                // For single file: replace existing files
                replaceFilesInInput(fileInput, filesToAdd);
            } else {
                // For multiple files: append to existing files
                appendFilesToInput(fileInput, filesToAdd, { triggerChange: true });
            }
            console.log("fileInput paste event", fileInput);
            // renderPreview(fileInput);
            active.value = "✅ Image added!";
            setTimeout(() => (active.value = ""), 1500);
        }
    });
    /**
     * Replace files in input (for single file fields)
     */
    function replaceFilesInInput(fileInput, newFiles, options = { triggerChange: true }) {
        const dt = new DataTransfer();
        
        // Add only the new files (replace existing)
        for (const f of newFiles) dt.items.add(f);
        
        fileInput.files = dt.files;
        if (options.triggerChange) {
            fileInput.dispatchEvent(new Event("change", { bubbles: true }));
        }
    }
    
    /**
     * Append new files to input, optionally skipping the 'change' event
     */
    function appendFilesToInput(fileInput, newFiles, options = { triggerChange: true }) {
        const dt = new DataTransfer();

        // Add existing files
        for (const f of fileInput.files) dt.items.add(f);
        // Add new files
        for (const f of newFiles) dt.items.add(f);

        fileInput.files = dt.files;
        if (options.triggerChange) {
            fileInput.dispatchEvent(new Event("change", { bubbles: true }));
        }
    }

    /**
     * Attach single 'change' listener to all file inputs
     */
    document.querySelectorAll(".paste-target-file, .file-input-group").forEach(input => {
        // prevent multiple event bindings
        console.log("input", input);
        input.removeEventListener("change", handleFileChange);
        input.addEventListener("change", handleFileChange);
    });

    function handleFileChange(e) {
        renderPreview(e.target);
    }

    /**
     * Get file icon based on file type
     */
     function getFileIcon(file) {
        const extension = file.name.split('.').pop().toLowerCase();
        const iconMap = {
            'pdf': 'mdi-file-pdf-box',
            'doc': 'mdi-file-word-box',
            'docx': 'mdi-file-word-box',
            'xls': 'mdi-file-excel-box',
            'xlsx': 'mdi-file-excel-box',
            'ppt': 'mdi-file-powerpoint-box',
            'pptx': 'mdi-file-powerpoint-box',
            'zip': 'mdi-folder-zip',
            'rar': 'mdi-folder-zip',
            'txt': 'mdi-file-document-outline',
            'csv': 'mdi-file-delimited-outline',
            'mp4': 'mdi-file-video',
            'avi': 'mdi-file-video',
            'mov': 'mdi-file-video',
            'mp3': 'mdi-file-music',
            'wav': 'mdi-file-music',
        };
        return iconMap[extension] || 'mdi-file-document-outline';
    }
    /**
     * Render preview safely
     */
    function renderPreview(fileInput) {
        const previewContainer = fileInput.closest(".input-group")?.nextElementSibling;
        console.log("preview rendering before checking...", previewContainer);
        if (!previewContainer || !previewContainer.classList.contains("file-preview-container")) return;
        console.log("preview rendering is working..");
        // Clear all previews before re-render
        previewContainer.innerHTML = "";

        Array.from(fileInput.files).forEach((file, index) => {

            const wrapper = document.createElement("div");
                wrapper.className = "position-relative";
                wrapper.style = 'border: 1px solid #dee2e6; padding: 8px; border-radius: 4px; background: #f8f9fa;';
                wrapper.dataset.index = index;

            if(file.type.startsWith("image/")) {

                const reader = new FileReader();
                reader.onload = function (e) {
                    wrapper.innerHTML = `
                        <img src="${e.target.result}" 
                            class="border rounded" 
                            style="max-width: 120px; height: auto; border-radius: 4px; display: block;">
                        <button type="button" 
                            class="btn btn-sm btn-danger remove-preview-btn" 
                            title="Remove image"
                            style="position:absolute;top:-5px;right:-5px;border-radius:50%;padding:0 6px;line-height:1;">×</button>
                    `;
                    // Store index in DOM dataset
                    wrapper.dataset.index = index;
                    previewContainer.appendChild(wrapper);
                };
                reader.readAsDataURL(file);

            } else {
                // Handle non-image files
                const fileIcon = getFileIcon(file);
                const fileName = file.name.length > 15 ? file.name.substring(0, 15) + '...' : file.name;
                wrapper.innerHTML = `
                    <div class="text-center" style="padding: 10px 0;">
                        <i class="mdi ${fileIcon}" style="font-size: 48px; color: #6c757d;"></i>
                        <button type="button" 
                            class="btn btn-sm btn-danger remove-preview-btn" 
                            title="Remove file"
                            style="position:absolute;top:-5px;right:-5px;border-radius:50%;padding:0 6px;line-height:1;">×</button>
                    </div>
                    <small class="text-muted d-block text-center mt-1" style="max-width: 120px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; font-size: 11px;">
                        ${fileName}
                    </small>
                `;
                previewContainer.appendChild(wrapper);
            }
        });

        // Remove handler — delegated once per container
        previewContainer.removeEventListener("click", previewRemoveHandler);
        previewContainer.addEventListener("click", previewRemoveHandler);
    }

    /**
     * Remove handler (shared for all containers)
     */
    function previewRemoveHandler(e) {
        if (!e.target.classList.contains("remove-preview-btn")) return;

        // find the preview wrapper we created earlier
        const wrapper = e.target.closest(".position-relative");
        if (!wrapper) return;

        // get the actual file index stored at render time
        const indexStr = wrapper.dataset.index;
        const fileIndex = (indexStr !== undefined) ? parseInt(indexStr, 10) : NaN;

        // fallback: if no dataset.index, compute DOM index (original behaviour)
        const previewContainer = wrapper.parentElement;
        if (!previewContainer) return;

        const fileInput = previewContainer.previousElementSibling.querySelector("input[type='file']");
        if (!fileInput) return;

        if (!Number.isNaN(fileIndex)) {
            // remove by the original file index (correct)
            removeFileAt(fileInput, fileIndex);
        } else {
            // safety fallback: remove by the preview DOM position
            const indexToRemove = Array.from(previewContainer.children).indexOf(wrapper);
            removeFileAt(fileInput, indexToRemove);
        }
    }

    /**
     * Remove file at specific index
     */
    function removeFileAt(input, index) {
        const dt = new DataTransfer();
        Array.from(input.files)
            .filter((_, i) => i !== index)
            .forEach(f => dt.items.add(f));

        input.files = dt.files;
        input.dispatchEvent(new Event("change", { bubbles: true }));
    }
    
</script>
<script>
// Handle removal of existing files when editing
document.addEventListener('DOMContentLoaded', function() {
    // Listen for remove clicks on existing files
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-existing-file')) {
            const button = e.target;
            const fileItem = button.closest('.current-file-item');
            const filePath = fileItem.dataset.filePath;
            
            if (!filePath) {
                console.error('No file path found');
                return;
            }
            
            if (confirm('Are you sure you want to remove this file? This action cannot be undone.')) {
                console.log("file removed", filePath);
                
                // Find the hidden input field - look for sibling element
                const currentFilesPreview = fileItem.closest('.current-files-preview');
                const hiddenInput = currentFilesPreview.previousElementSibling;
                
                if (hiddenInput && hiddenInput.name && hiddenInput.name.endsWith('_removed')) {
                    // Hide the file preview
                    fileItem.style.setProperty('display', 'none', 'important');
                    
                    // For single file fields, just set the value to the file path
                    const isMultiple = hiddenInput && hiddenInput.hasAttribute('multiple');
                    // For multiple file fields, it's a comma-separated list
                    if (isMultiple) {
                        // Multiple files - append to comma-separated list
                        let removedFiles = hiddenInput.value ? hiddenInput.value.split(',') : [];
                        if (!removedFiles.includes(filePath)) {
                            removedFiles.push(filePath);
                            hiddenInput.value = removedFiles.join(',');
                        }
                    } else {
                        // Single file - just set the path
                        hiddenInput.value = filePath;
                    }
                    
                    // Show visual feedback
                    button.style.backgroundColor = '#28a745';
                    button.textContent = '✓';
                    button.disabled = true;
                } else {
                    console.error('Could not find hidden input for removal tracking');
                }
            }
        }
    });
});
</script>
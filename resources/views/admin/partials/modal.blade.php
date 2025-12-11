<!-- Cropper Modal -->
<div class="modal fade" id="cropperModal" tabindex="-1" aria-labelledby="cropperModalLabel" aria-hidden="true" style="z-index: 1200!important;">
    <div class="modal-dialog modal-lg" style="max-width: 800px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Crop Image</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="img-container" style="max-height: 500px; overflow: hidden;">
                    <img id="image-to-crop" src="" alt="Image to crop" style="max-width: 100%;">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="crop-button">Crop & Save</button>
            </div>
        </div>
    </div>
</div>


<!-- Normal Modal -->
<div id="small_modal" class="modal fade" tabindex="-1" aria-labelledby="small_modal_label" aria-hidden="true" style="display: none;" role="dialog">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-primary-subtle p-2">
                <h5 class="modal-title" id="small-modal-title"></h5>
                <button type="button" class="btn-close text-danger" data-bs-dismiss="modal" aria-label="Close"> </button>
            </div>
            <div class="modal-body" id="small-modal-content">

            </div>

        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<!-- Ajax Modal -->
<div id="ajax_modal" class="modal fade" tabindex="-1" aria-labelledby="ajax_modal_label" aria-hidden="true" style="display: none;" role="dialog">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-primary-subtle p-2">
                <h5 class="modal-title" id="ajax-modal-title"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> </button>
            </div>
            <div class="modal-body" id="ajax-modal-content">

            </div>

        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<!-- X-Large Modal -->
<div id="large_modal" class="modal fade" tabindex="-1" aria-labelledby="large_modal_label" aria-hidden="true" style="display: none;" role="dialog">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-primary-subtle p-2">
                <h5 class="modal-title" id="large-modal-title"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> </button>
            </div>
            <div class="modal-body" id="large-modal-content">

            </div>

        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<!-- X-Large Modal -->
<div id="full_modal" class="modal fade" tabindex="-1" aria-labelledby="full_modal_label" aria-hidden="true" style="display: none;" role="dialog">
    <div class="modal-dialog modal-fullscreen modal-dialog-scrollable mx-auto" style="max-width: 1200px;">
        <div class="modal-content">
            <div class="modal-header bg-primary-subtle p-2">
                <h5 class="modal-title" id="full-modal-title"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> </button>
            </div>
            <div class="modal-body" id="full-modal-content">

            </div>

        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<!-- Video Modal -->
<div id="video_modal" class="modal fade" tabindex="-1" aria-labelledby="video_modal_label" aria-hidden="true" role="dialog">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content bg-dark">
            <div class="modal-header border-0">
                <h5 class="modal-title text-white" id="video-modal-title"></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0 text-center">
                <video id="video-player" class="w-100" controls controlsList="nodownload noremoteplayback nofullscreen" playsinline disablePictureInPicture>
                    <source id="video-source" src="" type="video/mp4">
                    Your browser does not support the video tag.
                </video>
            </div>
        </div>
    </div>
</div>

<!-- Image Gallery Modal -->
<div id="image_modal" class="modal fade" tabindex="-1" aria-labelledby="image_modal_label" aria-hidden="true" role="dialog">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary-subtle p-2">
                <h5 class="modal-title" id="image-modal-title">Image Gallery</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center" id="image-modal-body">
                <!-- Images will be loaded dynamically -->
            </div>
        </div>
    </div>
</div>




<script type="text/javascript">
    function loadModalContent(modalId, contentId, titleId, url, header, callback = null, onClose = null) {
        $('#' + contentId).html('<div style="padding:40px; text-align:center;"><div class="spinner-border text-primary" role="status"><span class="sr-only">Loading...</span></div></div>');
        $('#' + titleId).html('Loading...');

        $('#' + modalId).modal({
            backdrop: 'static',
            keyboard: false
        }).modal('show');

        if (typeof onClose === 'function') {
            $('#' + modalId).off('hidden.bs.modal').on('hidden.bs.modal', function () {
                onClose();
            });
        }

        $.ajax({
            url: url,
            success: function (response) {
                $('#' + contentId).html(response);
                $('#' + titleId).html(header);

                // Auto-initialize select2 fields inside loaded content
                $('#' + contentId + ' .select2, #' + contentId + ' .select2-ajax').each(function () {
                    if (!$(this).hasClass('select2-hidden-accessible')) {
                        const $select = $(this);
                        const parentModal = $select.closest('.modal');

                        // console.log('Initializing in MODAL file Select2 field:', $select);

                        if ($select.hasClass('select2-ajax')) {
                            // Initialize AJAX Select2
                            initializeSelect2Ajax($select.parent());
                        } else {

                            $select.select2({
                                width: '100%',
                                dropdownParent: parentModal.length ? parentModal : $(document.body),
                                closeOnSelect: !$select.prop('multiple') // auto close for single
                            });
    
                            // extra guarantee
                            $select.on('select2:select', function () {
                                if (!$select.prop('multiple')) {
                                    $select.select2('close');
                                }
                            });
                        }
                    }
                });
                // Re-initialize file input event listeners for pasteable file fields
                $('#' + contentId + ' .paste-target-file, .file-input-group').each(function() {
                    // Remove existing event listener to prevent duplicates
                    this.removeEventListener("change", handleFileChange);
                    // Add the event listener
                    this.addEventListener("change", handleFileChange);
                });
                
                if (typeof initializeShowIfLogic === 'function') {
                    const formEl = document.querySelector('#' + contentId + ' form');
                    if (formEl) {
                        initializeShowIfLogic(formEl);
                    }
                }

                if (typeof callback === 'function') {
                    callback(response);
                }
            },
            error: function () {
                $('#' + contentId).html('<p class="text-danger text-center">Failed to load content.</p>');
            }
        });
    }

    function showSmallModal(url, header, callback = null, onClose = null) {
        loadModalContent('small_modal', 'small-modal-content', 'small-modal-title', url, header, callback, onClose);
    }

    function showAjaxModal(url, header, callback = null, onClose = null) {
        loadModalContent('ajax_modal', 'ajax-modal-content', 'ajax-modal-title', url, header, callback, onClose);
    }

    function showLargeModal(url, header, callback = null, onClose = null) {
        loadModalContent('large_modal', 'large-modal-content', 'large-modal-title', url, header, callback, onClose);
    }

    function showFullModal(url, header, callback = null, onClose = null) {
        loadModalContent('full_modal', 'full-modal-content', 'full-modal-title', url, header, callback, onClose);
    }

    function alertModalSuccess(message = '', message_title = 'Success!', cancel_button = 'Okay') {
        Swal.fire({
            html: '<div class="mt-3">' +
                '<lord-icon src="https://cdn.lordicon.com/lupuorrc.json" trigger="loop" colors="primary:#0ab39c,secondary:#405189" style="width:120px;height:120px"></lord-icon>' +
                '<div class="mt-4 pt-2 fs-15">' +
                '<h4>' + message_title + '</h4>' +
                '<p class="text-muted mx-4 mb-0">' + message + '</p>' +
                '</div>' +
                '</div>',
            showCancelButton: true,
            showConfirmButton: false,
            cancelButtonClass: 'btn btn-success w-xs mb-1',
            cancelButtonText: cancel_button,
            buttonsStyling: false,
            showCloseButton: true
        });
    }

    function alertModalError(message = 'Something went wrong..!', cancel_button = 'Okay') {
        Swal.fire({
            html: '<div class="mt-3">' +
                '<lord-icon src="https://cdn.lordicon.com/tdrtiskw.json" trigger="loop" colors="primary:#f06548,secondary:#f7b84b" style="width:120px;height:120px"></lord-icon>' +
                '<div class="mt-4 pt-2 fs-15">' +
                '<h2>Oops...!</h2>' +
                '<p class="text-muted mx-4 mb-0">' + message + '</p>' +
                '</div>' +
                '</div>',
            showCancelButton: true,
            showConfirmButton: false,
            cancelButtonClass: 'btn btn-danger1 btn-outline-danger w-xs mb-1',
            cancelButtonText: cancel_button,
            buttonsStyling: false,
            showCloseButton: true
        });
    }

    function confirmModal(message = 'Are you Sure ?', message_description = 'Are you Sure You want to Delete this Account ?', button_text = 'Yes, Delete It!') {
        Swal.fire({
            html: '<div class="mt-3">' +
                '<lord-icon src="https://cdn.lordicon.com/gsqxdxog.json" trigger="loop" colors="primary:#f7b84b,secondary:#f06548" style="width:100px;height:100px"></lord-icon>' +
                '<div class="mt-4 pt-2 fs-15 mx-5">' +
                '<h4>' + message + '</h4>' +
                '<p class="text-muted mx-4 mb-0"> ' + message_description + '</p>' +
                '</div>' +
                '</div>',
            showCancelButton: true,
            confirmButtonClass: 'btn btn-primary w-xs me-2 mb-1',
            confirmButtonText: button_text,
            cancelButtonClass: 'btn btn-danger w-xs mb-1',
            buttonsStyling: false,
            showCloseButton: true
        });
    }

    function confirmClone(clone_url) {
        let message = 'Are you sure?';
        let message_description = 'Do you really want to clone this item?';
        let button_text = 'Yes, Clone It!';

        Swal.fire({
            html: `
                <div class="mt-3">
                    <lord-icon src="https://cdn.lordicon.com/gsqxdxog.json" trigger="loop" 
                            colors="primary:#f7b84b,secondary:#4caf50" style="width:100px;height:100px"></lord-icon>
                    <div class="mt-4 pt-2 fs-15 mx-5">
                        <h4>${message}</h4>
                        <p class="text-muted mx-4 mb-0">${message_description}</p>
                    </div>
                </div>
            `,
            showCancelButton: true,
            confirmButtonText: button_text,
            confirmButtonClass: 'btn btn-primary w-xs me-2 mb-1',
            cancelButtonClass: 'btn btn-danger w-xs mb-1',
            buttonsStyling: false,
            showCloseButton: true,
            preConfirm: () => {
                const confirmBtn = Swal.getConfirmButton();
                confirmBtn.disabled = true;

                // Change text + add spinner
                confirmBtn.innerHTML = `
                    <span class="spinner-border spinner-border-sm me-2"></span>
                    Cloning...
                `;

                return $.ajax({
                    url: clone_url,
                    type: 'POST',
                    data: { _token: $('meta[name="csrf-token"]').attr('content') },
                    dataType: 'json'
                }).then(response => {
                    if (response.status === 'success') {
                        messageSuccess(response.message);

                        if (response.data.action === 'modal' && response.data.url) {
                            showAjaxModal(
                                response.data.url,
                                response.title || 'Edit Item',
                                null,
                                () => location.reload() // reload when modal closes
                            );
                        } else if (response.data.action === 'redirect' && response.data.url) {
                            navigateToPage(response.data.url, false);
                        } else {
                            location.reload();
                        }

                        return true; // resolves preConfirm
                    } else {
                        Swal.showValidationMessage(response.message || 'Clone failed.');
                        confirmBtn.disabled = false;
                        confirmBtn.innerHTML = button_text; // reset
                        return false;
                    }
                }).catch(xhr => {
                    Swal.showValidationMessage('Server error: ' + (xhr.responseJSON?.message || xhr.statusText));
                    confirmBtn.disabled = false;
                    confirmBtn.innerHTML = button_text; // reset
                });
            }
        });
    }

    function confirmDelete(delete_url, redirect_url = null) {
        let button_text = 'Yes, Delete It!';

        Swal.fire({
            html: `
                <div class="mt-3">
                    <lord-icon src="https://cdn.lordicon.com/gsqxdxog.json" trigger="loop"
                            colors="primary:#f7b84b,secondary:#f06548"
                            style="width:100px;height:100px"></lord-icon>
                    <div class="mt-4 pt-2 fs-15 mx-5">
                        <h4>Are you sure?</h4>
                        <p class="text-muted mx-4 mb-0">This action cannot be undone.</p>
                    </div>
                </div>
            `,
            showCancelButton: true,
            confirmButtonClass: 'btn btn-primary w-xs me-2 mb-1',
            confirmButtonText: button_text,
            cancelButtonClass: 'btn btn-danger w-xs mb-1',
            buttonsStyling: false,
            showCloseButton: true,
            preConfirm: () => {
                const confirmBtn = Swal.getConfirmButton();
                confirmBtn.disabled = true;
                confirmBtn.innerHTML = `
                    <span class="spinner-border spinner-border-sm me-2"></span>
                    Deleting...
                `;

                return $.ajax({
                    url: delete_url,
                    type: 'DELETE',
                    data: { _token: $('meta[name="csrf-token"]').attr('content') },
                    dataType: 'json'
                }).then(response => {
                    if (response.status === 'success') {
                        messageSuccess(response.message || 'Item deleted successfully.');
                        if (redirect_url && typeof navigateToPage === 'function') {
                            navigateToPage(redirect_url, false);
                        } else {
                            location.reload();
                        }
                        return true;
                    } else {
                        Swal.showValidationMessage(response.message || 'Delete failed.');
                        confirmBtn.disabled = false;
                        confirmBtn.innerHTML = button_text;
                        return false;
                    }
                }).catch(xhr => {
                    Swal.showValidationMessage('Server error: ' + (xhr.responseJSON?.message || xhr.statusText));
                    confirmBtn.disabled = false;
                    confirmBtn.innerHTML = button_text;
                });
            }
        });
    }




    function confirmDeleteBulk(deleteUrl, redirectUrl = null, selected = []) {
        const count = selected.length;

        if (count === 0) {
            return messageWarning('Please select at least one item to delete.');
        }

        Swal.fire({
            icon: 'warning',
            html: `
                <div class="mt-3">
                    <lord-icon src="https://cdn.lordicon.com/gsqxdxog.json" trigger="loop"
                        colors="primary:#f7b84b,secondary:#f06548"
                        style="width:100px;height:100px"></lord-icon>
                    <div class="mt-4 pt-2 fs-15 mx-5">
                        <h4>Are you sure?</h4>
                        <div class="fw-semibold text-primary fs-6 mt-1">${count} item(s) selected</div>
                        <p class="text-muted mx-4 mb-0">This action cannot be undone. Do you want to proceed?</p>
                    </div>
                </div>
            `,
            showCancelButton: true,
            confirmButtonClass: 'btn btn-primary w-xs me-2 mb-1',
            confirmButtonText: 'Yes, Delete It!',
            cancelButtonClass: 'btn btn-danger w-xs mb-1',
            buttonsStyling: false,
            showCloseButton: true,
            preConfirm: () => {
                const confirmBtn = Swal.getConfirmButton();
                confirmBtn.disabled = true;

                // Spinner + new text
                confirmBtn.innerHTML = `
                    <span class="spinner-border spinner-border-sm me-2"></span>
                    Deleting...
                `;

                return $.ajax({
                    url: deleteUrl,
                    type: 'POST', // Or 'DELETE' if supported with payload
                    dataType: 'json',
                    data: { 
                        ids: selected,
                        _token: $('meta[name="csrf-token"]').attr('content')
                    }
                }).then(response => {
                    if (response.status === 'success') {
                        messageSuccess(response.message || 'Deleted successfully.');

                        if (redirectUrl && typeof navigateToPage === 'function') {
                            navigateToPage(redirectUrl, false);
                        } else {
                            location.reload();
                        }

                        return true;
                    } else {
                        Swal.showValidationMessage(response.message || 'Delete failed.');
                        confirmBtn.disabled = false;
                        confirmBtn.innerHTML = 'Yes, Delete It!'; // reset
                        return false;
                    }
                }).catch(xhr => {
                    Swal.showValidationMessage('Server error: ' + (xhr.responseJSON?.message || xhr.statusText));
                    confirmBtn.disabled = false;
                    confirmBtn.innerHTML = 'Yes, Delete It!'; // reset
                });
            }
        });
    }




    function approveModal(approve_url, message = 'Are you Sure ?', message_description = 'Are you Sure You want to Approve this?', button_text = 'Yes, Approve It!') {
        Swal.fire({
            html: '<div class="mt-3">' +
                '<lord-icon src="https://cdn.lordicon.com/cgzlioyf.json" trigger="loop" delay="2000" stroke="bold" state="hover-loading" colors="primary:#109121" style="width:150px;height:150px"></lord-icon>' +
                '<div class="mt-4 pt-2 fs-15 mx-5">' +
                '<h4>' + message + '</h4>' +
                '<p class="text-muted mx-4 mb-0"> ' + message_description + '</p>' +
                '</div>' +
                '</div>',
            showCancelButton: true,
            confirmButtonClass: 'btn btn-success w-xs me-2 mb-1',
            confirmButtonText: button_text,
            cancelButtonClass: 'btn btn-danger w-xs mb-1',
            buttonsStyling: false,
            showCloseButton: true,
            preConfirm: () => {
                window.location.href = approve_url;
            }
        });
    }

    function rejectModal(reject_url, message = 'Are you Sure ?', message_description = 'Are you Sure You want to Reject this?', button_text = 'Yes, Reject It!') {
        Swal.fire({
            html: '<div class="mt-3">' +
                '<lord-icon src="https://cdn.lordicon.com/krenhavm.json" trigger="loop" colors="primary:#e83a30,secondary:#c71f16" style="width:150px;height:150px"></lord-icon>' +
                '<div class="mt-4 pt-2 fs-15 mx-5">' +
                '<h4>' + message + '</h4>' +
                '<p class="text-muted mx-4 mb-0"> ' + message_description + '</p>' +
                '</div>' +
                '</div>',
            showCancelButton: true,
            confirmButtonClass: 'btn btn-danger w-xs me-2 mb-1',
            confirmButtonText: button_text,
            cancelButtonClass: 'btn btn-primary w-xs mb-1',
            buttonsStyling: false,
            showCloseButton: true,
            preConfirm: () => {
                window.location.href = reject_url;
            }
        });
    }

    function restoreModal(restore_url, message = 'Are you Sure ?', message_description = 'Are you Sure You want to Restore this?', button_text = 'Yes, Restore It!') {
        Swal.fire({
            html: '<div class="mt-3">' +
                '<lord-icon src="https://cdn.lordicon.com/rsbokaso.json" trigger="loop" delay="2000" stroke="bold" colors="primary:#16c79e" style="width:150px;height:150px"></lord-icon>' +
                '<div class="mt-4 pt-2 fs-15 mx-5">' +
                '<h4>' + message + '</h4>' +
                '<p class="text-muted mx-4 mb-0"> ' + message_description + '</p>' +
                '</div>' +
                '</div>',
            showCancelButton: true,
            confirmButtonClass: 'btn btn-success w-xs me-2 mb-1',
            confirmButtonText: button_text,
            cancelButtonClass: 'btn btn-danger w-xs mb-1',
            buttonsStyling: false,
            showCloseButton: true,
            preConfirm: () => {
                window.location.href = restore_url;
            }
        });
    }

    function showVideoModal(title, videoUrl) {
        // Set title
        $('#video-modal-title').text(title);

        // Set video source
        $('#video-source').attr('src', videoUrl);

        // Reload the player
        $('#video-player')[0].load();

        // Show modal
        $('#video_modal').modal('show');

        // Stop video when modal is closed
        $('#video_modal').off('hidden.bs.modal').on('hidden.bs.modal', function () {
            let player = $('#video-player')[0];
            player.pause();
            player.currentTime = 0;
        });
    }

    function showFileModal(title, files = []) {
        $('#image-modal-title').text(title);

        let html = '';
        if (files.length > 0) {
            html += '<div class="row g-2">';
            files.forEach((file, index) => {
                // Determine file type based on extension
                const extension = file.split('.').pop().toLowerCase();
                const imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp', 'svg'];
                const isImage = imageExtensions.includes(extension);
                
                if (isImage) {
                    // Show image preview
                    html += `
                        <div class="col-md-4 col-6">
                            <a href="${file}" target="_blank">
                                <img src="${file}" class="img-fluid rounded shadow-sm" alt="Image ${index + 1}">
                            </a>
                        </div>
                    `;
                } else {
                    // Show file download button
                    html += `
                        <div class="col-md-4 col-6">
                            <div class="card h-100">
                                <div class="card-body text-center d-flex flex-column justify-content-center">
                                    <i class="mdi mdi-file-document-outline" style="font-size: 48px; color: #6c757d;"></i>
                                    <h6 class="card-title mt-2 mb-1">File ${index + 1}</h6>
                                    <small class="text-muted mb-2">${extension.toUpperCase()}</small>
                                    <a href="${file}" target="_blank" class="btn btn-sm btn-outline-primary">
                                        <i class="mdi mdi-download"></i> Download
                                    </a>
                                </div>
                            </div>
                        </div>
                    `;
                }
            });
            html += '</div>';
        } else {
            html = '<p class="text-muted">No files available.</p>';
        }

        $('#image-modal-body').html(html);
        $('#image_modal').modal('show');
    }

    // Keep the old function for backward compatibility
    function showImageModal(title, images = []) {
        showFileModal(title, images);
    }

</script>

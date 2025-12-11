<!-- top offcanvas -->
<div class="offcanvas offcanvas-top" tabindex="-1" id="offcanvasTop" aria-labelledby="offcanvasTopLabel" style="min-height: 38vh!important;">
    <div class="offcanvas-header bg-primary-subtle p-2">
        <h5 id="offcanvasTopLabel">Offcanvas Top</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        ...
    </div>
</div>

<!-- top offcanvas large -->
<div class="offcanvas offcanvas-top" tabindex="-1" id="offcanvasTopLarge" aria-labelledby="offcanvasTopLargeLabel" style="min-height: 75vh!important;">
    <div class="offcanvas-header bg-primary-subtle p-2">
        <h5 id="offcanvasTopLargeLabel">Offcanvas Top Large</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        ...
    </div>
</div>

<!-- right offcanvas -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasRight" aria-labelledby="offcanvasRightLabel" style="min-width: 30%!important;">
    <div class="offcanvas-header bg-primary-subtle p-2">
        <h5 id="offcanvasRightLabel">Offcanvas Right</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        ...
    </div>
</div>

<!-- right offcanvas large -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasRightLarge" aria-labelledby="offcanvasRightLargeLabel" style="min-width: 45%!important;">
    <div class="offcanvas-header bg-primary-subtle p-2">
        <h5 id="offcanvasRightLargeLabel">Offcanvas Right Large</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        ...
    </div>
</div>

<!-- bottom offcanvas -->
<div class="offcanvas offcanvas-bottom" tabindex="-1" id="offcanvasBottom" aria-labelledby="offcanvasBottomLabel" style="min-height: 38vh!important;">
    <div class="offcanvas-header bg-primary-subtle p-2">
        <h5 id="offcanvasBottomLabel">Offcanvas Bottom</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        ...
    </div>
</div>

<!-- bottom offcanvas large -->
<div class="offcanvas offcanvas-bottom" tabindex="-1" id="offcanvasBottomLarge" aria-labelledby="offcanvasBottomLargeLabel" style="min-height: 75vh!important;">
    <div class="offcanvas-header bg-primary-subtle p-2">
        <h5 id="offcanvasBottomLargeLabel">Offcanvas Bottom Large</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        ...
    </div>
</div>

<!-- left offcanvas -->
<div class="offcanvas offcanvas-start" tabindex="-1" id="offcanvasLeft" aria-labelledby="offcanvasLeftLabel" style="min-width: 30%!important;">
    <div class="offcanvas-header bg-primary-subtle p-2">
        <h5 id="offcanvasLeftLabel">Offcanvas Left</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        ...
    </div>
</div>

<!-- left offcanvas large -->
<div class="offcanvas offcanvas-start" tabindex="-1" id="offcanvasLeftLarge" aria-labelledby="offcanvasLeftLargeLabel" style="min-width: 45%!important;">
    <div class="offcanvas-header bg-primary-subtle p-2">
        <h5 id="offcanvasLeftLargeLabel">Offcanvas Left Large</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        ...
    </div>
</div>


<script>
    function loadCanvasContent(canvasId, contentId, titleId, url, header, callback = null, onClose = null) {
        $('#' + contentId).html('<div style="padding:40px; text-align:center;"><div class="spinner-border text-primary" role="status"><span class="sr-only">Loading...</span></div></div>');
        $('#' + titleId).html('Loading...');

        $('#' + canvasId).offcanvas('show');

        if (typeof onClose === 'function') {
            $('#' + canvasId).off('hidden.bs.offcanvas').on('hidden.bs.offcanvas', function () {
                onClose();
            });
        }

        $.ajax({
            url: url,
            success: function (response) {
                $('#' + contentId).html(response);
                $('#' + titleId).html(header);

                // Auto-initialize select2 fields inside loaded content
                $('#' + contentId + ' .select2').each(function () {
                    if (!$(this).hasClass('select2-hidden-accessible')) {
                        const $select = $(this);
                        const parentCanvas = $select.closest('.offcanvas');

                        console.log('Initializing in CANVAS file Select2 field:', $select);
                        $select.select2({
                            width: '100%',
                            dropdownParent: parentCanvas.length ? parentCanvas : $(document.body),
                            closeOnSelect: !$select.prop('multiple') // auto close for single
                        });

                        // extra guarantee
                        $select.on('select2:select', function () {
                            if (!$select.prop('multiple')) {
                                $select.select2('close');
                            }
                        });
                    }
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

    function canvasTop(url, header, callback = null, onClose = null) {
        loadCanvasContent('offcanvasTop', 'offcanvasTop .offcanvas-body', 'offcanvasTopLabel', url, header, callback, onClose);
    }

    function canvasTopLarge(url, header, callback = null, onClose = null) {
        loadCanvasContent('offcanvasTopLarge', 'offcanvasTopLarge .offcanvas-body', 'offcanvasTopLargeLabel', url, header, callback, onClose);
    }

    function canvasBottom(url, header, callback = null, onClose = null) {
        loadCanvasContent('offcanvasBottom', 'offcanvasBottom .offcanvas-body', 'offcanvasBottomLabel', url, header, callback, onClose);
    }

    function canvasBottomLarge(url, header, callback = null, onClose = null) {
        loadCanvasContent('offcanvasBottomLarge', 'offcanvasBottomLarge .offcanvas-body', 'offcanvasBottomLargeLabel', url, header, callback, onClose);
    }

    function canvasLeft(url, header, callback = null, onClose = null) {
        loadCanvasContent('offcanvasLeft', 'offcanvasLeft .offcanvas-body', 'offcanvasLeftLabel', url, header, callback, onClose);
    }

    function canvasLeftLarge(url, header, callback = null, onClose = null) {
        loadCanvasContent('offcanvasLeftLarge', 'offcanvasLeftLarge .offcanvas-body', 'offcanvasLeftLargeLabel', url, header, callback, onClose);
    }

    function canvasRight(url, header, callback = null, onClose = null) {
        loadCanvasContent('offcanvasRight', 'offcanvasRight .offcanvas-body', 'offcanvasRightLabel', url, header, callback, onClose);
    }

    function canvasRightLarge(url, header, callback = null, onClose = null) {
        loadCanvasContent('offcanvasRightLarge', 'offcanvasRightLarge .offcanvas-body', 'offcanvasRightLargeLabel', url, header, callback, onClose);
    }
</script>
<script>
    // Show loading overlay
    function showLoading() {
        if ($('#global-loading').length === 0) {
            $('body').append('<div id="global-loading" class="loading-overlay"><div class="spinner-border text-primary" role="status"><span class="sr-only">Loading...</span></div></div>');
        }
    }

    // Hide loading overlay
    function hideLoading() {
        $('#global-loading').remove();
    }

    // Handle form submission
    function navigateToPage(pageUrl, modalId){
        if(modalId){
            $('#'+modalId).modal('hide');
        }
        window.location.href = pageUrl;
    }

    // Show form submit loading (only for elements inside the given form)
    function showLoadingBtn(form){
        const $form = $(form);
        const $submitBtn = $form.find('.btn-save');
        $submitBtn.prop('disabled', true);
        $submitBtn.find('.btn-text', $form).addClass('d-none');
        $submitBtn.find('.spinner-border', $form).removeClass('d-none');
    }

    // Hide form submit loading (only for elements inside the given form)
    function hideLoadingBtn(form){
        const $form = $(form);
        const $submitBtn = $form.find('.btn-save');
        $submitBtn.prop('disabled', false);
        $submitBtn.find('.btn-text', $form).removeClass('d-none');
        $submitBtn.find('.spinner-border', $form).addClass('d-none');
    }

    /**
     * Key Filter
     */
    function filterKey(value) {
        return value
            .toLowerCase()
            .replace(/[-\s]/g, '_')
            .replace(/[^a-z0-9_]/g, '');
    }
    /**
     * Slug Filter
     */
    function filterSlug(value) {
        return value
            .toLowerCase()
            .replace(/[-\s]/g, '-')
            .replace(/[^a-z0-9-]/g, '');
    }

    /**
     * Whitespace Filter
     */
    function filterWhitespace(value) {
        return value.replace(/\s+/g, '');
    }
 


    /**
     * Bulk Delete Button
     */

     function initBulkDeleteCheckboxes(container = document) {
        const $container = $(container);

        function toggleBulkDeleteButton() {
            const selectedCount = $container.find('.row-checkbox:checked').length;
            if (selectedCount > 0) {
                $('#bulk-delete-btn').removeClass('d-none');
            } else {
                $('#bulk-delete-btn').addClass('d-none');
            }
        }

        // Clear previous bindings to avoid duplicates
        $container.find('.row-checkbox').off('change').on('change', function () {
            const allChecked = $container.find('.row-checkbox').length === $container.find('.row-checkbox:checked').length;
            $('#select-all-bulk').prop('checked', allChecked);
            toggleBulkDeleteButton();
        });

        $('#select-all-bulk').off('change').on('change', function () {
            $container.find('.row-checkbox').prop('checked', this.checked);
            toggleBulkDeleteButton();
        });

        toggleBulkDeleteButton(); // initial trigger
    }

    function deleteSelected(deleteUrl, redirectUrl){
        let selected = $('.row-checkbox:checked').map(function () {
            return $(this).val();
        }).get();

        if (selected.length === 0) {
            return messageWarning('Please select at least one item to delete.');
        }

        confirmDeleteBulk(
            deleteUrl,
            redirectUrl,
            selected
        );
    }

    function initAjaxFilterForm(formSelector = '#filter-form', baseUrl = null) {
        const $form = $(formSelector);
        if (!$form.length) return;

        $form.on('submit', function (e) {
            e.preventDefault();

            const formData = $form.serialize();
            const targetUrl = (baseUrl || $form.attr('action')) + '?' + formData;

            if (typeof loadPageContent === 'function') {
                loadPageContent(targetUrl);
            } else {
                console.warn('loadPageContent() is not defined');
            }
        });
    }

    function initFilterResetButton(resetBtnSelector = '#clear-filter-btn', formSelector = '#filter-form') {
        const $btn = $(resetBtnSelector);
        const $form = $(formSelector);
        if (!$btn.length || !$form.length) return;

        $btn.off('click').on('click', function (e) {
            e.preventDefault();

            // Reset all input/select fields
            $form.trigger('reset');

            // If using select2 or other plugins, manually reset them too
            $form.find('select').each(function () {
                if ($(this).hasClass('select2')) {
                    $(this).val('').trigger('change');
                }
            });

            // Reload unfiltered page
            const baseUrl = $form.attr('action') || window.location.pathname;
            if (typeof loadPageContent === 'function') {
                loadPageContent(baseUrl);
            } else {
                window.location.href = baseUrl; // fallback
            }
        });
    }


    /**
     * Sortable Form Function
     */
     function initSortableForm(options) {
        let settings = $.extend({
            form: '#sort-form',
            saveUrl: '',
            redirectUrl: ''
        }, options);

        // Enable sortable for all lists with .sortable class
        $('.sortable').sortable({
            placeholder: 'bg-light border',
            update: function(event, ui) {
                if (typeof settings.onUpdate === 'function') {
                    let ids = $('.sortable li').map(function(){ return $(this).data('id'); }).get();
                    settings.onUpdate(ids);
                }
            }
        });

        // Handle submit
        $(settings.form).on('submit', function(e){
            e.preventDefault();
            showLoadingBtn(this);

            let order = [];
            $('.sortable li').each(function(){
                order.push($(this).data('id'));
            });

            $.post(settings.saveUrl, { 
                order: order,
                _token: $('meta[name="csrf-token"]').attr('content')
            }, function(response){
                if (response.status === 'success') {
                    messageSuccess(response.message);
                    if (settings.redirectUrl) {
                        navigateToPage(settings.redirectUrl, 'small_modal');
                    }
                } else {
                    messageDanger(response.message);
                }
            }, 'json').always(() => {
                hideLoadingBtn(this);
            });
        });
    }

    /**
     * Ajax Form Function
     */
    function initAjaxForm(options) {
        let settings = $.extend({
            form: '',
            onBefore: null,
            onSuccess: null,
            onError: null,
            redirectUrl: ''
        }, options);


        let form = settings.form;
        let formData = new FormData(form);

        if (typeof settings.onBefore === 'function') {
            settings.onBefore(formData, form);
        }

        if(form.checkValidity()){
            showLoadingBtn(form);

            $.ajax({
                url: $(form).attr('action'),
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function(response){
                    console.log(response);

                    if(response.status === 'success'){
                        messageSuccess(response.message);

                        if (typeof settings.onSuccess === 'function') {
                            settings.onSuccess(response, form);
                        }

                        if (settings.redirectUrl) {
                            navigateToPage(settings.redirectUrl, 'ajax_modal');
                        }
                    } else {
                        messageDanger(response.message || 'Something went wrong.');
                    }
                },
                error: function(xhr){
                    if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                        // Show first validation error
                        const firstError = Object.values(xhr.responseJSON.errors)[0][0];
                        messageDanger(firstError);
                    } else if (xhr.responseJSON && xhr.responseJSON.message) {
                        messageDanger(xhr.responseJSON.message);
                    } else {
                        messageDanger('Server error: ' + xhr.statusText);
                    }
                    
                    if (typeof settings.onError === 'function') {
                        settings.onError(xhr, form);
                    }
                },
                complete: function() {
                    hideLoadingBtn(form);
                }
            });
        } else {
            messageDanger('Please fill in all required fields.');
        }
    }



    function initPageComponents(container = document) {
        initializeTables(container);
        initializeSelect2(container);
        initBulkDeleteCheckboxes(container);
        initAjaxFilterForm(container);
        initFilterResetButton(container);
    }


    $(function () {
        initializeSelect2();

        $('body').on('submit', '.ajax-crud-form', function (e) {
            e.preventDefault();
            const form = this;
            const $form = $(form);
            const $submitBtn = $form.find('button[type=submit]');
            const redirectUrl = $form.data('redirect') || window.location.href;

            initAjaxForm({
                form: form,
                redirectUrl: redirectUrl,
                onBefore: (formData) => {
                    // Attach cropped files globally
                    Object.entries(window.croppedFiles || {}).forEach(([key, file]) => {
                        if (file) formData.append(key, file);
                    });

                    // Show loading state on button
                    showLoadingBtn(form);
                },
                onAfter: () => {
                    // Reset button state (in case of error)
                    hideLoadingBtn(form);
                }
            });
        });
    });


    function initializeShowIfLogic(context) {
        const form = context || document;
        let firstRun = true;

        function evaluateConditions() {
            form.querySelectorAll('[data-show-if]').forEach(function (el) {
                const condition = el.dataset.showIf ? JSON.parse(el.dataset.showIf) : null;
                if (!condition) return;

                let visible = true;
                for (const [depField, expectedValues] of Object.entries(condition)) {
                    const dep = form.querySelector(`[name="${depField}"]`);
                    if (!dep) {
                        visible = false;
                        break;
                    }

                    const depValue = dep.value;
                    const hasMatch = Array.isArray(expectedValues)
                        ? expectedValues.includes(depValue)
                        : depValue == expectedValues;

                    if (!hasMatch) {
                        visible = false;
                        break;
                    }
                }

                if (visible) {
                    el.classList.remove('d-none');

                    // Re-init select2 if needed
                    $(el).find('select.select2').each(function () {
                        const $select = $(this);
                        if (!$select.hasClass('select2-hidden-accessible')) {
                            $select.select2({
                                width: '100%',
                                dropdownParent: $select.closest('.modal'),
                                closeOnSelect: !$select.prop('multiple')
                            }).on('select2:select', function () {
                                if (!$select.prop('multiple')) {
                                    $select.select2('close');
                                }
                            });
                        }
                    });

                } else {
                    if (!firstRun) {
                        // clear and destroy hidden select2
                        $(el).find('select.select2').each(function () {
                            $(this).val(null).trigger('change');
                            if ($(this).data('select2')) {
                                $(this).select2('destroy');
                            }
                        });
                        // clear other inputs
                        el.querySelectorAll('input, textarea').forEach(input => {
                            input.value = '';
                        });
                    }
                    el.classList.add('d-none');
                }
            });

            firstRun = false;
        }

        evaluateConditions();

        form.querySelectorAll('select, input, textarea').forEach(function (input) {
            input.addEventListener('change', evaluateConditions);
            input.addEventListener('input', evaluateConditions);
        });
    }

   

    document.addEventListener("DOMContentLoaded", function () {
        const images = document.querySelectorAll("img[data-src]");
        const observer = new IntersectionObserver(entries => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    img.src = img.dataset.src;
                    observer.unobserve(img);
                }
            });
        });

        images.forEach(img => observer.observe(img));
    });











</script>
<style>
.loading-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(255, 255, 255, 0.7);
    z-index: 9999;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    font-weight: bold;
    color: #333;
}
</style>


<form id="{{ $formId ?? 'crud-form' }}"
      action="{{ $action }}"
      method="post"
      class="{{ $class ?? '' }}"
      data-redirect="{{ $redirect ?? '' }}"
      enctype="multipart/form-data">
    @csrf
    @isset($method) @method($method) @endisset

    <div class="row">
        @foreach($fields as $field)
            @php
                if (isset($field['enabled']) && !$field['enabled']) {
                    continue;
                }
                $type = $field['type'] ?? 'text';
                $partial = match($type) {
                    'text' => 'admin.crud.fields.text',
                    'number' => 'admin.crud.fields.number',
                    'date' => 'admin.crud.fields.date',
                    'time' => 'admin.crud.fields.time',
                    'datetime' => 'admin.crud.fields.datetime',
                    'textarea' => 'admin.crud.fields.textarea',
                    'url' => 'admin.crud.fields.url',
                    'country-code' => 'admin.crud.fields.country-code',
                    'select' => 'admin.crud.fields.select',
                    'select2' => 'admin.crud.fields.select2',
                    'select2-ajax' => 'admin.crud.fields.select2-ajax',
                    'select2-multiple' => 'admin.crud.fields.select2-multiple',
                    'file' => 'admin.crud.fields.file',
                    'files' => 'admin.crud.fields.files',
                    'image' => 'admin.crud.fields.image',
                    'checkbox' => 'admin.crud.fields.checkbox',
                    'hidden' => 'admin.crud.fields.hidden',
                    'seperator' => 'admin.crud.fields.seperator',
                    'info' => 'admin.crud.fields.info',
                    'note-info' => 'admin.crud.fields.note-info',
                    'custom' => 'admin.crud.fields.custom',
                    'repeater' => 'admin.crud.fields.repeater',
                    'slug' => 'admin.crud.fields.slug',
                    'tags' => 'admin.crud.fields.tags',
                    default => 'admin.crud.fields.text',
                };
            @endphp

            @if($type === 'hidden')
                @include($partial, $field)
            @else
                <div class="col-lg-{{ $field['col'] ?? 12 }} col-sm-12 p-2 field-wrapper"
                     @if(isset($field['show_if']))
                         data-show-if='@json($field["show_if"])'
                     @endif>
                    @include($partial, $field)
                </div>
            @endif
        @endforeach

        {{-- Submit --}}
        <div class="col-12 p-2 pt-3">
            <div class="d-flex gap-2 justify-content-end">
                <button class="btn btn-success btn-save" type="submit" id="submit-btn">
                    <span class="btn-text">
                        <i class="ri-check-fill me-1"></i>
                        {{ $submitText ?? 'Save' }}
                    </span>
                    <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                </button>
            </div>
        </div>
    </div>
</form>

<script>
function initializeShowIfLogic(context) {
    const form = context || document;
    let firstRun = true; // 

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
            } else {
                // only clear on user interaction, not first run
                if (!firstRun) {
                    el.querySelectorAll('input, textarea').forEach(input => {
                        input.value = '';
                    });
                    el.querySelectorAll('select').forEach(select => {
                        if ($(select).hasClass('select2')) {
                            $(select).val(null).trigger('change');
                        } else {
                            select.value = '';
                        }
                    });
                }
                el.classList.add('d-none');
            }
        });

        firstRun = false; // 👈 after first execution
    }

    // Run once on load
    evaluateConditions();

    // Watch changes on all inputs
    form.querySelectorAll('select, input, textarea').forEach(function (input) {
        input.addEventListener('change', evaluateConditions);
        input.addEventListener('input', evaluateConditions);
    });

    // Handle select2 changes
    $(form).find('.select2').on('change', evaluateConditions);
}

// Works for both add and edit forms
initializeShowIfLogic(document.getElementById('{{ $formId ?? "crud-form" }}'));

document.addEventListener('input', function (e) {
    const el = e.target;

    if (el.classList.contains('filter-key-input')) {
        el.value = filterKey(el.value);
    } 
    else if (el.classList.contains('filter-slug-input')) {
        el.value = filterSlug(el.value);
    } 
    else if (el.classList.contains('filter-whitespace-input')) {
        el.value = filterWhitespace(el.value);
    }
});
// Slug field functionality

initializeSlugFields();

function initializeSlugFields() {
    // Find all slug fields within the current form context
    const form = document.getElementById('{{ $formId ?? "crud-form" }}');
    if (!form) return;
    
    // Check if form has slug fields
    const slugFields = form.querySelectorAll('.slug-field');
    if (slugFields.length === 0) return;
    
    // Initially disable submit button if slug fields exist (until we verify availability)
    const submitBtn = form.querySelector('.btn-save, #submit-btn, button[type="submit"]');
    if (submitBtn) {
        submitBtn.disabled = true;
        submitBtn.setAttribute('data-slug-check-required', 'true');
    }
    
    // Find all slug fields in this form
    slugFields.forEach(function(slugField) {
        // Skip if already initialized
        if (slugField.dataset.initialized === 'true') {
            return;
        }
        
        const relatedFieldId = slugField.getAttribute('data-related-field');
        const modelName = slugField.getAttribute('data-model-name');
        const slugFieldId = slugField.id;
        const isRequired = slugField.hasAttribute('required');
        
        if (!relatedFieldId || !modelName) {
            console.warn('Slug field missing required data attributes:', slugFieldId);
            return;
        }
        
        const relatedField = form.querySelector('#' + relatedFieldId);
        if (!relatedField) {
            console.warn('Related field not found:', relatedFieldId);
            return;
        }
        
        // Mark as initialized to prevent duplicate event listeners
        slugField.dataset.initialized = 'true';
        
        let isManualEdit = false;
        let slugCheckTimeout = null;
        
        // Check if this is edit mode (slug field already has a value)
        const isEditMode = slugField.value && slugField.value.trim() !== '';
        
        // Track if user has manually edited the slug field
        let userHasEditedSlug = false;
        
        // Auto-generate slug from related field
        relatedField.addEventListener('input', function() {
            // Only auto-generate if user hasn't manually edited the slug field
            if (!userHasEditedSlug) {
                const slugValue = filterSlug(this.value);
                slugField.value = slugValue;
                
                // Trigger slug check after auto-generation
                if (slugValue) {
                    checkSlugAvailability(slugField, modelName, slugValue, form, isRequired);
                } else {
                    // If slug is empty and not required, allow submission
                    if (!isRequired) {
                        updateSubmitButtonState(form, true);
                    } else {
                        updateSubmitButtonState(form, false);
                    }
                }
            }
        });
        
        // Reset manual edit flag when related field is focused (works in both create and edit mode)
        relatedField.addEventListener('focus', function() {
            // Reset flag so title changes can update slug (unless user has manually edited slug)
            // This allows title changes to update slug even in edit mode
            if (!userHasEditedSlug) {
                isManualEdit = false;
            }
        });
        
        // Manual slug editing
        slugField.addEventListener('input', function() {
            userHasEditedSlug = true; // Mark that user has manually edited slug
            isManualEdit = true;
            this.value = filterSlug(this.value);
            
            // Debounce slug checking
            clearTimeout(slugCheckTimeout);
            const currentSlug = this.value;
            
            if (currentSlug) {
                slugCheckTimeout = setTimeout(function() {
                    checkSlugAvailability(slugField, modelName, currentSlug, form, isRequired);
                }, 500); // Wait 500ms after user stops typing
            } else {
                hideSlugStatus(slugFieldId);
                // If slug is empty and not required, allow submission
                if (!isRequired) {
                    updateSubmitButtonState(form, true);
                } else {
                    updateSubmitButtonState(form, false);
                }
            }
        });
        
        // Also track when slug field gets focus (user might edit it)
        slugField.addEventListener('focus', function() {
            // When user focuses on slug field, they might edit it
            // But don't set userHasEditedSlug yet - only set it on actual input
        });
        
        // Check slug on page load if it has a value (for edit mode)
        const excludeId = slugField.getAttribute('data-exclude-id');
        if (isEditMode && slugField.value) {
            // In edit mode, check slug but allow if it's the current record's slug
            checkSlugAvailability(slugField, modelName, slugField.value, form, isRequired);
        } else if (!isRequired && !slugField.value) {
            // If slug is not required and empty, allow submission
            updateSubmitButtonState(form, true);
        } else if (excludeId && slugField.value) {
            // If we have an exclude_id and slug value, enable button initially (will be checked)
            updateSubmitButtonState(form, true);
        }
    });
}

function checkSlugAvailability(slugField, modelName, slugValue, form, isRequired) {
    const slugFieldId = slugField.id;
    const excludeId = slugField.getAttribute('data-exclude-id') || '';
    
    if (!slugValue || slugValue.trim() === '') {
        hideSlugStatus(slugFieldId);
        // If slug is empty and not required, allow submission
        if (!isRequired) {
            updateSubmitButtonState(form, true);
        } else {
            updateSubmitButtonState(form, false);
        }
        return;
    }
    
    // Disable submit button while checking
    updateSubmitButtonState(form, false);
    
    // Show loading state
    showSlugStatus(slugFieldId, 'Checking availability...', 'info');
    
    // Prepare request body
    const requestBody = {
        model_name: modelName,
        slug: slugValue
    };
    
    // Add exclude_id if available (for edit mode)
    if (excludeId) {
        requestBody.exclude_id = excludeId;
    }
    
    // Make AJAX request
    fetch('/admin/slug/check', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify(requestBody)
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            if (data.data && data.data.exists === true) {
                showSlugStatus(slugFieldId, 'This slug is already in use', 'danger');
                slugField.classList.add('is-invalid');
                slugField.classList.remove('is-valid');
                // Disable submit button - slug exists
                updateSubmitButtonState(form, false);
            } else {
                showSlugStatus(slugFieldId, 'Slug is available', 'success');
                slugField.classList.remove('is-invalid');
                slugField.classList.add('is-valid');
                // Enable submit button - slug is available
                updateSubmitButtonState(form, true);
            }
        } else {
            showSlugStatus(slugFieldId, 'Error checking slug availability', 'warning');
            slugField.classList.add('is-invalid');
            slugField.classList.remove('is-valid');
            // Disable submit button - error occurred
            updateSubmitButtonState(form, false);
        }
    })
    .catch(error => {
        console.error('Error checking slug:', error);
        showSlugStatus(slugFieldId, 'Error checking slug availability', 'warning');
        slugField.classList.add('is-invalid');
        slugField.classList.remove('is-valid');
        // Disable submit button - error occurred
        updateSubmitButtonState(form, false);
    });
}

function updateSubmitButtonState(form, enable) {
    const submitBtn = form.querySelector('.btn-save, #submit-btn, button[type="submit"]');
    if (!submitBtn) return;
    
    // Only update if button has slug check requirement
    if (submitBtn.getAttribute('data-slug-check-required') === 'true') {
        submitBtn.disabled = !enable;
        
        // Add visual feedback
        if (enable) {
            submitBtn.classList.remove('btn-secondary');
            submitBtn.classList.add('btn-success');
        } else {
            submitBtn.classList.remove('btn-success');
            submitBtn.classList.add('btn-secondary');
        }
    }
}

function showSlugStatus(slugFieldId, message, type) {
    const statusMessage = document.getElementById('slug-status-' + slugFieldId);
    if (!statusMessage) return;
    
    statusMessage.style.display = 'block';
    statusMessage.className = 'slug-status-message mt-2';
    
    const badgeClass = type === 'success' ? 'badge bg-success' : 
                      type === 'danger' ? 'badge bg-danger' : 
                      type === 'warning' ? 'badge bg-warning' : 
                      'badge bg-info';
    
    statusMessage.innerHTML = `<span class="${badgeClass}">${message}</span>`;
}

function hideSlugStatus(slugFieldId) {
    const statusMessage = document.getElementById('slug-status-' + slugFieldId);
    if (statusMessage) {
        statusMessage.style.display = 'none';
        statusMessage.innerHTML = '';
    }
    
    const slugField = document.getElementById(slugFieldId);
    if (slugField) {
        slugField.classList.remove('is-invalid', 'is-valid');
    }
}


</script>
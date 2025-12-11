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
                    'textarea' => 'admin.crud.fields.textarea',
                    'url' => 'admin.crud.fields.url',
                    'country-code' => 'admin.crud.fields.country-code',
                    'select' => 'admin.crud.fields.select',
                    'select2' => 'admin.crud.fields.select2',
                    'select-multiple' => 'admin.crud.fields.select2-multiple',
                    'file' => 'admin.crud.fields.file',
                    'files' => 'admin.crud.fields.files',
                    'image' => 'admin.crud.fields.image',
                    'checkbox' => 'admin.crud.fields.checkbox',
                    'hidden' => 'admin.crud.fields.hidden',
                    'seperator' => 'admin.crud.fields.seperator',
                    'repeater' => 'admin.crud.fields.repeater',
                    'custom' => 'admin.crud.fields.custom',
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

</script>
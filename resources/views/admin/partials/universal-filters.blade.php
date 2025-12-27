<div class="card mb-3" id="table-filter-container">
    <div class="card-body">
        <form id="filter-form" action="{{ request()->url() }}" method="GET">
            @isset($searchConfig)
                <!-- Search Input Row -->
                <div class="row">
                    <div class="col-md-8">
                        <label for="search" class="form-label">Search</label>
                        <div class="input-group">
                            <input type="text" 
                                class="form-control" 
                                name="search" 
                                id="search" 
                                value="{{ request('search') }}" 
                                placeholder="Search in: {{ implode(', ', array_values($searchConfig['search_fields'])) }}">
                            <button class="btn btn-outline-danger" type="button" id="clear-search" style="border-radius: 0 5px 5px 0!important;">
                                <i class="ri-close-circle-line"></i>
                            </button>
                        </div>
                        <small class="text-muted"></small>
                    </div>
                   
                    <!-- Show submit button in search row only when no filters exist -->
                    @if(count($filterConfig) == 0)
                    
                    <div class="col-md-4">
                        <div class="d-flex flex-row gap-2 align-items-end" style="height: 100%;border-radius: 0!important;">
                            <button type="submit" class="btn btn-primary" style="width: 130px;border-radius: 5px!important;">
                                <i class="ri-search-line"></i> &nbsp;Search
                            </button>
                        </div>
                    </div>
                    @endif
                </div>
            @endisset

            <!-- Dynamic Filters Row -->
            @if(count($filterConfig) > 0)
            <div class="row mt-3">
                @foreach($filterConfig as $fieldName => $fieldConfig)
                    @php
                        if (isset($fieldConfig['enabled']) && !$fieldConfig['enabled']) {
                            continue;
                        }
                        $type = $fieldConfig['type'] ?? 'select';
                        $partial = match($type) {
                            'select' => 'admin.partials.filter-fields.select',
                            'select2' => 'admin.partials.filter-fields.select2',
                            'select2-ajax' => 'admin.partials.filter-fields.select2-ajax',
                            'select-multiple' => 'admin.partials.filter-fields.select-multiple',
                            'relationship' => 'admin.partials.filter-fields.select2',
                            'date_range' => 'admin.partials.filter-fields.date-range',
                            'date' => 'admin.partials.filter-fields.date',
                            'text' => 'admin.partials.filter-fields.text',
                            'number' => 'admin.partials.filter-fields.number',
                            default => 'admin.partials.filter-fields.select',
                        };
                        
                        // Prepare field data
                        $fieldData = array_merge([
                            'name' => $fieldName,
                            'id' => $fieldName,
                            'label' => $fieldConfig['label'] ?? ucfirst(str_replace('_', ' ', $fieldName)),
                        ], $fieldConfig);
                    @endphp

                    @include($partial, $fieldData)
                @endforeach

                <!-- Action Buttons - Show only when filters exist -->
                <div class="col-md-2 mb-3">
                    <div class="d-flex flex-row gap-2 align-items-end" style="height: 100%;border-radius: 0!important;">
                        <button type="submit" id="tableFilterBtn" class="btn btn-primary" style="border-radius: 5px!important;">
                            <i class="ri-search-line"></i>
                        </button>
                        <button type="button" id="clear-all-filters" class="btn btn-outline-danger reset-btn" style="border-radius: 5px!important;">
                            <i class="ri-close-circle-line"></i>
                        </button>
                    </div>
                </div>
            </div>
            @endif
        </form>
    </div>
</div>

<script>
$(document).ready(function() {
    // Initialize simple filter system
    window.simpleFilter = new SimpleFilterSystem();
    window.simpleFilter.init();

});

class SimpleFilterSystem {
    init() {
        this.bindEvents();
        this.initializeComponents();
    }

    bindEvents() {
        // Form submission
        $('#filter-form').on('submit', this.handleFormSubmit.bind(this));
        
        // Clear all (including search)
        $('#clear-all-filters').on('click', this.clearAll.bind(this));
        
        // Clear search only
        $('#clear-search').on('click', this.clearSearch.bind(this));
    }

    initializeComponents() {
        // Initialize Select2
        $('.select2').select2({
            placeholder: function() {
                return $(this).data('placeholder') || 'Select an option';
            },
            allowClear: true,
            width: '100%'
        });
        
        setTimeout(() => {
            initializeSelect2Ajax();
        }, 500);
    }

    handleFormSubmit(e) {
        e.preventDefault();
        this.submitForm();
    }

    clearSearch() {
        $('#search').val('');
    }

    clearAll() {
        // Clear search input
        $('#search').val('');
        
        // Clear all select2 dropdowns
        $('.select2').val('').trigger('change');
        
        // Clear select2-ajax dropdowns
        $('.select2-ajax').val(null).trigger('change');

        // Clear date inputs
        $('input[type="date"]').val('');
        
        // Clear any other text inputs
        $('input[type="text"]').not('#search').val('');
        
        // Clear number inputs
        $('input[type="number"]').val('');
    }

    submitForm() {
        const formData = $('#filter-form').serialize();
        const url = new URL(window.location);
        url.search = '';
        
        const params = new URLSearchParams(formData);
        params.forEach((value, key) => {
            if (value) {
                url.searchParams.set(key, value);
            }
        });
        
        if (typeof loadPageContent === 'function') {
            loadPageContent(url.toString());
        } else {
            window.location.href = url.toString();
        }
    }
}
</script>

<style>

/* Align all form elements */
.align-items-end .form-label {
    margin-bottom: 0.5rem;
}

/* Ensure consistent spacing */
.col-md-1 .d-flex {
    height: 100%;
    justify-content: center;
}

/* Small text styling */
.text-muted {
    font-size: 0.75rem;
    margin-top: 0.25rem;
}
</style>
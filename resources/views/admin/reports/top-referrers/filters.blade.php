<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ url('admin/reports/top-referrers') }}" id="filter-form">
            <div class="row align-items-end">
                <!-- Search -->
                <div class="col-md-4 mb-3">
                    <label class="form-label">Search</label>
                    <input type="text" 
                           name="search" 
                           class="form-control" 
                           placeholder="Search by name, email, or phone" 
                           value="{{ request('search') }}">
                </div>

                <!-- Date From -->
                <div class="col-md-3 mb-3">
                    <label class="form-label">Date From</label>
                    <input type="date" 
                           name="date_from" 
                           class="form-control" 
                           value="{{ $filters['date_from'] ?? '' }}">
                </div>

                <!-- Date To -->
                <div class="col-md-3 mb-3">
                    <label class="form-label">Date To</label>
                    <input type="date" 
                           name="date_to" 
                           class="form-control" 
                           value="{{ $filters['date_to'] ?? '' }}">
                </div>

                <!-- Filter Buttons -->
                <div class="col-md-2 mb-3">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="ri-search-line"></i> Filter
                    </button>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    @if(request()->hasAny(['search', 'date_from', 'date_to']))
                        <a href="{{ url('admin/reports/top-referrers') }}" class="btn btn-sm btn-outline-secondary">
                            <i class="ri-close-line"></i> Clear Filters
                        </a>
                    @endif
                </div>
            </div>
        </form>
    </div>
</div>




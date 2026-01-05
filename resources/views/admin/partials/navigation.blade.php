<div class="app-menu navbar-menu">
    <!-- LOGO -->
    <div class="navbar-brand-box" style="margin-top: 10px;">
        <!-- Dark Logo -->
        <a href="{{ url('admin/dashboard/index') }}" class="logo logo-dark">
            <span class="logo-sm" style="line-height:0px">
                <img src="{{ get_site_logo() }}" alt="" height="50">
                <span style="font-size: 8px; font-weight: 500; white-space: nowrap;">
                    {{ config('app.name') }}
                </span>
            </span>
            <span class="logo-lg">
                <img src="{{ get_site_logo() }}" alt="" height="50">
            </span>
        </a>

        <!-- Light Logo -->
        <a href="{{ url('admin/dashboard/index') }}" class="logo logo-light">
            <span class="logo-sm">
                <img src="{{ get_site_logo() }}" alt="" height="50">
            </span>
            <span class="logo-lg">
                <img src="{{ get_site_logo() }}" alt="" height="17">
                <img src="{{ get_site_logo() }}" alt="" height="50" style="filter: brightness(0) invert(1);">
            </span>
        </a>

        <br>
        <button type="button" class="btn btn-sm p-0 fs-20 header-item float-end btn-vertical-sm-hover" id="vertical-hover">
            <i class="ri-record-circle-line"></i>
        </button>
    </div>

    <!-- Menu Items -->
    <div id="scrollbar" style="margin-top: 10px;">
        <div class="container-fluid">

            <div id="two-column-menu"></div>

            <ul class="navbar-nav" id="navbar-nav">
                @php
                    $menuItems = config('admin_menu');
                    $visibleHeaders = [];
                    $currentHeader = null;
                    
                    // First pass: determine which headers have visible items
                    foreach ($menuItems as $item) {
                        if (!empty($item['is_header'])) {
                            $currentHeader = $item;
                            continue;
                        }
                        
                        // Check if this item is visible
                        $isVisible = false;
                        
                        // Permission check
                        if (!empty($item['can']) && !auth()->user()->can($item['can'])) {
                            continue;
                        }
                        
                        // Feature check
                        if (!empty($item['feature']) && !has_feature($item['feature'])) {
                            continue;
                        }
                        
                        // For dropdowns, check if any children are visible
                        if (!empty($item['children'])) {
                            $visibleChildren = collect($item['children'])->filter(function($child) {
                                return empty($child['can']) || auth()->user()->can($child['can']);
                            });
                            
                            if ($visibleChildren->isEmpty()) {
                                continue;
                            }
                        }
                        
                        // If we get here, the item is visible
                        // Mark the current header as having visible items
                        if ($currentHeader) {
                            $visibleHeaders[$currentHeader['title']] = true;
                        }
                    }
                @endphp

                @php
                    $currentHeader = null;
                @endphp
                @foreach($menuItems as $item)

                    {{-- Header --}}
                    @if(!empty($item['is_header']))
                        @php
                            $currentHeader = $item;
                        @endphp
                        {{-- Only show header if it has visible items following it --}}
                        @if(isset($visibleHeaders[$item['title']]))
                            <li class="nav-item">
                                <div class="menu-title text-primary text-uppercase fs-11 fw-semibold ms-2">
                                    <span>{{ $item['title'] }}</span>
                                </div>
                            </li>
                        @endif
                        @continue
                    @endif

                    {{-- Permission check --}}
                    @if(!empty($item['can']) && !auth()->user()->can($item['can']))
                        @continue
                    @endif

                    {{-- Feature check --}}
                    @if(!empty($item['feature']) && !has_feature($item['feature']))
                        @continue
                    @endif

                    {{-- Dropdown --}}
                    @if(!empty($item['children']))
                        {{-- Filter children based on permissions --}}
                        @php
                            $visibleChildren = collect($item['children'])->filter(function($child) {
                                return empty($child['can']) || auth()->user()->can($child['can']);
                            });
                        @endphp

                        {{-- Only show parent if there are visible children --}}
                        @if($visibleChildren->isNotEmpty())
                            <li class="nav-item">
                                <a class="nav-link menu-link"
                                   href="#menu-{{ Str::slug($item['title']) }}"
                                   data-bs-toggle="collapse"
                                   role="button"
                                   aria-expanded="false"
                                   aria-controls="menu-{{ Str::slug($item['title']) }}">
                                    <i class="{{ $item['icon'] ?? 'ri-folder-line' }}"></i>
                                    <span>{{ $item['title'] }}</span>
                                </a>

                                <div class="collapse menu-dropdown
                                    {{ $visibleChildren->pluck('route')->filter(fn($r) => Request::is($r . '*'))->isNotEmpty() ? 'show' : '' }}"
                                    id="menu-{{ Str::slug($item['title']) }}">
                                    <ul class="nav nav-sm flex-column">
                                        @foreach($visibleChildren as $child)
                                            <li class="nav-item">
                                                <a class="nav-link {{ Request::is($child['route'] . '*') ? 'active' : '' }}"
                                                   href="{{ url($child['route']) }}">
                                                    {{ $child['title'] }}
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </li>
                        @endif
                    @else
                        {{-- Single link --}}
                        <li class="nav-item">
                            <a class="nav-link menu-link {{ menu_active($item['route']) }}"
                               href="{{ url($item['route']) }}">
                                <i class="{{ $item['icon'] ?? 'ri-folder-line' }}"></i>
                                <span>{{ $item['title'] }}</span>
                            </a>
                        </li>
                    @endif

                @endforeach
            </ul>
        </div>
    </div>
    <!-- Sidebar background -->
    <div class="sidebar-background"></div>
</div>

<!-- Vertical Overlay -->
<div class="vertical-overlay"></div>

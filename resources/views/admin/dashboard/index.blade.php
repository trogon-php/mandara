@extends('admin.layouts.app')

@section('content')
{!! show_window_title($page_title) !!}
<!-- Welcome Section -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card welcome-card">
            <div class="card-body p-4">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h3 class="mb-2 fw-semibold">Welcome back, Admin!</h3>
                        <p class="mb-0 opacity-75">Here's your LMS performance overview</p>
                    </div>
                    <div class="flex-shrink-0">
                        <div class="avatar-lg bg-white bg-opacity-10 rounded-circle p-3">
                            <i class="ri-dashboard-line fs-1 text-white"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Key Stats Overview -->
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="fw-semibold mb-0">Quick Overview</h5>
            <div class="dropdown">
                <button class="btn btn-sm btn-soft-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    <i class="ri-calendar-line me-1"></i> This Month
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="#">Today</a></li>
                    <li><a class="dropdown-item" href="#">This Week</a></li>
                    <li><a class="dropdown-item" href="#">This Month</a></li>
                    <li><a class="dropdown-item" href="#">This Year</a></li>
                </ul>
            </div>
        </div>
    </div>
    
    <!-- Total Students -->
    <div class="col-xl-3 col-md-6">
        <div class="card stat-card h-100">
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <div class="flex-shrink-0">
                        <div class="avatar-md rounded-circle bg-primary-subtle p-2">
                            <i class="ri-user-line fs-2" style="color: #357980;"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="text-muted mb-0">Total Students</h6>
                        <h2 class="mb-0 fw-semibold">{{ $number_of_students ?? 0 }}</h2>
                    </div>
                </div>
                <div>
                    <div class="d-flex justify-content-between mb-2">
                        <div>
                            <span class="badge bg-success-subtle px-2 py-1" style="color: #27474A;">
                                <i class="ri-shopping-cart-line me-1"></i> Purchased
                            </span>
                        </div>
                        <div>
                            <span class="fw-semibold fs-5" style="color: #27474A;">{{ $purchased_students ?? '332' }}</span>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between">
                        <div>
                            <span class="badge bg-light px-2 py-1 text-muted">
                                <i class="ri-user-unfollow-line me-1"></i> Not Purchased
                            </span>
                        </div>
                        <div>
                            <span class="fw-semibold fs-5 text-muted">{{ $not_purchased_students ?? '178' }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Tutors -->
    <div class="col-xl-3 col-md-6">
        <div class="card stat-card h-100">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="avatar-md rounded-circle bg-info-subtle p-2">
                            <i class="ri-user-voice-line fs-2" style="color: #357980;"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="text-muted mb-0">Total Tutors</h6>
                        <h2 class="mb-0 fw-semibold">{{ $number_of_instructors ?? 0 }}</h2>
                        <div class="mt-2">
                            <span class="badge bg-info-subtle p-1 px-2 rounded" style="color: #357980;">
                                <i class="ri-arrow-up-line"></i> 4% from last month
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- New Students Registered Today -->
    <div class="col-xl-3 col-md-6">
        <div class="card stat-card h-100">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="avatar-md rounded-circle bg-success-subtle p-2">
                            <i class="ri-user-add-line fs-2" style="color: #27474A;"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="text-muted mb-0">New Students Today</h6>
                        <h2 class="mb-0 fw-semibold">{{ $new_students_today ?? 0 }}</h2>
                        <div class="mt-2">
                            <span class="badge bg-success-subtle p-1 px-2 rounded" style="color: #27474A;">
                                <i class="ri-arrow-up-line"></i> 8% from yesterday
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- New Students Registered This Week -->
    <div class="col-xl-3 col-md-6">
        <div class="card stat-card h-100">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="avatar-md rounded-circle bg-warning-subtle p-2">
                            <i class="ri-group-line fs-2" style="color: #357980;"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="text-muted mb-0">New Students This Week</h6>
                        <h2 class="mb-0 fw-semibold">{{ $new_students_week ?? 0 }}</h2>
                        <div class="mt-2">
                            <span class="badge bg-warning-subtle p-1 px-2 rounded" style="color: #357980;">
                                <i class="ri-arrow-up-line"></i> 15% from last week
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Financial Overview -->
<div class="row mb-4">
    <div class="col-xl-5">
        <div class="card h-100">
            <div class="card-header bg-transparent border-bottom d-flex align-items-center">
                <h5 class="card-title mb-0 flex-grow-1">Total Income</h5>
                <div class="flex-shrink-0">
                    <div class="dropdown">
                        <button class="btn btn-sm btn-soft-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            This Year
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="#">This Month</a></li>
                            <li><a class="dropdown-item" href="#">Last Quarter</a></li>
                            <li><a class="dropdown-item" href="#">This Year</a></li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h2 class="mb-0">₹ {{ $total_income ?? '87,429' }}</h2>
                        <p class="text-muted mb-0">Total Revenue Generated</p>
                    </div>
                    <div>
                        <span class="badge bg-success-subtle p-2 px-3 rounded" style="color: #27474A;">
                            <i class="ri-arrow-up-line me-1"></i> {{ $income_growth_percentage ?? '0.0' }}% from last year
                        </span>
                    </div>
                </div>
                
                <div class="mb-3">
                    <h6 class="mb-3">Income This Month</h6>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h4 class="mb-0">₹ {{ $total_income_month ?? '12,580' }}</h4>
                        <span class="badge bg-success-subtle p-1 px-2 rounded" style="color: #27474A;">
                            <i class="ri-arrow-up-line"></i> {{ $monthly_income_growth_percentage ?? '0.0' }}%
                        </span>
                    </div>
                    <div class="progress" style="height: 8px;">
                        <div class="progress-bar bg-gradient-primary" role="progressbar" style="width: 75%;" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>
                
                <div id="monthly-profits-chart" class="apex-charts mt-4" style="height: 250px;"></div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-7">
        <div class="card h-100">
            <div class="card-header bg-transparent border-bottom">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h5 class="card-title mb-0">Course-wise Income</h5>
                    </div>
                    <div class="flex-shrink-0">
                        <div class="dropdown">
                            <button class="btn btn-sm btn-soft-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                All Time
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="#">All Time</a></li>
                                <li><a class="dropdown-item" href="#">This Month</a></li>
                                <li><a class="dropdown-item" href="#">This Year</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-borderless table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th scope="col">Course</th>
                                <th scope="col">Students</th>
                                <th scope="col">Income</th>
                                <th scope="col">This Month</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(isset($course_data))
                                @foreach($course_data as $course)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="flex-shrink-0">
                                                <div class="avatar-sm bg-primary-subtle rounded">
                                                    <span class="avatar-title bg-primary-subtle text-primary rounded fs-4" style="color: #357980;">
                                                        <i class="ri-code-line"></i>
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="ms-3">
                                                <h6 class="mb-0 fw-medium">{{ $course['title'] }}</h6>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $course['students'] }}</td>
                                    <td>₹ {{ number_format($course['income']) }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <span class="badge bg-success-subtle ms-2" style="color: #27474A;">
                                                ₹ {{ number_format($course['income'] * 0.13) }}
                                            </span>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="4" class="text-center">No course data available</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Enrollment Analytics -->
<div class="row mb-4">
    <div class="col-xl-7">
        <div class="card">
            <div class="card-header bg-transparent border-bottom">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h5 class="card-title mb-0">Student Registration Analytics</h5>
                    </div>
                    <div class="flex-shrink-0">
                        <div class="dropdown">
                            <button class="btn btn-sm btn-soft-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                Last 12 Months
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="#">Last 6 Months</a></li>
                                <li><a class="dropdown-item" href="#">Last 12 Months</a></li>
                                <li><a class="dropdown-item" href="#">This Year</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body pt-3">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h2 class="mb-0">{{ $total_registrations ?? '1,248' }}</h2>
                        <p class="text-muted mb-0">Total Registrations</p>
                    </div>
                    <div>
                        <span class="badge bg-success-subtle p-2 px-3 rounded" style="color: #27474A;">
                            <i class="ri-arrow-up-line me-1"></i> 24.8% growth
                        </span>
                    </div>
                </div>
                <div id="student-registration-chart" class="apex-charts" style="height: 300px;"></div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-5">
        <div class="card">
            <div class="card-header bg-transparent border-bottom">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h5 class="card-title mb-0">Course-wise Students</h5>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div id="course-students-chart" class="apex-charts" style="height: 250px;"></div>
                <div class="mt-4">
                    @if(isset($course_data) && !empty($course_data))
                        @foreach($course_data as $index => $course)
                        <div class="d-flex justify-content-between mb-3">
                            <div class="d-flex align-items-center">
                                <i class="ri-checkbox-blank-circle-fill text-primary me-2" style="color: #357980 !important;"></i>
                                <span>{{ $course['title'] }}</span>
                            </div>
                            <div>
                                <span class="fw-medium">{{ $course['students'] }}</span>
                                <span class="text-muted ms-2">({{ $course['purchased'] }} purchased)</span>
                            </div>
                        </div>
                        @endforeach
                    @else
                        <div class="text-center py-4">
                            <div class="mb-3">
                                <i class="ri-book-open-line text-muted" style="font-size: 3rem; color: #ccc;"></i>
                            </div>
                            <p class="text-muted mb-0">No course data available</p>
                            <small class="text-muted">Course statistics will appear here once students start enrolling</small>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.welcome-card {
    background: linear-gradient(to left, #508066, #19341a) !important
    border-radius: 15px;
    margin-bottom: 1.5rem;
    overflow: hidden;
    transition: all 0.3s ease;
}

.welcome-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 30px rgba(59, 116, 168, 0.3);
}
.card {
    border: none;
    box-shadow: 0 0 10px rgba(59, 116, 168, 0.08);
    margin-bottom: 24px;
    border-radius: 12px;
    overflow: hidden;
}

.card-header {
    background-color: transparent;
    border-bottom: 1px solid rgba(59, 116, 168, 0.1);
    padding: 1.25rem 1.5rem;
}

.btn-soft-primary {
    background-color: rgba(59, 116, 168, 0.1);
    color: #3B74A8;
    border: 1px solid rgba(59, 116, 168, 0.2);
    transition: all 0.3s ease;
}

.btn-soft-primary:hover {
    background-color: rgba(59, 116, 168, 0.2);
    color: #1C3E68;
    border-color: #3B74A8;
    transform: translateY(-1px);
}

.dropdown-toggle::after {
    display: none;
}

.badge {
    font-weight: 500;
    padding: 0.5rem 0.8rem;
    border-radius: 30px;
}

.fs-11 {
    font-size: 11px;
}

.fs-13 {
    font-size: 13px;
}

.bg-gradient-primary {
    background: linear-gradient(to right, #3B74A8, #1C3E68) !important;
}

.rounded-pill {
    border-radius: 50rem;
}

a {
    text-decoration: none;
    color: inherit;
}

a:hover {
    text-decoration: none;
}

.text-decoration-none:hover {
    color: inherit;
}

.table>:not(caption)>*>* {
    padding: 1rem 1.25rem;
    border-bottom-width: 1px;
}

.table>:not(:first-child) {
    border-top: 0;
}

.text-primary {
    color: #3B74A8 !important;
}

.text-success {
    color: #1C3E68 !important;
}

.progress {
    background-color: rgba(59, 116, 168, 0.1);
    border-radius: 10px;
    height: 8px;
}

.progress-bar {
    border-radius: 10px;
    background: linear-gradient(to right, #3B74A8, #1C3E68) !important;
}


/* Update badge colors */
.badge.bg-success-subtle {
    background-color: rgba(28, 62, 104, 0.1) !important;
    color: #1C3E68 !important;
}

.badge.bg-info-subtle {
    background-color: rgba(59, 116, 168, 0.1) !important;
    color: #3B74A8 !important;
}

.badge.bg-warning-subtle {
    background-color: rgba(59, 116, 168, 0.1) !important;
    color: #3B74A8 !important;
}

.badge.bg-primary-subtle {
    background-color: rgba(59, 116, 168, 0.1) !important;
    color: #3B74A8 !important;
}

/* Update table avatar colors */
.avatar-sm.bg-primary-subtle {
    background-color: rgba(59, 116, 168, 0.1) !important;
}

.avatar-title.bg-primary-subtle {
    background-color: rgba(59, 116, 168, 0.1) !important;
    color: #3B74A8 !important;
}

/* Update chart colors in JavaScript */
</style>

<script>
// Monthly Profits Chart
var profitOptions = {
    series: [{
        name: 'Monthly Profit',
        data: {!! json_encode($monthly_profits ?? [12500, 14200, 11800, 15400, 16800, 14300, 19200, 21500, 19800, 22400, 25800, 28900]) !!}
    }],
    chart: {
        type: 'area',
        height: 250,
        toolbar: {
            show: false
        },
        fontFamily: "'Inter', sans-serif",
        zoom: {
            enabled: false
        }
    },
    colors: ['#3B74A8'],
    dataLabels: {
        enabled: true,
        formatter: function(val) {
            return '$' + (val/1000).toFixed(1) + 'k';
        },
        style: {
            fontSize: '10px',
            colors: ['#333']
        }
    },
    stroke: {
        curve: 'smooth',
        width: 3
    },
    fill: {
        type: 'gradient',
        gradient: {
            shadeIntensity: 1,
            opacityFrom: 0.6,
            opacityTo: 0.2,
            stops: [0, 90, 100]
        }
    },
    grid: {
        borderColor: '#f1f1f1',
        strokeDashArray: 4,
        show: true
    },
    xaxis: {
        categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
        labels: {
            style: {
                colors: '#333333',
                fontSize: '12px',
                fontFamily: "'Inter', sans-serif",
                fontWeight: 500
            }
        },
        axisBorder: {
            show: true
        },
        axisTicks: {
            show: true
        }
    },
    yaxis: {
        labels: {
            formatter: function(value) {
                return '$' + (value / 1000).toFixed(1) + 'k';
            },
            style: {
                colors: '#333333',
                fontSize: '12px',
                fontFamily: "'Inter', sans-serif",
                fontWeight: 500
            }
        },
        title: {
            text: 'Revenue ($)',
            style: {
                color: '#333333',
                fontSize: '12px',
                fontFamily: "'Inter', sans-serif",
                fontWeight: 500
            }
        }
    },
    tooltip: {
        x: {
            format: 'MM yyyy'
        },
        y: {
            formatter: function(value) {
                return '$' + value.toLocaleString();
            }
        }
    }
};

var profitChart = new ApexCharts(document.querySelector("#monthly-profits-chart"), profitOptions);
profitChart.render();

// Student Registration Chart
var studentRegOptions = {
    series: [{
        name: 'Total Registrations',
        data: {!! json_encode($student_registrations ?? [82, 68, 90, 110, 120, 94, 115, 130, 135, 145, 155, 170]) !!}
    }, {
        name: 'Purchased Course',
        data: {!! json_encode($purchased_courses ?? [45, 52, 68, 74, 96, 72, 90, 98, 110, 115, 125, 132]) !!}
    }],
    chart: {
        type: 'bar',
        height: 300,
        toolbar: {
            show: false
        },
        fontFamily: "'Inter', sans-serif",
    },
    plotOptions: {
        bar: {
            horizontal: false,
            columnWidth: '60%',
            borderRadius: 5
        },
    },
    colors: ['#3B74A8', '#1C3E68'],
    dataLabels: {
        enabled: false
    },
    stroke: {
        show: true,
        width: 2,
        colors: ['transparent']
    },
    grid: {
        borderColor: '#f1f1f1',
        strokeDashArray: 4
    },
    xaxis: {
        categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
        labels: {
            style: {
                colors: '#888888',
                fontSize: '12px',
                fontFamily: "'Inter', sans-serif",
                fontWeight: 400
            }
        }
    },
    yaxis: {
        title: {
            text: 'Number of Students',
            style: {
                color: '#888888',
                fontSize: '12px',
                fontFamily: "'Inter', sans-serif",
                fontWeight: 400
            }
        },
        labels: {
            style: {
                colors: '#888888',
                fontSize: '12px',
                fontFamily: "'Inter', sans-serif",
                fontWeight: 400
            }
        }
    },
    legend: {
        position: 'top',
        horizontalAlign: 'right',
        fontFamily: "'Inter', sans-serif",
        fontSize: '14px',
        offsetY: 0
    },
    fill: {
        opacity: 1
    },
    tooltip: {
        y: {
            formatter: function (val) {
                return val + " students"
            }
        }
    }
};

var studentRegChart = new ApexCharts(document.querySelector("#student-registration-chart"), studentRegOptions);
studentRegChart.render();

// Course-wise Students Chart
var courseStudentsOptions = {
    series: {!! json_encode($course_chart_data['series'] ?? []) !!},
    chart: {
        type: 'donut',
        height: 250,
        fontFamily: "'Inter', sans-serif",
    },
    labels: {!! json_encode($course_chart_data['labels'] ?? []) !!},
    colors: ['#3B74A8', '#1C3E68', '#5A9FD4', '#2E5A8A', '#4A7BA7'],
    plotOptions: {
        pie: {
            donut: {
                size: '65%',
                background: 'transparent',
                labels: {
                    show: true,
                    name: {
                        show: true,
                        fontSize: '14px',
                        fontFamily: "'Inter', sans-serif",
                        color: '#343a40',
                        offsetY: -10
                    },
                    value: {
                        show: true,
                        fontSize: '18px',
                        fontFamily: "'Inter', sans-serif",
                        color: '#343a40',
                        formatter: function (val) {
                            return val
                        }
                    },
                    total: {
                        show: true,
                        label: 'Total',
                        fontSize: '14px',
                        fontFamily: "'Inter', sans-serif",
                        color: '#888888',
                        formatter: function (w) {
                            return w.globals.seriesTotals.reduce((a, b) => a + b, 0)
                        }
                    }
                }
            }
        }
    },
    dataLabels: {
        enabled: false
    },
    legend: {
        show: false
    },
    responsive: [{
        breakpoint: 480,
        options: {
            chart: {
                height: 250
            },
            legend: {
                show: false
            }
        }
    }],
    stroke: {
        width: 0
    }
};

var courseStudentsChart = new ApexCharts(document.querySelector("#course-students-chart"), courseStudentsOptions);
courseStudentsChart.render();

// Add responsive behavior for mobile
window.addEventListener('resize', function() {
    var width = window.innerWidth;
    if (width < 768) {
        // Adjust cards for mobile view
        document.querySelectorAll('.stat-card .d-flex').forEach(function(el) {
            el.classList.add('flex-column');
            el.classList.add('align-items-start');
        });
    } else {
        // Reset for desktop
        document.querySelectorAll('.stat-card .d-flex').forEach(function(el) {
            el.classList.remove('flex-column');
            el.classList.remove('align-items-start');
        });
    }
});

// Initialize any tooltips
document.addEventListener('DOMContentLoaded', function() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
</script>
@endsection


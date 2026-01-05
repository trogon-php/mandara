<?php

namespace App\Services\App;

use App\Http\Resources\Amenities\AppAmenityListResource;
use App\Services\Amenities\AmenityService;

class MandaraDashboardService extends AppBaseService
{
    protected string $cachePrefix = 'mandara_dashboard';
    protected int $defaultTtl = 300;

    /**
     * Create a new class instance.
     */
    public function __construct(
        private AmenityAppService $amenityAppService,
        private AmenityService $amenityService)
    {
        //
    }

    public function getDashboard($userId)
    {
        $user = authUser();
        return [
            'user_data' => [
                'name' => $user->name,
                'profile_picture' => $user->profile_picture_url,
            ],
            'package_info' => [
                'room' => 'Room 211',
                'package_name' => 'Package 1',
                'since' => 'Day 5',
                'remaining' => 'of 14 days stay',
            ],
            'baby_live_cam' => $this->getBabyLiveCam(),
            'baby_health_data' => $this->getBabyHealthData($userId),
            'mother_health_data' => $this->getMotherHealthData($userId),
            'todays_schedule' => $this->getTodaysSchedule($userId),
            'amenities' => $this->amenityAppService->listAmenities(3),
            'doctor_amenity' => AppAmenityListResource::make($this->amenityService->find(10)),
            'nurse_amenity' => AppAmenityListResource::make($this->amenityService->find(11)),
        ];

    }

    public function getBabyDashboard($userId)
    {
        return [
            'baby_live_cam' => $this->getBabyLiveCam(),
            'sleep_cycle' => [
                'day_sleep' => '8.2h',
                'weekly_average' => '7.5h',
                'no_of_wakeups' => '2',
                'day_naps_count' => '3',
                'day_naps_quality' => 'Good quality',
                'day_naps_color' => 'success',
                'weekly_naps_count' => '15',
                'weekly_naps_quality' => 'Average quality',
                'weekly_naps_color' => 'warning',
            ],
            'baby_health_data' => $this->getBabyHealthData($userId),
            'sleep_graph_data' => [
                'baby_name' => 'Emma',
                'unit'  => 'hours',
                'max_hours' => '10',
                'weekly_sleep_summary' => [
                    [
                        'day' => 'Sunday',
                        'short' => 'S',
                        'hours' => '9'
                    ],
                    [
                        'day' => 'Monday',
                        'short' => 'M',
                        'hours' => '8'
                    ],
                    [
                        'day' => 'Tuesday',
                        'short' => 'T',
                        'hours' => '7'
                    ],
                    [
                        'day' => 'Wednesday',
                        'short' => 'W',
                        'hours' => '7'
                    ],
                    [
                        'day' => 'Thursday',
                        'short' => 'T',
                        'hours' => '7'
                    ],
                    [
                        'day' => 'Friday',
                        'short' => 'F',
                        'hours' => '7'
                    ],
                    [
                        'day' => 'Saturday',
                        'short' => 'S',
                        'hours' => '9'
                    ],
                ]

            ],
            'weight_graph_data' => [
                'baby_name' => 'Emma',
                'unit' => 'kg',
                'interval_x' => '1',
                'interval_y' => '6',
                'x_axis' => 'weeks',
                'min_x' => '0',
                'max_x' => '6',
                'min_y' => '0',
                'max_y' => '10',
                'weight_progress' => [
                    [
                        'week' => '0',
                        'weight' => '0.5',
                    ],
                    [
                        'week' => '1',
                        'weight' => '1',
                    ],
                    [
                        'week' => '2',
                        'weight' => '1.5',
                    ],
                    [
                        'week' => '3',
                        'weight' => '2',
                    ],
                    [
                        'week' => '4',
                        'weight' => '2.5',
                    ],
                    [
                        'week' => '5',
                        'weight' => '3',
                    ],
                    [
                        'week' => '6',
                        'weight' => '3.5',
                    ]
                ]
            ],
            'health_records' => [
                [
                    'title' => 'BCG Vaccine',
                    'date' => 'December 30, 2025',
                    'description' => 'BCG Vaccine given to Emma',
                ],
                [
                    'title' => 'DPT Vaccine',
                    'date' => 'December 30, 2025',
                    'description' => 'DPT Vaccine given to Emma',
                ],
                [
                    'title' => 'Hepatitis B Vaccine',
                    'date' => 'December 30, 2025',
                    'description' => 'Hepatitis B Vaccine given to Emma',
                ],
                [
                    'title' => 'OPV Vaccine',
                    'date' => 'December 30, 2025',
                    'description' => 'OPV Vaccine given to Emma',
                ],
                [
                    'title' => 'Measles Vaccine',
                    'date' => 'December 30, 2025',
                    'description' => 'Measles Vaccine given to Emma',
                ]
            ],
            'feed_log' => [
                'total_feed_count' => '3',
                'feed_logs' => [
                    [
                        'title' => 'Feeding completed',
                        'time' => '10:00',
                        'time_standard' => 'AM',
                        'feed_amount' => '100ml',
                    ],
                    [
                        'title' => 'Feeding completed',
                        'time' => '12:00',
                        'time_standard' => 'PM',
                        'feed_amount' => '150ml',
                    ],
                    [
                        'title' => 'Feeding completed',
                        'time' => '01:00',
                        'time_standard' => 'PM',
                        'feed_amount' => '100ml',
                    ],
                    [
                        'title' => 'Feeding completed',
                        'time' => '03:00',
                        'time_standard' => 'PM',
                        'feed_amount' => '150ml',
                    ],
                ]

            ]
            
        ];
    }

    public function getMotherDashboard($userId)
    {
        return [
            'mother_health_data' => [
                'blood_pressure' => '120/80',
                'heart_rate' => '120 BPM',
                'temperature' => '96.8',
                'weight' => '70 Kg',
            ],
            'sleep_cycle' => [
                'day_sleep' => '8.2h',
                'weekly_average' => '7.5h',
                'no_of_wakeups' => '2',
                'day_naps_count' => '3',
                'day_naps_quality' => 'Good quality',
                'day_naps_color' => 'success',
                'weekly_naps_count' => '15',
                'weekly_naps_quality' => 'Average quality',
                'weekly_naps_color' => 'warning',
            ],
            'sleep_graph_data' => [
                'baby_name' => 'Emma',
                'unit'  => 'hours',
                'max_hours' => '10',
                'weekly_sleep_summary' => [
                    [
                        'day' => 'Sunday',
                        'short' => 'S',
                        'hours' => '9'
                    ],
                    [
                        'day' => 'Monday',
                        'short' => 'M',
                        'hours' => '8'
                    ],
                    [
                        'day' => 'Tuesday',
                        'short' => 'T',
                        'hours' => '7'
                    ],
                    [
                        'day' => 'Wednesday',
                        'short' => 'W',
                        'hours' => '7'
                    ],
                    [
                        'day' => 'Thursday',
                        'short' => 'T',
                        'hours' => '7'
                    ],
                    [
                        'day' => 'Friday',
                        'short' => 'F',
                        'hours' => '7'
                    ],
                    [
                        'day' => 'Saturday',
                        'short' => 'S',
                        'hours' => '9'
                    ],
                ]

            ],
            'weight_graph_data' => [
                'baby_name' => 'Emma',
                'unit' => 'kg',
                'interval_x' => '1',
                'interval_y' => '6',
                'x_axis' => 'weeks',
                'min_x' => '0',
                'max_x' => '6',
                'min_y' => '0',
                'max_y' => '10',
                'weight_progress' => [
                    [
                        'week' => '0',
                        'weight' => '0.5',
                    ],
                    [
                        'week' => '1',
                        'weight' => '1',
                    ],
                    [
                        'week' => '2',
                        'weight' => '1.5',
                    ],
                    [
                        'week' => '3',
                        'weight' => '2',
                    ],
                    [
                        'week' => '4',
                        'weight' => '2.5',
                    ],
                    [
                        'week' => '5',
                        'weight' => '3',
                    ],
                    [
                        'week' => '6',
                        'weight' => '3.5',
                    ]
                ]
            ],
            'health_records' => [
                [
                    'title' => 'BCG Vaccine',
                    'date' => 'December 30, 2025',
                    'description' => 'BCG Vaccine given to Emma',
                ],
                [
                    'title' => 'DPT Vaccine',
                    'date' => 'December 30, 2025',
                    'description' => 'DPT Vaccine given to Emma',
                ],
                [
                    'title' => 'Hepatitis B Vaccine',
                    'date' => 'December 30, 2025',
                    'description' => 'Hepatitis B Vaccine given to Emma',
                ],
                [
                    'title' => 'OPV Vaccine',
                    'date' => 'December 30, 2025',
                    'description' => 'OPV Vaccine given to Emma',
                ],
                [
                    'title' => 'Measles Vaccine',
                    'date' => 'December 30, 2025',
                    'description' => 'Measles Vaccine given to Emma',
                ]
            ],
        ];
    }

    public function getBabyLiveCam()
    {
        return [
            'name' => 'Baby Emma',
            'room_name' => 'Room 1',
            'day' => 'Day 5',
            'last_seen' => 'Last Checked 10 minutes ago',
            'live_cam_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
        ];
    }

    public function getBabyHealthData($userId)
    {
        return [
            'temperature' => '98.6℃',
            'heart_rate' => '120 bpm',
            'respiratory_rate' => '12 bpm',
            'weight' => '1 kg',
        ];
    }
    public function getMotherHealthData($userId)
    {
        return [
            'blood_pressure' => '120/80 mmHg',
            'heart_rate' => '120 bpm',
            'oxygen_level' => '98%',
            'weight' => '70 kg',
            'sleep_tracking' => [
                'day_sleep' => '8.2h',
                'weekly_average' => '7.5h',
            ]
        ];
    }

    public function getTodaysSchedule($userId)
    {
        return [
            [
                'time' => '10:00',
                'time_standard' => 'AM',
                'activity' => 'Doctor Consultation',
                'description' => 'Iron Suppliment 1 tablets',
            ],
            [
                'time' => '12:00',
                'time_standard' => 'PM',
                'activity' => 'Doctor Consultation',
                'description' => 'Iron Suppliment 1 tablets',
            ],
            [
                'time' => '01:00',
                'time_standard' => 'PM',
                'activity' => 'Doctor Consultation',
                'description' => 'Iron Suppliment 1 tablets',
            ],
            [
                'time' => '03:00',
                'time_standard' => 'PM',
                'activity' => 'Doctor Consultation',
                'description' => 'Iron Suppliment 1 tablets',
            ],
        ];
    }
}

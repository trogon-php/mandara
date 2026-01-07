<?php

namespace App\Services\App;

use App\Services\App\AppBaseService;
use App\Services\Estore\EstoreOrderAssignmentService;
use Illuminate\Http\Response;

class EstoreDeliveryService extends AppBaseService
{
    public function __construct(
        protected EstoreOrderAssignmentService $assignmentService
    ) {}

    /**
     * Get dashboard data for delivery staff
     */
    public function getDashboard(int $staffId): array
    {
        $newOrdersCount = $this->assignmentService->getNewOrdersCount($staffId);
        $ordersCountText = $newOrdersCount > 0 ? "$newOrdersCount new orders" : "No new orders";
        return [
            'status' => true,
            'data' => [
                'attendance_log' => [
                    'punch_in' => [
                        'time' => '08:00 AM',
                        'text' => 'Punch in',
                    ],
                    'punch_out' => [
                        'time' => '--:-- --',
                        'text' => 'Punch out',
                    ],
                ],
                'orders_count_text' => $ordersCountText,
                'my_tasks' => 
                    [
                        [
                            'time' => '08:00',
                            'time_standard' => 'AM',
                            'title' => 'Order 123456',
                            'description' => 'Delivery to Room 101',
                            'status' => 'pending'
                        ],
                        [
                            'time' => '08:00',
                            'time_standard' => 'AM',
                            'title' => 'Order 123456',
                            'description' => 'Delivery to Room 101',
                            'status' => 'pending'
                        ]
                    ]
            ],
            'http_code' => Response::HTTP_OK
        ];
    }

    /**
     * Get orders list (pending or delivered)
     */
    public function getOrders(int $staffId, ?string $status = null, int $perPage = 15): array
    {
        if ($status === 'delivered') {
            $orders = $this->assignmentService->getDeliveredAssignments($staffId, $perPage);
        } else {
            $orders = $this->assignmentService->getPendingAssignments($staffId, $perPage);
        }

        return [
            'status' => true,
            'data' => $orders,
            'http_code' => Response::HTTP_OK
        ];
    }

    /**
     * Get order details
     */
    public function getOrderDetails(int $assignmentId, int $staffId): array
    {
        return $this->assignmentService->getAssignmentDetails($assignmentId, $staffId);
    }

    /**
     * Start delivery
     */
    public function startDelivery(int $assignmentId, int $staffId): array
    {
        return $this->assignmentService->startDelivery($assignmentId, $staffId);
    }

    /**
     * Update order status
     */
    public function updateOrderStatus(int $assignmentId, int $staffId, string $status, ?string $remarks = null): array
    {
        $allowedStatuses = ['assigned', 'accepted', 'out_for_delivery', 'delivered', 'failed'];
        
        if (!in_array($status, $allowedStatuses)) {
            return [
                'status' => false,
                'message' => 'Invalid status',
                'http_code' => Response::HTTP_BAD_REQUEST
            ];
        }

        return $this->assignmentService->updateStatus($assignmentId, $staffId, $status, $remarks);
    }

    /**
     * Mark order as delivered
     */
    public function markDelivered(int $assignmentId, int $staffId, ?string $remarks = null): array
    {
        return $this->assignmentService->markDelivered($assignmentId, $staffId, $remarks);
    }
}
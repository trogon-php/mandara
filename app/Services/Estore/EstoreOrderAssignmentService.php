<?php

namespace App\Services\Estore;

use App\Models\EstoreOrderAssignment;
use App\Services\Core\BaseService;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class EstoreOrderAssignmentService extends BaseService
{
    protected string $modelClass = EstoreOrderAssignment::class;

    public function getFilterConfig(): array
    {
        return [
            'status' => [
                'type' => 'select',
                'label' => 'Status',
                'col' => 3,
                'options' => [
                    'assigned' => 'Assigned',
                    'accepted' => 'Accepted',
                    'out_for_delivery' => 'Out for Delivery',
                    'delivered' => 'Delivered',
                    'failed' => 'Failed',
                    'cancelled' => 'Cancelled',
                ],
            ],
        ];
    }

    public function getSearchFieldsConfig(): array
    {
        return [
            'order_id' => 'Order ID',
        ];
    }

    public function getDefaultSearchFields(): array
    {
        return ['order_id'];
    }

    public function getDefaultSorting(): array
    {
        return ['field' => 'assigned_at', 'direction' => 'desc'];
    }

    /**
     * Assign order to delivery staff
     */
    // public function assignOrder(int $orderId, int $deliveryStaffId, int $assignedById, ?string $deliveryRoom = null): array
    // {
    //     return DB::transaction(function () use ($orderId, $deliveryStaffId, $assignedById, $deliveryRoom) {
    //         // Check if order already has active assignment
    //         $existingAssignment = $this->model
    //             ->where('order_id', $orderId)
    //             ->whereIn('status', ['assigned', 'accepted', 'out_for_delivery'])
    //             ->first();
              

    //         if ($existingAssignment) {
    //             return [
    //                 'status' => false,
    //                 'message' => 'Order is already assigned to another delivery staff',
    //                 'http_code' => Response::HTTP_BAD_REQUEST
    //             ];
    //         }

    //         // Create assignment
    //         $assignment = $this->model->create([
    //             'order_id' => $orderId,
    //             'delivery_staff_id' => $deliveryStaffId,
    //             'assigned_by' => $assignedById,
    //             'assigned_at' => now(),
    //             'status' => 'assigned',
    //             'delivery_room' => $deliveryRoom,
    //         ]);

    //         return [
    //             'status' => true,
    //             'message' => 'Order assigned successfully',
    //             'data' => $assignment->load(['order', 'deliveryStaff', 'assignedBy']),
    //             'http_code' => Response::HTTP_OK
    //         ];
    //     });
    // }

    public function assignOrder(int $orderId,int $deliveryStaffId,int $assignedById,?string $deliveryRoom = null): array {
        return DB::transaction(function () use ($orderId,$deliveryStaffId,$assignedById,$deliveryRoom) 
        {
    
            // Get existing active assignment (ONLY ONE should exist)
            $existingAssignment = $this->model
                ->where('order_id', $orderId)
                ->whereIn('status', ['assigned', 'accepted', 'out_for_delivery'])
                ->lockForUpdate()
                ->first();
              
    
            // CASE 1: Order already assigned
            if ($existingAssignment) {
                // SAME staff → ERROR
                if ((int) $existingAssignment->delivery_staff_id === $deliveryStaffId) {
                   
                    return [
                        'status' => false,
                        'message' => 'Order is already assigned to the same delivery staff',
                        'http_code' => Response::HTTP_BAD_REQUEST
                    ];
                }
    
                // DIFFERENT staff → REASSIGN
                $existingAssignment->update([
                    'delivery_staff_id' => $deliveryStaffId,
                    'assigned_by'       => $assignedById,
                    'assigned_at'       => now(),
                    'delivery_room'     => $deliveryRoom,
                    'status'            => 'assigned',
                ]);
    
                return [
                    'status' => true,
                    'message' => 'Order reassigned successfully',
                    'data' => $existingAssignment->load([
                        'order',
                        'deliveryStaff',
                        'assignedBy'
                    ]),
                    'http_code' => Response::HTTP_OK
                ];
            }
    
            // CASE 2: No assignment exists → ASSIGN NEW
            $assignment = $this->model->create([
                'order_id'           => $orderId,
                'delivery_staff_id'  => $deliveryStaffId,
                'assigned_by'        => $assignedById,
                'assigned_at'        => now(),
                'status'             => 'assigned',
                'delivery_room'      => $deliveryRoom,
            ]);
    
            return [
                'status' => true,
                'message' => 'Order assigned successfully',
                'data' => $assignment->load([
                    'order',
                    'deliveryStaff',
                    'assignedBy'
                ]),
                'http_code' => Response::HTTP_OK
            ];
        });
    }
    

    /**
     * Get assignments for a delivery staff
     */
    public function getStaffAssignments(int $staffId, ?string $status = null, int $perPage = 15)
    {
        $query = $this->model
            ->where('delivery_staff_id', $staffId)
            ->with(['order.user', 'order.items.product', 'assignedBy']);

        if ($status) {
            $query->where('status', $status);
        }

        return $query->orderBy('assigned_at', 'desc')->paginate($perPage);
    }

    /**
     * Get pending assignments for a delivery staff
     */
    public function getPendingAssignments(int $staffId, int $perPage = 15)
    {
        return $this->model
            ->where('delivery_staff_id', $staffId)
            ->whereIn('status', ['assigned', 'accepted', 'out_for_delivery'])
            ->with(['order.user', 'order.items.product'])
            ->orderBy('assigned_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Get delivered assignments for a delivery staff
     */
    public function getDeliveredAssignments(int $staffId, int $perPage = 15)
    {
        return $this->model
            ->where('delivery_staff_id', $staffId)
            ->where('status', 'delivered')
            ->with(['order.user', 'order.items.product'])
            ->orderBy('delivered_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Start delivery (update status to out_for_delivery)
     */
    public function startDelivery(int $assignmentId, int $staffId): array
    {
        $assignment = $this->model
            ->where('id', $assignmentId)
            ->where('delivery_staff_id', $staffId)
            ->first();

        if (!$assignment) {
            return [
                'status' => false,
                'message' => 'Assignment not found',
                'http_code' => Response::HTTP_NOT_FOUND
            ];
        }

        if (!in_array($assignment->status, ['assigned', 'accepted'])) {
            return [
                'status' => false,
                'message' => 'Cannot start delivery. Invalid status.',
                'http_code' => Response::HTTP_BAD_REQUEST
            ];
        }

        $assignment->update([
            'status' => 'out_for_delivery',
            'started_at' => now(),
        ]);

        return [
            'status' => true,
            'message' => 'Delivery started successfully',
            'data' => $assignment->load(['order.user', 'order.items.product']),
            'http_code' => Response::HTTP_OK
        ];
    }

    /**
     * Update assignment status
     */
    public function updateStatus(int $assignmentId, int $staffId, string $status, ?string $remarks = null): array
    {
        $assignment = $this->model
            ->where('id', $assignmentId)
            ->where('delivery_staff_id', $staffId)
            ->first();

        if (!$assignment) {
            return [
                'status' => false,
                'message' => 'Assignment not found',
                'http_code' => Response::HTTP_NOT_FOUND
            ];
        }

        $updateData = [
            'status' => $status,
        ];

        if ($remarks) {
            $updateData['delivery_remarks'] = $remarks;
        }

        // Set appropriate timestamp based on status
        if ($status === 'delivered') {
            $updateData['delivered_at'] = now();
        } elseif ($status === 'failed') {
            $updateData['failed_at'] = now();
        } elseif ($status === 'accepted') {
            $updateData['accepted_at'] = now();
        }

        $assignment->update($updateData);

        // If delivered, update the order status as well
        if ($status === 'delivered') {
            $assignment->order->update([
                'order_status' => 'delivered',
                'delivered_at' => now(),
            ]);
        }

        return [
            'status' => true,
            'message' => 'Status updated successfully',
            'data' => $assignment->load(['order.user', 'order.items.product']),
            'http_code' => Response::HTTP_OK
        ];
    }

    /**
     * Mark as delivered
     */
    public function markDelivered(int $assignmentId, int $staffId, ?string $remarks = null): array
    {
        return $this->updateStatus($assignmentId, $staffId, 'delivered', $remarks);
    }

    /**
     * Get assignment details
     */
    public function getAssignmentDetails(int $assignmentId, int $staffId): array
    {
        $assignment = $this->model
            ->where('id', $assignmentId)
            ->where('delivery_staff_id', $staffId)
            ->with(['order.user', 'order.items.product', 'assignedBy'])
            ->first();

        if (!$assignment) {
            return [
                'status' => false,
                'message' => 'Assignment not found',
                'http_code' => Response::HTTP_NOT_FOUND
            ];
        }

        return [
            'status' => true,
            'data' => $assignment,
            'http_code' => Response::HTTP_OK
        ];
    }

    /**
     * Get new orders count for delivery staff
     */
    public function getNewOrdersCount(int $staffId): int
    {
        return $this->model
            ->where('delivery_staff_id', $staffId)
            ->where('status', 'assigned')
            ->count();
    }
}

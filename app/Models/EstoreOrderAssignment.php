<?php

namespace App\Models;

use App\Models\BaseModel;

class EstoreOrderAssignment extends BaseModel
{
    
    protected $casts = [
        'order_id' => 'integer',
        'delivery_staff_id' => 'integer',
        'assigned_by' => 'integer',
        'assigned_at' => 'datetime',
        'accepted_at' => 'datetime',
        'started_at' => 'datetime',
        'delivered_at' => 'datetime',
        'failed_at' => 'datetime',
    ];

    // Relationships
    public function order()
    {
        return $this->belongsTo(EstoreOrder::class, 'order_id');
    }

    public function deliveryStaff()
    {
        return $this->belongsTo(User::class, 'delivery_staff_id');
    }

    public function assignedBy()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    // Scopes
    public function scopeAssigned($query)
    {
        return $query->where('status', 'assigned');
    }

    public function scopeOutForDelivery($query)
    {
        return $query->where('status', 'out_for_delivery');
    }

    public function scopeDelivered($query)
    {
        return $query->where('status', 'delivered');
    }

    public function scopePending($query)
    {
        return $query->whereIn('status', ['assigned', 'accepted', 'out_for_delivery']);
    }

    public function scopeForStaff($query, int $staffId)
    {
        return $query->where('delivery_staff_id', $staffId);
    }
}

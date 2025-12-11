<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use App\Models\Traits\HasFileUrls;

abstract class BaseModel extends Model
{
    use SoftDeletes, HasFileUrls, HasFactory;

    /**
     * Allow mass assignment by default.
     * Child models can override if they want stricter control.
     */
    protected $guarded = [];

    /**
     * Common casts for all tables.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'created_by' => 'integer',
        'updated_by' => 'integer',
        'deleted_by' => 'integer',
        // Laravel automatically casts timestamps to Carbon
    ];

    /**
     * Boot hooks for audit fields and activity logging.
     */
    protected static function booted()
    {
        // Set created_by and updated_by on create
        static::creating(function ($model) {
            if (Auth::check()) {
                $model->created_by = $model->created_by ?? Auth::id();
                $model->updated_by = $model->updated_by ?? Auth::id();
            }

            if (\Schema::hasColumn($model->getTable(), 'sort_order') && empty($model->sort_order)) {
                $model->sort_order = (static::max('sort_order') ?? 0) + 1;
            }
        });

        // Update updated_by on update
        static::updating(function ($model) {
            if (Auth::check()) {
                $model->updated_by = Auth::id();
            }
        });

        // Soft delete → set deleted_by
        static::deleting(function ($model) {
            if (Auth::check() && ! $model->isForceDeleting()) {
                $model->deleted_by = Auth::id();
                $model->saveQuietly();
            }
        });

        // Activity logging
        static::created(fn ($model) => static::logActivity($model, 'created'));
        static::updated(fn ($model) => static::logActivity($model, 'updated'));
        static::deleted(fn ($model) => ! $model->isForceDeleting() && static::logActivity($model, 'deleted'));
        static::restored(fn ($model) => static::logActivity($model, 'restored'));
    }

    /**
     * Relationships to track user actions.
     */
    public function creator()
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(\App\Models\User::class, 'updated_by');
    }

    public function deleter()
    {
        return $this->belongsTo(\App\Models\User::class, 'deleted_by');
    }

    /**
     * Common query scopes.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 1)->orWhere('status', 'active');
    }

    public function scopeSorted($query)
    {
        $model = $query->getModel();
        $table = $model->getTable();

        static $cache = [];

        if (!isset($cache[$table])) {
            $cache[$table] = Schema::hasColumn($table, 'sort_order');
        }

        if ($cache[$table]) {
            return $query->orderBy('sort_order', 'asc')
                        ->orderBy('created_at', 'desc');
        }

        return $query->orderBy('created_at', 'desc');
    }

    /**
     * Centralized file fields access.
     *
     * @return array<string, array>
     */
    public function getFileFields(): array
    {
        return property_exists($this, 'fileFields') ? $this->fileFields : [];
    }

    /**
     * Activity logging handler.
     */
    protected static function logActivity($model, string $action): void
    {
        if (!Auth::check()) {
            return;
        }

        $changes = null;
        if ($action === 'updated') {
            $changes = $model->getDirty();
            unset($changes['updated_at'], $changes['updated_by']);
        }

        try {
            \App\Models\ActivityLog::create([
                'user_id'    => Auth::id(),
                'model_type' => get_class($model),
                'model_id'   => $model->id,
                'action'     => $action,
                'changes'    => $changes ? json_encode($changes) : null,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to log activity: ' . $e->getMessage());
        }
    }

    /**
     * Get activity logs for this model.
     */
    public function activityLogs()
    {
        return $this->morphMany(\App\Models\ActivityLog::class, 'model');
    }
}

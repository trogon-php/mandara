<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\Traits\HasFileUrls;
use App\Models\Traits\HasRoles;
use Tymon\JWTAuth\Contracts\JWTSubject;

abstract class BaseAuthModel extends Authenticatable implements JWTSubject
{
    use SoftDeletes, HasFileUrls, HasFactory, Notifiable, HasRoles;

    protected $guarded = [];

    protected $casts = [
        'created_by' => 'integer',
        'updated_by' => 'integer',
        'deleted_by' => 'integer',
    ];

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     */
    public function getJWTCustomClaims()
    {
        return [
        ];
    }

    protected static function booted()
    {
        static::creating(function ($model) {
            if (Auth::check()) {
                $model->created_by = $model->created_by ?? Auth::id();
                $model->updated_by = $model->updated_by ?? Auth::id();
            }
        });

        static::updating(function ($model) {
            if (Auth::check()) {
                $model->updated_by = Auth::id();
            }
        });

        static::deleting(function ($model) {
            if (Auth::check() && ! $model->isForceDeleting()) {
                $model->deleted_by = Auth::id();
                $model->saveQuietly();
            }
        });

        static::created(fn ($model) => static::logActivity($model, 'created'));
        static::updated(fn ($model) => static::logActivity($model, 'updated'));
        static::deleted(fn ($model) => ! $model->isForceDeleting() && static::logActivity($model, 'deleted'));
        static::restored(fn ($model) => static::logActivity($model, 'restored'));
    }

    public function creator()
    {
        return $this->belongsTo(\App\Modules\Users\Models\User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(\App\Modules\Users\Models\User::class, 'updated_by');
    }

    public function deleter()
    {
        return $this->belongsTo(\App\Modules\Users\Models\User::class, 'deleted_by');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    public function scopeSorted($query)
    {
        return $query->orderBy('sort_order', 'asc')
                     ->orderBy('created_at', 'desc');
    }

    public function getFileFields(): array
    {
        return property_exists($this, 'fileFields') ? $this->fileFields : [];
    }

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

    public function activityLogs()
    {
        return $this->morphMany(\App\Modules\Logs\Models\ActivityLog::class, 'model');
    }
}

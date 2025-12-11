<?php

namespace App\Services\Logs;

use App\Models\ActivityLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class ActivityLogService
{
    /**
     * Log a custom activity.
     */
    public static function log(
        string $action,
        Model $model = null,
        array $changes = null,
        string $description = null
    ): ActivityLog {
        return ActivityLog::create([
            'user_id'    => Auth::id(),
            'model_type' => $model ? get_class($model) : null,
            'model_id'   => $model ? $model->id : null,
            'action'     => $action,
            'changes'    => $changes ? json_encode($changes) : null,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
        ]);
    }

    /**
     * Log a custom action without a model.
     */
    public static function logCustomAction(string $action, array $context = []): ActivityLog
    {
        return static::log($action, null, $context);
    }

    /**
     * Get activity logs for a specific model.
     */
    public static function getModelLogs(Model $model, int $limit = 50): \Illuminate\Database\Eloquent\Collection
    {
        return ActivityLog::where('model_type', get_class($model))
            ->where('model_id', $model->id)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get activity logs for a specific user.
     */
    public static function getUserLogs(int $userId, int $limit = 50): \Illuminate\Database\Eloquent\Collection
    {
        return ActivityLog::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get activity logs for a specific action.
     */
    public static function getActionLogs(string $action, int $limit = 50): \Illuminate\Database\Eloquent\Collection
    {
        return ActivityLog::where('action', $action)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get recent activity across all models.
     */
    public static function getRecentActivity(int $limit = 100): \Illuminate\Database\Eloquent\Collection
    {
        return ActivityLog::with(['user', 'model'])
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Clean up old activity logs.
     */
    public static function cleanupOldLogs(int $daysOld = 365): int
    {
        $cutoffDate = now()->subDays($daysOld);
        
        return ActivityLog::where('created_at', '<', $cutoffDate)->delete();
    }

    /**
     * Export activity logs to CSV.
     */
    public static function exportToCsv(\Illuminate\Database\Eloquent\Collection $logs): string
    {
        $headers = ['ID', 'User', 'Action', 'Model Type', 'Model ID', 'Changes', 'IP Address', 'User Agent', 'Created At'];
        
        $csv = fopen('php://temp', 'r+');
        fputcsv($csv, $headers);
        
        foreach ($logs as $log) {
            fputcsv($csv, [
                $log->id,
                $log->user?->name ?? 'Unknown',
                $log->action,
                $log->model_type,
                $log->model_id,
                $log->changes ? json_encode($log->changes) : '',
                $log->ip_address,
                $log->user_agent,
                $log->created_at,
            ]);
        }
        
        rewind($csv);
        $content = stream_get_contents($csv);
        fclose($csv);
        
        return $content;
    }
}

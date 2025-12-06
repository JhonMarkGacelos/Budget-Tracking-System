<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    /**
     * Display all audit logs
     */
    public function index(Request $request)
    {
        $user = auth()->user();

        // Only admin can view all logs
        if (!$user->isAdmin()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $query = AuditLog::with('user');

        // Filter by action
        if ($request->query('action')) {
            $query = $query->where('action', $request->query('action'));
        }

        // Filter by model type
        if ($request->query('model_type')) {
            $query = $query->where('model_type', $request->query('model_type'));
        }

        // Filter by user
        if ($request->query('user_id')) {
            $query = $query->where('user_id', $request->query('user_id'));
        }

        // Date range filter
        if ($request->query('start_date')) {
            $query = $query->whereDate('created_at', '>=', $request->query('start_date'));
        }

        if ($request->query('end_date')) {
            $query = $query->whereDate('created_at', '<=', $request->query('end_date'));
        }

        $logs = $query->orderBy('created_at', 'desc')->paginate(20);

        return response()->json($logs);
    }

    /**
     * Display audit logs for a specific model
     */
    public function show($modelType, $modelId)
    {
        $user = auth()->user();

        // Only admin can view
        if (!$user->isAdmin()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $logs = AuditLog::where('model_type', $modelType)
            ->where('model_id', $modelId)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'model_type' => $modelType,
            'model_id' => $modelId,
            'logs' => $logs,
        ]);
    }

    /**
     * Get activity summary
     */
    public function summary(Request $request)
    {
        $user = auth()->user();

        // Only admin can view
        if (!$user->isAdmin()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $startDate = $request->query('start_date', now()->subMonth()->toDateString());
        $endDate = $request->query('end_date', now()->toDateString());

        $summary = [
            'total_actions' => AuditLog::whereBetween('created_at', [$startDate, $endDate])->count(),
            'actions_by_type' => AuditLog::whereBetween('created_at', [$startDate, $endDate])
                ->groupBy('action')
                ->selectRaw('action, count(*) as count')
                ->get(),
            'actions_by_model' => AuditLog::whereBetween('created_at', [$startDate, $endDate])
                ->groupBy('model_type')
                ->selectRaw('model_type, count(*) as count')
                ->get(),
            'top_users' => AuditLog::whereBetween('created_at', [$startDate, $endDate])
                ->with('user')
                ->groupBy('user_id')
                ->selectRaw('user_id, count(*) as count')
                ->orderByDesc('count')
                ->limit(10)
                ->get(),
        ];

        return response()->json($summary);
    }
}

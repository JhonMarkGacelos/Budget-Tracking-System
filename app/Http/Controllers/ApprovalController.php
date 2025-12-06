<?php

namespace App\Http\Controllers;

use App\Models\BudgetRequest;
use App\Models\BudgetSubmission;
use App\Models\LiquidationReport;
use Illuminate\Http\Request;

class ApprovalController extends Controller
{
    /**
     * Get pending approvals for the authenticated user.
     */
    public function getPendingApprovals()
    {
        $user = auth()->user();
        $approvals = [];

        if ($user->isAdmin()) {
            // Admin can see all pending and department-approved requests
            $approvals['budget_requests'] = BudgetRequest::whereIn('status', ['pending', 'department_approved'])
                ->with(['user', 'department'])
                ->orderBy('created_at', 'desc')
                ->get();

            // Admin can see department-reviewed submissions
            $approvals['submissions'] = BudgetSubmission::where('status', 'department_reviewed')
                ->with(['user', 'budgetRequest'])
                ->orderBy('created_at', 'desc')
                ->get();

            // Admin can see department-reviewed liquidation reports
            $approvals['liquidation_reports'] = LiquidationReport::where('status', 'department_reviewed')
                ->with(['user', 'budgetRequest'])
                ->orderBy('created_at', 'desc')
                ->get();
        } elseif ($user->isDepartment()) {
            // Department head can see pending requests from their department
            $approvals['budget_requests'] = BudgetRequest::where('department_id', $user->department_id)
                ->where('status', 'pending')
                ->with(['user', 'department'])
                ->orderBy('created_at', 'desc')
                ->get();

            // Department head can see pending submissions from their department
            $approvals['submissions'] = BudgetSubmission::whereHas('budgetRequest', function ($query) use ($user) {
                $query->where('department_id', $user->department_id);
            })->where('status', 'pending')
                ->with(['user', 'budgetRequest'])
                ->orderBy('created_at', 'desc')
                ->get();

            // Department head can see pending liquidation reports from their department
            $approvals['liquidation_reports'] = LiquidationReport::whereHas('budgetRequest', function ($query) use ($user) {
                $query->where('department_id', $user->department_id);
            })->where('status', 'pending')
                ->with(['user', 'budgetRequest'])
                ->orderBy('created_at', 'desc')
                ->get();
        } else {
            return response()->json(['message' => 'Faculty users do not have approvals'], 403);
        }

        return response()->json($approvals);
    }

    /**
     * Get approval statistics for dashboard.
     */
    public function getStatistics()
    {
        $user = auth()->user();
        $stats = [];

        if ($user->isAdmin()) {
            $stats['total_budget_requests'] = BudgetRequest::count();
            $stats['pending_budget_requests'] = BudgetRequest::where('status', 'pending')->count();
            $stats['approved_budget_requests'] = BudgetRequest::where('status', 'admin_approved')->count();
            $stats['rejected_budget_requests'] = BudgetRequest::whereIn('status', ['admin_rejected', 'department_rejected'])->count();

            $stats['total_submissions'] = BudgetSubmission::count();
            $stats['pending_submissions'] = BudgetSubmission::where('status', 'pending')->count();
            $stats['reviewed_submissions'] = BudgetSubmission::where('status', 'admin_reviewed')->count();

            $stats['total_liquidation_reports'] = LiquidationReport::count();
            $stats['pending_liquidation_reports'] = LiquidationReport::where('status', 'pending')->count();
            $stats['reviewed_liquidation_reports'] = LiquidationReport::where('status', 'admin_reviewed')->count();

            $stats['total_approved_amount'] = BudgetRequest::where('status', 'admin_approved')->sum('amount');
        } elseif ($user->isDepartment()) {
            $stats['total_budget_requests'] = BudgetRequest::where('department_id', $user->department_id)->count();
            $stats['pending_budget_requests'] = BudgetRequest::where('department_id', $user->department_id)
                ->where('status', 'pending')->count();
            $stats['approved_budget_requests'] = BudgetRequest::where('department_id', $user->department_id)
                ->where('status', 'department_approved')->count();
            $stats['rejected_budget_requests'] = BudgetRequest::where('department_id', $user->department_id)
                ->where('status', 'department_rejected')->count();

            $stats['total_pending_approvals'] = BudgetRequest::where('department_id', $user->department_id)
                ->where('status', 'pending')->count();

            $stats['total_approved_amount'] = BudgetRequest::where('department_id', $user->department_id)
                ->where('status', 'department_approved')->sum('amount');
        } else {
            return response()->json(['message' => 'Faculty users do not have statistics'], 403);
        }

        return response()->json($stats);
    }
}

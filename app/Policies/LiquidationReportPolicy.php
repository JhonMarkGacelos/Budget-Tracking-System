<?php

namespace App\Policies;

use App\Models\LiquidationReport;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class LiquidationReportPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Admin, department heads, and faculty can view reports
        return $user->isAdmin() || $user->isDepartment() || $user->isFaculty();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, LiquidationReport $liquidationReport): bool
    {
        // Admin can view all reports
        if ($user->isAdmin()) {
            return true;
        }

        // Department head can view reports from their department
        if ($user->isDepartment() && $user->department_id === $liquidationReport->budgetRequest->department_id) {
            return true;
        }

        // Faculty can only view their own reports
        if ($user->isFaculty() && $user->id === $liquidationReport->user_id) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Only faculty can create liquidation reports
        return $user->isFaculty();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, LiquidationReport $liquidationReport): bool
    {
        // Faculty can update their own pending reports
        if ($user->isFaculty() && $user->id === $liquidationReport->user_id && $liquidationReport->isPending()) {
            return true;
        }

        // Department head can review reports from their department
        if ($user->isDepartment() && $user->department_id === $liquidationReport->budgetRequest->department_id) {
            return true;
        }

        // Admin can always update
        if ($user->isAdmin()) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, LiquidationReport $liquidationReport): bool
    {
        // Faculty can delete their own pending reports
        if ($user->isFaculty() && $user->id === $liquidationReport->user_id && $liquidationReport->isPending()) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, LiquidationReport $liquidationReport): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, LiquidationReport $liquidationReport): bool
    {
        return false;
    }

    /**
     * Determine whether the user can review at department level.
     */
    public function reviewDepartment(User $user, LiquidationReport $liquidationReport): bool
    {
        return $user->isDepartment() && 
               $user->department_id === $liquidationReport->budgetRequest->department_id && 
               $liquidationReport->isPending();
    }

    /**
     * Determine whether the user can review at admin level.
     */
    public function reviewAdmin(User $user, LiquidationReport $liquidationReport): bool
    {
        return $user->isAdmin() && $liquidationReport->isDepartmentReviewed();
    }
}

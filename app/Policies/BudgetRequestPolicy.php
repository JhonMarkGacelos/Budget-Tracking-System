<?php

namespace App\Policies;

use App\Models\BudgetRequest;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class BudgetRequestPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Admin can see all budget requests
        if ($user->isAdmin()) {
            return true;
        }

        // Department heads can see requests from their department
        if ($user->isDepartment()) {
            return true;
        }

        // Faculty can see their own requests
        if ($user->isFaculty()) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, BudgetRequest $budgetRequest): bool
    {
        // Admin can view all requests
        if ($user->isAdmin()) {
            return true;
        }

        // Department head can view requests from their department
        if ($user->isDepartment() && $user->department_id === $budgetRequest->department_id) {
            return true;
        }

        // Faculty can only view their own requests
        if ($user->isFaculty() && $user->id === $budgetRequest->user_id) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Only faculty can create budget requests
        return $user->isFaculty();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, BudgetRequest $budgetRequest): bool
    {
        // Faculty can only update their own pending requests
        if ($user->isFaculty() && $user->id === $budgetRequest->user_id && $budgetRequest->isPending()) {
            return true;
        }

        // Department heads can update requests in their department (for approval/rejection)
        if ($user->isDepartment() && $user->department_id === $budgetRequest->department_id) {
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
    public function delete(User $user, BudgetRequest $budgetRequest): bool
    {
        // Only faculty can delete their own pending requests
        if ($user->isFaculty() && $user->id === $budgetRequest->user_id && $budgetRequest->isPending()) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, BudgetRequest $budgetRequest): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, BudgetRequest $budgetRequest): bool
    {
        return false;
    }

    /**
     * Determine whether the user can approve the model.
     */
    public function approveDepartment(User $user, BudgetRequest $budgetRequest): bool
    {
        // Department head can approve requests from their department
        return $user->isDepartment() && 
               $user->department_id === $budgetRequest->department_id && 
               $budgetRequest->isPending();
    }

    /**
     * Determine whether the user can reject at department level.
     */
    public function rejectDepartment(User $user, BudgetRequest $budgetRequest): bool
    {
        return $this->approveDepartment($user, $budgetRequest);
    }

    /**
     * Determine whether the user can approve at admin level.
     */
    public function approveAdmin(User $user, BudgetRequest $budgetRequest): bool
    {
        return $user->isAdmin() && $budgetRequest->isDepartmentApproved();
    }

    /**
     * Determine whether the user can reject at admin level.
     */
    public function rejectAdmin(User $user, BudgetRequest $budgetRequest): bool
    {
        return $user->isAdmin() && ($budgetRequest->isPending() || $budgetRequest->isDepartmentApproved());
    }
}

<?php

namespace App\Policies;

use App\Models\BudgetSubmission;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class BudgetSubmissionPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Admin, department heads, and faculty can view submissions
        return $user->isAdmin() || $user->isDepartment() || $user->isFaculty();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, BudgetSubmission $budgetSubmission): bool
    {
        // Admin can view all submissions
        if ($user->isAdmin()) {
            return true;
        }

        // Department head can view submissions from their department
        if ($user->isDepartment() && $user->department_id === $budgetSubmission->budgetRequest->department_id) {
            return true;
        }

        // Faculty can only view their own submissions
        if ($user->isFaculty() && $user->id === $budgetSubmission->user_id) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Only faculty can create submissions
        return $user->isFaculty();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, BudgetSubmission $budgetSubmission): bool
    {
        // Faculty can update their own pending submissions
        if ($user->isFaculty() && $user->id === $budgetSubmission->user_id && $budgetSubmission->isPending()) {
            return true;
        }

        // Department head can review submissions from their department
        if ($user->isDepartment() && $user->department_id === $budgetSubmission->budgetRequest->department_id) {
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
    public function delete(User $user, BudgetSubmission $budgetSubmission): bool
    {
        // Faculty can delete their own pending submissions
        if ($user->isFaculty() && $user->id === $budgetSubmission->user_id && $budgetSubmission->isPending()) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, BudgetSubmission $budgetSubmission): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, BudgetSubmission $budgetSubmission): bool
    {
        return false;
    }

    /**
     * Determine whether the user can review at department level.
     */
    public function reviewDepartment(User $user, BudgetSubmission $budgetSubmission): bool
    {
        return $user->isDepartment() && 
               $user->department_id === $budgetSubmission->budgetRequest->department_id && 
               $budgetSubmission->isPending();
    }

    /**
     * Determine whether the user can review at admin level.
     */
    public function reviewAdmin(User $user, BudgetSubmission $budgetSubmission): bool
    {
        return $user->isAdmin() && $budgetSubmission->isDepartmentReviewed();
    }
}

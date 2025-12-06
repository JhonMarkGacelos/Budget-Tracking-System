<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait Filterable
{
    /**
     * Filter by status
     */
    public function scopeFilterByStatus(Builder $query, ?string $status): Builder
    {
        if (!$status) {
            return $query;
        }

        return $query->where('status', $status);
    }

    /**
     * Filter by date range
     */
    public function scopeFilterByDateRange(Builder $query, ?string $startDate, ?string $endDate): Builder
    {
        if ($startDate) {
            $query = $query->whereDate('created_at', '>=', $startDate);
        }

        if ($endDate) {
            $query = $query->whereDate('created_at', '<=', $endDate);
        }

        return $query;
    }

    /**
     * Filter by department
     */
    public function scopeFilterByDepartment(Builder $query, ?int $departmentId): Builder
    {
        if (!$departmentId) {
            return $query;
        }

        return $query->where('department_id', $departmentId);
    }

    /**
     * Search by title and description
     */
    public function scopeSearch(Builder $query, ?string $searchTerm): Builder
    {
        if (!$searchTerm) {
            return $query;
        }

        return $query->where('title', 'like', "%{$searchTerm}%")
            ->orWhere('description', 'like', "%{$searchTerm}%");
    }

    /**
     * Filter by amount range
     */
    public function scopeFilterByAmountRange(Builder $query, ?float $minAmount, ?float $maxAmount): Builder
    {
        if ($minAmount !== null) {
            $query = $query->where('amount', '>=', $minAmount);
        }

        if ($maxAmount !== null) {
            $query = $query->where('amount', '<=', $maxAmount);
        }

        return $query;
    }
}

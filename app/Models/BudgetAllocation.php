<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BudgetAllocation extends Model
{
    protected $fillable = [
        'department_id',
        'fiscal_year',
        'allocated_amount',
        'spent_amount',
        'remaining_balance',
        'status',
        'notes',
    ];

    protected $casts = [
        'allocated_amount' => 'decimal:2',
        'spent_amount' => 'decimal:2',
        'remaining_balance' => 'decimal:2',
    ];

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function budgetRequests()
    {
        return $this->hasMany(BudgetRequest::class);
    }

    /**
     * Update spending and balance
     */
    public function updateSpending()
    {
        $approved = $this->department
            ->budgetRequests()
            ->where('status', 'admin_approved')
            ->whereYear('created_at', $this->fiscal_year)
            ->sum('amount');

        $this->spent_amount = $approved;
        $this->remaining_balance = $this->allocated_amount - $approved;
        $this->save();
    }

    /**
     * Get spending percentage
     */
    public function getSpendingPercentage()
    {
        if ($this->allocated_amount == 0) {
            return 0;
        }
        return ($this->spent_amount / $this->allocated_amount) * 100;
    }
}

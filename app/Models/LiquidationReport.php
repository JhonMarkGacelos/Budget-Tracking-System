<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LiquidationReport extends Model
{
    protected $fillable = [
        'user_id',
        'budget_request_id',
        'report_content',
        'status',
        'department_feedback',
        'admin_feedback',
        'document_path',
        'submitted_at',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function budgetRequest()
    {
        return $this->belongsTo(BudgetRequest::class);
    }

    // Status check methods
    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function isDepartmentReviewed()
    {
        return $this->status === 'department_reviewed';
    }

    public function isAdminReviewed()
    {
        return $this->status === 'admin_reviewed';
    }
}

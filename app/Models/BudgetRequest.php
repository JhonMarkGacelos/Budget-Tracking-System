<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Filterable;

class BudgetRequest extends Model
{
    use Filterable;

    protected $fillable = [
        'user_id',
        'department_id',
        'title',
        'description',
        'amount',
        'status',
        'department_feedback',
        'admin_feedback',
        'submitted_at',
        'department_reviewed_at',
        'admin_reviewed_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'submitted_at' => 'datetime',
        'department_reviewed_at' => 'datetime',
        'admin_reviewed_at' => 'datetime',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function submissions()
    {
        return $this->hasMany(BudgetSubmission::class);
    }

    public function liquidationReports()
    {
        return $this->hasMany(LiquidationReport::class);
    }

    // Status check methods
    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function isDepartmentApproved()
    {
        return $this->status === 'department_approved';
    }

    public function isDepartmentRejected()
    {
        return $this->status === 'department_rejected';
    }

    public function isAdminApproved()
    {
        return $this->status === 'admin_approved';
    }

    public function isAdminRejected()
    {
        return $this->status === 'admin_rejected';
    }

    public function isFullyApproved()
    {
        return $this->status === 'admin_approved';
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    protected $fillable = [
        'name',
        'description',
    ];

    // Relationships
    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function budgetRequests()
    {
        return $this->hasMany(BudgetRequest::class);
    }

    public function departmentHeads()
    {
        return $this->hasMany(User::class)->where('role', 'department');
    }

    public function faculty()
    {
        return $this->hasMany(User::class)->where('role', 'faculty');
    }
}

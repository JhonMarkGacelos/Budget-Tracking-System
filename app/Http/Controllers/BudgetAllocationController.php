<?php

namespace App\Http\Controllers;

use App\Models\BudgetAllocation;
use App\Models\Department;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class BudgetAllocationController extends Controller
{
    /**
     * Display all budget allocations.
     */
    public function index()
    {
        $user = auth()->user();

        if ($user->isAdmin()) {
            $allocations = BudgetAllocation::with('department')
                ->orderBy('fiscal_year', 'desc')
                ->get();
        } else {
            $allocations = BudgetAllocation::where('department_id', $user->department_id)
                ->with('department')
                ->orderBy('fiscal_year', 'desc')
                ->get();
        }

        return response()->json(['data' => $allocations]);
    }

    /**
     * Store a new budget allocation.
     */
    public function store(Request $request)
    {
        // Only admin can create allocations
        if (!auth()->user()->isAdmin()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Validate initial input
        $validated = $request->validate([
            'department_id' => 'required|exists:departments,id',
            'fiscal_year' => 'required|string|regex:/^\d{4}(-\d{4})?$/',
            'allocated_amount' => 'required|numeric|min:0.01',
            'notes' => 'nullable|string',
        ]);

        // Parse fiscal year - extract just the first year if format is "2025-2026"
        $fiscalYear = $validated['fiscal_year'];
        if (strpos($fiscalYear, '-') !== false) {
            // Format is "2025-2026", extract first year
            $fiscalYear = (int) explode('-', $fiscalYear)[0];
        } else {
            // Format is just "2026"
            $fiscalYear = (int) $fiscalYear;
        }

        // Validate the year is in acceptable range
        if ($fiscalYear < 2020 || $fiscalYear > 2099) {
            return response()->json(['error' => 'Fiscal year must be between 2020 and 2099'], 422);
        }

        $allocation = BudgetAllocation::create([
            'department_id' => $validated['department_id'],
            'fiscal_year' => $fiscalYear,
            'allocated_amount' => $validated['allocated_amount'],
            'spent_amount' => 0,
            'remaining_balance' => $validated['allocated_amount'],
            'notes' => $validated['notes'] ?? null,
        ]);

        // Log the action
        AuditLog::log('create', 'BudgetAllocation', $allocation->id, null, $request);

        return response()->json([
            'message' => 'Budget allocation created successfully',
            'allocation' => $allocation,
        ], 201);
    }

    /**
     * Show a single budget allocation.
     */
    public function show(BudgetAllocation $budgetAllocation)
    {
        $user = auth()->user();

        if (!$user->isAdmin() && $budgetAllocation->department_id !== $user->department_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $data = $budgetAllocation->load('department');
        return response()->json([
            'data' => $data
        ]);
    }

    /**
     * Update budget allocation.
     */
    public function update(Request $request, BudgetAllocation $budgetAllocation)
    {
        if (!auth()->user()->isAdmin()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'allocated_amount' => 'sometimes|numeric|min:0.01',
            'status' => 'sometimes|in:active,frozen,expired',
            'notes' => 'nullable|string',
        ]);

        $oldValues = $budgetAllocation->toArray();
        $budgetAllocation->update($validated);

        // Update remaining balance if allocated amount changed
        if (isset($validated['allocated_amount'])) {
            $budgetAllocation->remaining_balance = $validated['allocated_amount'] - $budgetAllocation->spent_amount;
            $budgetAllocation->save();
        }

        // Log the action
        $changes = [];
        foreach ($validated as $key => $value) {
            $changes[$key] = ['old' => $oldValues[$key] ?? null, 'new' => $value];
        }
        AuditLog::log('update', 'BudgetAllocation', $budgetAllocation->id, $changes, $request);

        return response()->json([
            'message' => 'Budget allocation updated successfully',
            'allocation' => $budgetAllocation,
        ]);
    }

    /**
     * Delete a budget allocation.
     */
    public function destroy(Request $request, BudgetAllocation $budgetAllocation)
    {
        if (!auth()->user()->isAdmin()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        AuditLog::log('delete', 'BudgetAllocation', $budgetAllocation->id, null, $request);

        $budgetAllocation->delete();

        return response()->json(['message' => 'Budget allocation deleted successfully']);
    }

    /**
     * Get allocation statistics for a department
     */
    public function statistics(Request $request)
    {
        $user = auth()->user();
        $fiscalYear = $request->query('fiscal_year', date('Y'));

        if ($user->isAdmin()) {
            $allocations = BudgetAllocation::where('fiscal_year', $fiscalYear)
                ->with('department')
                ->get();
        } else {
            $allocations = BudgetAllocation::where('department_id', $user->department_id)
                ->where('fiscal_year', $fiscalYear)
                ->get();
        }

        $statistics = $allocations->map(function ($allocation) {
            return [
                'department' => $allocation->department->name,
                'allocated_amount' => $allocation->allocated_amount,
                'spent_amount' => $allocation->spent_amount,
                'remaining_balance' => $allocation->remaining_balance,
                'spending_percentage' => $allocation->getSpendingPercentage(),
                'status' => $allocation->status,
            ];
        });

        $totals = [
            'total_allocated' => $allocations->sum('allocated_amount'),
            'total_spent' => $allocations->sum('spent_amount'),
            'total_remaining' => $allocations->sum('remaining_balance'),
        ];

        return response()->json([
            'fiscal_year' => $fiscalYear,
            'allocations' => $statistics,
            'totals' => $totals,
        ]);
    }
}

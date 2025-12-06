<?php

namespace App\Http\Controllers;

use App\Models\BudgetRequest;
use App\Models\BudgetAllocation;
use App\Mail\BudgetRequestApprovedDepartment;
use App\Mail\BudgetRequestRejectedDepartment;
use App\Mail\BudgetRequestApprovedAdmin;
use App\Mail\BudgetRequestRejectedAdmin;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;

class BudgetRequestController extends Controller
{
    /**
     * Display a listing of budget requests.
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', BudgetRequest::class);
        $user = auth()->user();

        $query = BudgetRequest::with(['user', 'department']);

        if ($user->isAdmin()) {
            // Admin sees all requests
            $query = $query;
        } elseif ($user->isDepartment()) {
            // Department sees only their department's requests
            $query = $query->where('department_id', $user->department_id);
        } else {
            // Faculty sees only their own requests
            $query = $query->where('user_id', $user->id);
        }

        // Apply filters
        $status = $request->query('status');
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');
        $departmentId = $request->query('department_id');
        $search = $request->query('search');
        $minAmount = $request->query('min_amount');
        $maxAmount = $request->query('max_amount');

        if ($status) {
            $query = $query->filterByStatus($status);
        }
        if ($startDate || $endDate) {
            $query = $query->filterByDateRange($startDate, $endDate);
        }
        if ($departmentId && $user->isAdmin()) {
            $query = $query->filterByDepartment($departmentId);
        }
        if ($search) {
            $query = $query->search($search);
        }
        if ($minAmount !== null || $maxAmount !== null) {
            $query = $query->filterByAmountRange($minAmount, $maxAmount);
        }

        $requests = $query->orderBy('created_at', 'desc')->paginate(15);

        return response()->json($requests);
    }

    /**
     * Show the form for creating a new budget request.
     */
    public function create()
    {
        $this->authorize('create', BudgetRequest::class);
        return response()->json(['message' => 'Ready to create budget request']);
    }

    /**
     * Store a newly created budget request.
     */
    public function store(Request $request)
    {
        $this->authorize('create', BudgetRequest::class);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'amount' => 'required|numeric|min:0.01',
        ]);

        $user = auth()->user();
        
        $budgetRequest = BudgetRequest::create([
            'user_id' => $user->id,
            'department_id' => $user->department_id,
            'title' => $validated['title'],
            'description' => $validated['description'],
            'amount' => $validated['amount'],
            'status' => 'pending',
            'submitted_at' => Carbon::now(),
        ]);

        return response()->json(['message' => 'Budget request created successfully', 'request' => $budgetRequest], 201);
    }

    /**
     * Display the specified budget request.
     */
    public function show(BudgetRequest $budgetRequest)
    {
        $this->authorize('view', $budgetRequest);
        $data = $budgetRequest->load(['user', 'department', 'submissions', 'liquidationReports']);
        return response()->json([
            'data' => $data
        ]);
    }

    /**
     * Show the form for editing the specified budget request.
     */
    public function edit(BudgetRequest $budgetRequest)
    {
        $this->authorize('update', $budgetRequest);
        return response()->json($budgetRequest);
    }

    /**
     * Update the specified budget request.
     */
    public function update(Request $request, BudgetRequest $budgetRequest)
    {
        $this->authorize('update', $budgetRequest);

        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'amount' => 'sometimes|numeric|min:0.01',
        ]);

        $budgetRequest->update($validated);

        return response()->json(['message' => 'Budget request updated successfully', 'request' => $budgetRequest]);
    }

    /**
     * Remove the specified budget request.
     */
    public function destroy(BudgetRequest $budgetRequest)
    {
        $this->authorize('delete', $budgetRequest);
        
        $budgetRequest->delete();
        return response()->json(['message' => 'Budget request deleted successfully']);
    }

    /**
     * Approve budget request at department level.
     */
    public function approveDepartment(Request $request, BudgetRequest $budgetRequest)
    {
        $this->authorize('approveDepartment', $budgetRequest);

        // For requests <= $20,000, deduct from budget allocation immediately
        if ($budgetRequest->amount <= 20000) {
            $this->deductFromBudget($budgetRequest);
        }

        $budgetRequest->update([
            'status' => 'department_approved',
            'department_reviewed_at' => Carbon::now(),
        ]);

        // Send email notification to faculty
        Mail::to($budgetRequest->user->email)
            ->send(new BudgetRequestApprovedDepartment($budgetRequest));

        return response()->json(['message' => 'Budget request approved by department', 'request' => $budgetRequest]);
    }

    /**
     * Reject budget request at department level.
     */
    public function rejectDepartment(Request $request, BudgetRequest $budgetRequest)
    {
        $this->authorize('rejectDepartment', $budgetRequest);

        $validated = $request->validate([
            'feedback' => 'required|string',
        ]);

        $budgetRequest->update([
            'status' => 'department_rejected',
            'department_feedback' => $validated['feedback'],
            'department_reviewed_at' => Carbon::now(),
        ]);

        // Send email notification to faculty
        Mail::to($budgetRequest->user->email)
            ->send(new BudgetRequestRejectedDepartment($budgetRequest));

        return response()->json(['message' => 'Budget request rejected by department', 'request' => $budgetRequest]);
    }

    /**
     * Approve budget request at admin level.
     */
    public function approveAdmin(Request $request, BudgetRequest $budgetRequest)
    {
        $this->authorize('approveAdmin', $budgetRequest);

        // Deduct from budget allocation when admin approves
        $this->deductFromBudget($budgetRequest);

        $budgetRequest->update([
            'status' => 'admin_approved',
            'admin_reviewed_at' => Carbon::now(),
        ]);

        // Send email notification to faculty
        Mail::to($budgetRequest->user->email)
            ->send(new BudgetRequestApprovedAdmin($budgetRequest));

        return response()->json(['message' => 'Budget request approved by admin', 'request' => $budgetRequest]);
    }

    /**
     * Reject budget request at admin level.
     */
    public function rejectAdmin(Request $request, BudgetRequest $budgetRequest)
    {
        $this->authorize('rejectAdmin', $budgetRequest);

        $validated = $request->validate([
            'feedback' => 'required|string',
        ]);

        $budgetRequest->update([
            'status' => 'admin_rejected',
            'admin_feedback' => $validated['feedback'],
            'admin_reviewed_at' => Carbon::now(),
        ]);

        // Send email notification to faculty
        Mail::to($budgetRequest->user->email)
            ->send(new BudgetRequestRejectedAdmin($budgetRequest));

        return response()->json(['message' => 'Budget request rejected by admin', 'request' => $budgetRequest]);
    }

    /**
     * Deduct approved amount from department budget allocation.
     */
    private function deductFromBudget(BudgetRequest $budgetRequest)
    {
        // Get current fiscal year
        $currentYear = date('Y');
        
        // Find the budget allocation for this department and fiscal year
        $allocation = BudgetAllocation::where('department_id', $budgetRequest->department_id)
            ->where('fiscal_year', $currentYear)
            ->where('status', 'active')
            ->first();

        if (!$allocation) {
            // Log warning but don't fail the approval
            \Log::warning("No active budget allocation found for department {$budgetRequest->department_id} for fiscal year {$currentYear}");
            return;
        }

        // Check if there's enough budget
        if ($allocation->remaining_balance < $budgetRequest->amount) {
            throw new \Exception("Insufficient budget. Available: \${$allocation->remaining_balance}, Required: \${$budgetRequest->amount}");
        }

        // Deduct from allocation
        $allocation->spent_amount += $budgetRequest->amount;
        $allocation->remaining_balance -= $budgetRequest->amount;
        $allocation->save();

        \Log::info("Deducted \${$budgetRequest->amount} from department {$budgetRequest->department_id} budget allocation. Remaining: \${$allocation->remaining_balance}");
    }
}

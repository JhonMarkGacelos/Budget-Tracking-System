<?php

namespace App\Http\Controllers;

use App\Models\LiquidationReport;
use App\Models\BudgetRequest;
use Illuminate\Http\Request;
use Carbon\Carbon;

class LiquidationReportController extends Controller
{
    /**
     * Display a listing of liquidation reports.
     */
    public function index()
    {
        $this->authorize('viewAny', LiquidationReport::class);
        $user = auth()->user();

        if ($user->isAdmin()) {
            $reports = LiquidationReport::with(['user', 'budgetRequest'])->orderBy('created_at', 'desc')->paginate(15);
        } elseif ($user->isDepartment()) {
            $reports = LiquidationReport::whereHas('budgetRequest', function ($query) use ($user) {
                $query->where('department_id', $user->department_id);
            })->with(['user', 'budgetRequest'])->orderBy('created_at', 'desc')->paginate(15);
        } else {
            $reports = LiquidationReport::where('user_id', $user->id)
                ->with(['user', 'budgetRequest'])
                ->orderBy('created_at', 'desc')
                ->paginate(15);
        }

        return response()->json($reports);
    }

    /**
     * Show the form for creating a new liquidation report.
     */
    public function create(Request $request)
    {
        $this->authorize('create', LiquidationReport::class);
        
        $budgetRequest = BudgetRequest::findOrFail($request->budget_request_id);
        return response()->json(['budget_request' => $budgetRequest]);
    }

    /**
     * Store a newly created liquidation report.
     */
    public function store(Request $request)
    {
        $this->authorize('create', LiquidationReport::class);

        $validated = $request->validate([
            'budget_request_id' => 'required|exists:budget_requests,id',
            'report_content' => 'required|string',
            'document' => 'nullable|file|mimes:pdf,doc,docx|max:5120',
        ]);

        $user = auth()->user();
        $budgetRequest = BudgetRequest::findOrFail($validated['budget_request_id']);

        // Check if user owns this budget request
        if ($budgetRequest->user_id !== $user->id) {
            return response()->json(['message' => 'You can only submit reports for your own budget requests'], 403);
        }

        $documentPath = null;
        if ($request->hasFile('document')) {
            $documentPath = $request->file('document')->store('liquidation_reports', 'public');
        }

        $report = LiquidationReport::create([
            'user_id' => $user->id,
            'budget_request_id' => $validated['budget_request_id'],
            'report_content' => $validated['report_content'],
            'document_path' => $documentPath,
            'status' => 'pending',
            'submitted_at' => Carbon::now(),
        ]);

        return response()->json(['message' => 'Liquidation report created successfully', 'report' => $report], 201);
    }

    /**
     * Display the specified liquidation report.
     */
    public function show(LiquidationReport $liquidationReport)
    {
        $this->authorize('view', $liquidationReport);
        return response()->json($liquidationReport->load(['user', 'budgetRequest']));
    }

    /**
     * Show the form for editing the specified liquidation report.
     */
    public function edit(LiquidationReport $liquidationReport)
    {
        $this->authorize('update', $liquidationReport);
        return response()->json($liquidationReport);
    }

    /**
     * Update the specified liquidation report.
     */
    public function update(Request $request, LiquidationReport $liquidationReport)
    {
        $this->authorize('update', $liquidationReport);

        $validated = $request->validate([
            'report_content' => 'sometimes|string',
            'document' => 'nullable|file|mimes:pdf,doc,docx|max:5120',
        ]);

        if (isset($validated['report_content'])) {
            $liquidationReport->report_content = $validated['report_content'];
        }

        if ($request->hasFile('document')) {
            $liquidationReport->document_path = $request->file('document')->store('liquidation_reports', 'public');
        }

        $liquidationReport->save();

        return response()->json(['message' => 'Liquidation report updated successfully', 'report' => $liquidationReport]);
    }

    /**
     * Remove the specified liquidation report.
     */
    public function destroy(LiquidationReport $liquidationReport)
    {
        $this->authorize('delete', $liquidationReport);
        
        $liquidationReport->delete();
        return response()->json(['message' => 'Liquidation report deleted successfully']);
    }

    /**
     * Review liquidation report at department level.
     */
    public function reviewDepartment(Request $request, LiquidationReport $liquidationReport)
    {
        $this->authorize('reviewDepartment', $liquidationReport);

        $validated = $request->validate([
            'feedback' => 'required|string',
        ]);

        $liquidationReport->update([
            'status' => 'department_reviewed',
            'department_feedback' => $validated['feedback'],
        ]);

        return response()->json(['message' => 'Liquidation report reviewed by department', 'report' => $liquidationReport]);
    }

    /**
     * Review liquidation report at admin level.
     */
    public function reviewAdmin(Request $request, LiquidationReport $liquidationReport)
    {
        $this->authorize('reviewAdmin', $liquidationReport);

        $validated = $request->validate([
            'feedback' => 'required|string',
        ]);

        $liquidationReport->update([
            'status' => 'admin_reviewed',
            'admin_feedback' => $validated['feedback'],
        ]);

        return response()->json(['message' => 'Liquidation report reviewed by admin', 'report' => $liquidationReport]);
    }
}

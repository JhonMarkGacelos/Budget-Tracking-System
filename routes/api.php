<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\BudgetRequestController;
use App\Http\Controllers\BudgetSubmissionController;
use App\Http\Controllers\LiquidationReportController;
use App\Http\Controllers\ApprovalController;
use App\Http\Controllers\BudgetAllocationController;
use App\Http\Controllers\AuditLogController;

// Get or create API token for authenticated web users
Route::middleware('auth')->get('/get-token', function (Request $request) {
    $user = $request->user();
    
    // Delete old tokens and create a fresh one
    $user->tokens()->delete();
    $token = $user->createToken('web-session-token')->plainTextToken;
    
    return response()->json(['token' => $token, 'message' => 'Token created successfully']);
});

Route::middleware('auth:sanctum')->group(function () {
    // User authentication info
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // User Management Routes
    Route::apiResource('users', UserController::class);

    // Department Management Routes
    Route::apiResource('departments', DepartmentController::class);
    Route::apiResource('budget-requests', BudgetRequestController::class);
    
    // Department level approval
    Route::post('budget-requests/{budgetRequest}/approve-department', [BudgetRequestController::class, 'approveDepartment']);
    Route::post('budget-requests/{budgetRequest}/reject-department', [BudgetRequestController::class, 'rejectDepartment']);
    
    // Admin level approval
    Route::post('budget-requests/{budgetRequest}/approve-admin', [BudgetRequestController::class, 'approveAdmin']);
    Route::post('budget-requests/{budgetRequest}/reject-admin', [BudgetRequestController::class, 'rejectAdmin']);

    // Budget Submission Routes
    Route::apiResource('budget-submissions', BudgetSubmissionController::class);
    
    // Department level review
    Route::post('budget-submissions/{budgetSubmission}/review-department', [BudgetSubmissionController::class, 'reviewDepartment']);
    
    // Admin level review
    Route::post('budget-submissions/{budgetSubmission}/review-admin', [BudgetSubmissionController::class, 'reviewAdmin']);

    // Liquidation Report Routes
    Route::apiResource('liquidation-reports', LiquidationReportController::class);
    
    // Department level review
    Route::post('liquidation-reports/{liquidationReport}/review-department', [LiquidationReportController::class, 'reviewDepartment']);
    
    // Admin level review
    Route::post('liquidation-reports/{liquidationReport}/review-admin', [LiquidationReportController::class, 'reviewAdmin']);

    // Budget Allocation Routes
    Route::apiResource('budget-allocations', BudgetAllocationController::class);
    Route::get('budget-allocations/statistics', [BudgetAllocationController::class, 'statistics']);

    // Approval Management Routes
    Route::get('approvals/pending', [ApprovalController::class, 'getPendingApprovals']);
    Route::get('approvals/statistics', [ApprovalController::class, 'getStatistics']);

    // Audit Log Routes
    Route::get('audit-logs', [AuditLogController::class, 'index']);
    Route::get('audit-logs/{modelType}/{modelId}', [AuditLogController::class, 'show']);
    Route::get('audit-logs/summary', [AuditLogController::class, 'summary']);

    // Analytics Routes
    Route::controller(\App\Http\Controllers\AnalyticsController::class)->group(function () {
        Route::get('analytics/overview', 'getOverview');
        Route::get('analytics/budget-by-department', 'getBudgetByDepartment');
        Route::get('analytics/request-status', 'getRequestStatus');
        Route::get('analytics/spending-trends', 'getSpendingTrends');
        Route::get('analytics/budget-allocation-vs-spending', 'getBudgetAllocationVsSpending');
        Route::get('analytics/department-spending-percentage', 'getDepartmentSpendingPercentage');
        Route::get('analytics/forecast', 'getForecast');
        Route::get('analytics/top-departments', 'getTopDepartments');
        Route::get('analytics/approval-rate', 'getApprovalRate');
        Route::get('analytics/financial-report', 'getFinancialReport');
    });
});

Route::post('/login', function (Request $request) {
    $credentials = $request->validate([
        'email' => 'required|email',
        'password' => 'required|string',
    ]);

    if (auth()->attempt($credentials)) {
        $user = auth()->user();
        $token = $user->createToken('app-token')->plainTextToken;
        
        return response()->json([
            'message' => 'Login successful',
            'user' => $user->load('department'),
            'token' => $token,
        ]);
    }

    return response()->json(['message' => 'Invalid credentials'], 401);
});

Route::post('/logout', function (Request $request) {
    $request->user()->tokens()->delete();
    return response()->json(['message' => 'Logged out successfully']);
})->middleware('auth:sanctum');

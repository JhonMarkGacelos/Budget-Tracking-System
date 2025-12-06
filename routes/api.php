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

    // User Management Routes (API endpoints only, no named routes to avoid conflicts)
    Route::get('api/users', [UserController::class, 'index']);
    Route::post('api/users', [UserController::class, 'store']);
    Route::get('api/users/{user}', [UserController::class, 'show']);
    Route::put('api/users/{user}', [UserController::class, 'update']);
    Route::delete('api/users/{user}', [UserController::class, 'destroy']);

    // Department Management Routes (API endpoints only)
    Route::get('api/departments', [DepartmentController::class, 'index']);
    Route::post('api/departments', [DepartmentController::class, 'store']);
    Route::get('api/departments/{department}', [DepartmentController::class, 'show']);
    Route::put('api/departments/{department}', [DepartmentController::class, 'update']);
    Route::delete('api/departments/{department}', [DepartmentController::class, 'destroy']);
    
    // Budget Request Routes (API endpoints only)
    Route::get('api/budget-requests', [BudgetRequestController::class, 'index']);
    Route::post('api/budget-requests', [BudgetRequestController::class, 'store']);
    Route::get('api/budget-requests/{budgetRequest}', [BudgetRequestController::class, 'show']);
    Route::put('api/budget-requests/{budgetRequest}', [BudgetRequestController::class, 'update']);
    Route::delete('api/budget-requests/{budgetRequest}', [BudgetRequestController::class, 'destroy']);
    
    // Department level approval
    Route::post('api/budget-requests/{budgetRequest}/approve-department', [BudgetRequestController::class, 'approveDepartment']);
    Route::post('api/budget-requests/{budgetRequest}/reject-department', [BudgetRequestController::class, 'rejectDepartment']);
    
    // Admin level approval
    Route::post('api/budget-requests/{budgetRequest}/approve-admin', [BudgetRequestController::class, 'approveAdmin']);
    Route::post('api/budget-requests/{budgetRequest}/reject-admin', [BudgetRequestController::class, 'rejectAdmin']);

    // Budget Submission Routes (API endpoints only)
    Route::get('api/budget-submissions', [BudgetSubmissionController::class, 'index']);
    Route::post('api/budget-submissions', [BudgetSubmissionController::class, 'store']);
    Route::get('api/budget-submissions/{budgetSubmission}', [BudgetSubmissionController::class, 'show']);
    Route::put('api/budget-submissions/{budgetSubmission}', [BudgetSubmissionController::class, 'update']);
    Route::delete('api/budget-submissions/{budgetSubmission}', [BudgetSubmissionController::class, 'destroy']);
    
    // Department level review
    Route::post('api/budget-submissions/{budgetSubmission}/review-department', [BudgetSubmissionController::class, 'reviewDepartment']);
    
    // Admin level review
    Route::post('api/budget-submissions/{budgetSubmission}/review-admin', [BudgetSubmissionController::class, 'reviewAdmin']);

    // Liquidation Report Routes (API endpoints only)
    Route::get('api/liquidation-reports', [LiquidationReportController::class, 'index']);
    Route::post('api/liquidation-reports', [LiquidationReportController::class, 'store']);
    Route::get('api/liquidation-reports/{liquidationReport}', [LiquidationReportController::class, 'show']);
    Route::put('api/liquidation-reports/{liquidationReport}', [LiquidationReportController::class, 'update']);
    Route::delete('api/liquidation-reports/{liquidationReport}', [LiquidationReportController::class, 'destroy']);
    
    // Department level review
    Route::post('api/liquidation-reports/{liquidationReport}/review-department', [LiquidationReportController::class, 'reviewDepartment']);
    
    // Admin level review
    Route::post('api/liquidation-reports/{liquidationReport}/review-admin', [LiquidationReportController::class, 'reviewAdmin']);

    // Budget Allocation Routes (API endpoints only)
    Route::get('api/budget-allocations', [BudgetAllocationController::class, 'index']);
    Route::post('api/budget-allocations', [BudgetAllocationController::class, 'store']);
    Route::get('api/budget-allocations/{budgetAllocation}', [BudgetAllocationController::class, 'show']);
    Route::put('api/budget-allocations/{budgetAllocation}', [BudgetAllocationController::class, 'update']);
    Route::delete('api/budget-allocations/{budgetAllocation}', [BudgetAllocationController::class, 'destroy']);
    Route::get('api/budget-allocations/statistics', [BudgetAllocationController::class, 'statistics']);

    // Approval Management Routes
    Route::get('api/approvals/pending', [ApprovalController::class, 'getPendingApprovals']);
    Route::get('api/approvals/statistics', [ApprovalController::class, 'getStatistics']);

    // Audit Log Routes
    Route::get('api/audit-logs', [AuditLogController::class, 'index']);
    Route::get('api/audit-logs/{modelType}/{modelId}', [AuditLogController::class, 'show']);
    Route::get('api/audit-logs/summary', [AuditLogController::class, 'summary']);

    // Analytics Routes
    Route::controller(\App\Http\Controllers\AnalyticsController::class)->group(function () {
        Route::get('api/analytics/overview', 'getOverview');
        Route::get('api/analytics/budget-by-department', 'getBudgetByDepartment');
        Route::get('api/analytics/request-status', 'getRequestStatus');
        Route::get('api/analytics/spending-trends', 'getSpendingTrends');
        Route::get('api/analytics/budget-allocation-vs-spending', 'getBudgetAllocationVsSpending');
        Route::get('api/analytics/department-spending-percentage', 'getDepartmentSpendingPercentage');
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

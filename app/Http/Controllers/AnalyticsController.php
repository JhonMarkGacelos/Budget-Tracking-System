<?php

namespace App\Http\Controllers;

use App\Models\BudgetRequest;
use App\Models\BudgetAllocation;
use App\Models\Department;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
use DB;

class AnalyticsController extends Controller
{
    /**
     * Display analytics dashboard
     */
    public function dashboard()
    {
        return view('analytics.dashboard');
    }

    /**
     * Get overview statistics for dashboard
     */
    public function getOverview()
    {
        $totalRequests = BudgetRequest::count();
        $approvedRequests = BudgetRequest::where('status', 'approved_admin')->count();
        $pendingRequests = BudgetRequest::where('status', 'pending')->count();
        $totalBudget = BudgetAllocation::sum('allocated_amount');
        $spentBudget = BudgetAllocation::sum('spent_amount');
        $remainingBudget = $totalBudget - $spentBudget;

        return response()->json([
            'total_requests' => $totalRequests,
            'approved_requests' => $approvedRequests,
            'pending_requests' => $pendingRequests,
            'total_budget' => $totalBudget,
            'spent_budget' => $spentBudget,
            'remaining_budget' => $remainingBudget,
            'spending_percentage' => $totalBudget > 0 ? round(($spentBudget / $totalBudget) * 100, 2) : 0,
        ]);
    }

    /**
     * Get budget distribution by department
     */
    public function getBudgetByDepartment()
    {
        $data = Department::withCount('budgetRequests')
            ->with([
                'budgetAllocations' => function ($query) {
                    $query->sum('allocated_amount');
                }
            ])
            ->get()
            ->map(function ($dept) {
                return [
                    'name' => $dept->name,
                    'budget' => $dept->budgetAllocations->sum('allocated_amount'),
                    'requests' => $dept->budget_requests_count,
                ];
            });

        return response()->json([
            'labels' => $data->pluck('name'),
            'budgets' => $data->pluck('budget'),
            'requests' => $data->pluck('requests'),
            'data' => $data,
        ]);
    }

    /**
     * Get request status distribution
     */
    public function getRequestStatus()
    {
        $statuses = BudgetRequest::select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status');

        $colors = [
            'pending' => '#FFA500',
            'approved_department' => '#87CEEB',
            'approved_admin' => '#28a745',
            'rejected' => '#dc3545',
            'cancelled' => '#6c757d',
        ];

        $labels = [];
        $data = [];
        $chartColors = [];

        foreach ($statuses as $status => $count) {
            $labels[] = ucfirst(str_replace('_', ' ', $status));
            $data[] = $count;
            $chartColors[] = $colors[$status] ?? '#888888';
        }

        return response()->json([
            'labels' => $labels,
            'data' => $data,
            'colors' => $chartColors,
        ]);
    }

    /**
     * Get spending trends over time
     */
    public function getSpendingTrends()
    {
        $sixMonthsAgo = Carbon::now()->subMonths(6)->startOfMonth();
        
        $trends = BudgetRequest::select(
            DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
            DB::raw('COUNT(*) as request_count'),
            DB::raw('SUM(CASE WHEN status = "approved_admin" THEN amount ELSE 0 END) as approved_amount'),
            DB::raw('SUM(amount) as total_amount')
        )
            ->where('created_at', '>=', $sixMonthsAgo)
            ->groupBy(DB::raw('DATE_FORMAT(created_at, "%Y-%m")'))
            ->orderBy('month')
            ->get();

        $labels = [];
        $requestCounts = [];
        $approvedAmounts = [];
        $totalAmounts = [];

        foreach ($trends as $trend) {
            $labels[] = Carbon::createFromFormat('Y-m', $trend->month)->format('M Y');
            $requestCounts[] = $trend->request_count;
            $approvedAmounts[] = $trend->approved_amount;
            $totalAmounts[] = $trend->total_amount;
        }

        return response()->json([
            'labels' => $labels,
            'request_counts' => $requestCounts,
            'approved_amounts' => $approvedAmounts,
            'total_amounts' => $totalAmounts,
        ]);
    }

    /**
     * Get budget allocation vs spending
     */
    public function getBudgetAllocationVsSpending()
    {
        $allocations = BudgetAllocation::select(
            'fiscal_year',
            DB::raw('SUM(allocated_amount) as total_allocated'),
            DB::raw('SUM(spent_amount) as total_spent')
        )
            ->groupBy('fiscal_year')
            ->orderBy('fiscal_year', 'desc')
            ->limit(5)
            ->get();

        $labels = [];
        $allocated = [];
        $spent = [];

        foreach ($allocations as $allocation) {
            $labels[] = 'FY ' . $allocation->fiscal_year;
            $allocated[] = $allocation->total_allocated;
            $spent[] = $allocation->total_spent;
        }

        return response()->json([
            'labels' => array_reverse($labels),
            'allocated' => array_reverse($allocated),
            'spent' => array_reverse($spent),
        ]);
    }

    /**
     * Get department spending percentage
     */
    public function getDepartmentSpendingPercentage()
    {
        $data = BudgetAllocation::select(
            'department_id',
            DB::raw('SUM(allocated_amount) as total_allocated'),
            DB::raw('SUM(spent_amount) as total_spent')
        )
            ->with('department')
            ->groupBy('department_id')
            ->get()
            ->map(function ($allocation) {
                return [
                    'department' => $allocation->department->name,
                    'allocated' => $allocation->total_allocated,
                    'spent' => $allocation->total_spent,
                    'remaining' => $allocation->total_allocated - $allocation->total_spent,
                    'percentage' => $allocation->total_allocated > 0 
                        ? round(($allocation->total_spent / $allocation->total_allocated) * 100, 2) 
                        : 0,
                ];
            });

        return response()->json([
            'labels' => $data->pluck('department'),
            'percentages' => $data->pluck('percentage'),
            'allocated' => $data->pluck('allocated'),
            'spent' => $data->pluck('spent'),
            'data' => $data,
        ]);
    }

    /**
     * Get financial forecast (simple projection)
     */
    public function getForecast()
    {
        $sixMonthsAgo = Carbon::now()->subMonths(6)->startOfMonth();
        
        $historicalData = BudgetRequest::select(
            DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
            DB::raw('SUM(CASE WHEN status = "approved_admin" THEN amount ELSE 0 END) as approved_amount')
        )
            ->where('created_at', '>=', $sixMonthsAgo)
            ->groupBy(DB::raw('DATE_FORMAT(created_at, "%Y-%m")'))
            ->orderBy('month')
            ->pluck('approved_amount', 'month');

        // Simple moving average forecast
        $forecast = [];
        $historyArray = $historicalData->values()->toArray();
        
        if (count($historyArray) > 0) {
            $average = array_sum($historyArray) / count($historyArray);
            for ($i = 0; $i < 3; $i++) {
                $forecast[] = round($average * (1 + (rand(-5, 5) / 100)), 2); // Add ±5% variance
            }
        }

        $currentMonth = Carbon::now();
        $labels = [];
        
        // Historical labels
        foreach ($historicalData->keys() as $month) {
            $labels[] = Carbon::createFromFormat('Y-m', $month)->format('M Y');
        }

        // Forecast labels
        for ($i = 1; $i <= 3; $i++) {
            $labels[] = $currentMonth->addMonth($i)->format('M Y');
        }

        return response()->json([
            'labels' => $labels,
            'historical' => array_merge($historyArray, [null, null, null]),
            'forecast' => array_merge(array_fill(0, count($historyArray), null), $forecast),
        ]);
    }

    /**
     * Get top departments by budget
     */
    public function getTopDepartments()
    {
        $data = BudgetAllocation::select(
            'department_id',
            DB::raw('SUM(allocated_amount) as total_allocated')
        )
            ->with('department')
            ->groupBy('department_id')
            ->orderByDesc(DB::raw('SUM(allocated_amount)'))
            ->limit(5)
            ->get()
            ->map(function ($allocation) {
                return [
                    'department' => $allocation->department->name,
                    'budget' => $allocation->total_allocated,
                ];
            });

        return response()->json([
            'labels' => $data->pluck('department'),
            'budgets' => $data->pluck('budget'),
            'data' => $data,
        ]);
    }

    /**
     * Get request approval rate
     */
    public function getApprovalRate()
    {
        $total = BudgetRequest::count();
        $approved = BudgetRequest::where('status', 'approved_admin')->count();
        $rejected = BudgetRequest::where('status', 'rejected')->count();
        $pending = BudgetRequest::whereIn('status', ['pending', 'approved_department'])->count();

        $approvalRate = $total > 0 ? round(($approved / $total) * 100, 2) : 0;
        $rejectionRate = $total > 0 ? round(($rejected / $total) * 100, 2) : 0;
        $pendingRate = $total > 0 ? round(($pending / $total) * 100, 2) : 0;

        return response()->json([
            'total' => $total,
            'approved' => $approved,
            'rejected' => $rejected,
            'pending' => $pending,
            'approval_rate' => $approvalRate,
            'rejection_rate' => $rejectionRate,
            'pending_rate' => $pendingRate,
            'labels' => ['Approved', 'Rejected', 'Pending'],
            'data' => [$approved, $rejected, $pending],
            'colors' => ['#28a745', '#dc3545', '#FFA500'],
        ]);
    }

    /**
     * Get detailed financial report
     */
    public function getFinancialReport()
    {
        $report = [
            'summary' => [
                'total_budget' => BudgetAllocation::sum('allocated_amount'),
                'total_spent' => BudgetAllocation::sum('spent_amount'),
                'total_remaining' => BudgetAllocation::sum('allocated_amount') - BudgetAllocation::sum('spent_amount'),
            ],
            'by_department' => BudgetAllocation::select(
                'department_id',
                DB::raw('SUM(allocated_amount) as allocated'),
                DB::raw('SUM(spent_amount) as spent')
            )
                ->with('department')
                ->groupBy('department_id')
                ->get()
                ->map(function ($row) {
                    return [
                        'department' => $row->department->name,
                        'allocated' => $row->allocated,
                        'spent' => $row->spent,
                        'remaining' => $row->allocated - $row->spent,
                        'percentage' => $row->allocated > 0 ? round(($row->spent / $row->allocated) * 100, 2) : 0,
                    ];
                }),
            'by_status' => BudgetRequest::select('status', DB::raw('COUNT(*) as count'), DB::raw('SUM(amount) as total_amount'))
                ->groupBy('status')
                ->get()
                ->map(function ($row) {
                    return [
                        'status' => ucfirst(str_replace('_', ' ', $row->status)),
                        'count' => $row->count,
                        'amount' => $row->total_amount,
                    ];
                }),
        ];

        return response()->json($report);
    }
}

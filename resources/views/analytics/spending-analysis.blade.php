@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4">
        <!-- Header -->
        <div class="mb-8 flex justify-between items-start">
            <div>
                <h1 class="text-4xl font-bold text-gray-900 mb-2">
                    <i class="fas fa-chart-bar text-orange-600 mr-3"></i>Spending Analysis Report
                </h1>
                <p class="text-gray-600">Detailed analysis of budget spending and department performance</p>
            </div>
            <a href="{{ route('analytics.dashboard') }}" class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                <i class="fas fa-arrow-left mr-2"></i>Back to Dashboard
            </a>
        </div>

        <!-- Charts Row 1 -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <!-- Department Spending Comparison -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-6">
                    <i class="fas fa-chart-bar text-blue-600 mr-2"></i>Department Spending Comparison
                </h2>
                <div style="position: relative; height: 300px;">
                    <canvas id="deptSpendingComparisonChart"></canvas>
                </div>
            </div>

            <!-- Budget Utilization Rate -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-6">
                    <i class="fas fa-gauge text-green-600 mr-2"></i>Budget Utilization Rate
                </h2>
                <div style="position: relative; height: 300px;">
                    <canvas id="utilizationRateChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Charts Row 2 -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <!-- Spending Trends -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-6">
                    <i class="fas fa-chart-line text-purple-600 mr-2"></i>6-Month Spending Trends
                </h2>
                <div style="position: relative; height: 300px;">
                    <canvas id="spendingTrendsAnalysisChart"></canvas>
                </div>
            </div>

            <!-- Budget vs Spent -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-6">
                    <i class="fas fa-balance-scale text-indigo-600 mr-2"></i>Budget vs Spending by Fiscal Year
                </h2>
                <div style="position: relative; height: 300px;">
                    <canvas id="budgetVsSpentChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Department Performance Table -->
        <div class="bg-white rounded-lg shadow p-6 mb-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">
                <i class="fas fa-medal text-yellow-600 mr-2"></i>Department Performance Metrics
            </h2>
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-900 uppercase">Department</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-900 uppercase">Allocated</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-900 uppercase">Spent</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-900 uppercase">Remaining</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-900 uppercase">Utilization %</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-900 uppercase">Status</th>
                        </tr>
                    </thead>
                    <tbody id="performanceTableBody" class="divide-y divide-gray-200">
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-gray-500">Loading...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Key Insights -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-lg shadow p-6 border-l-4 border-blue-600">
                <h3 class="text-lg font-bold text-blue-900 mb-2">Highest Spending</h3>
                <p class="text-3xl font-bold text-blue-600" id="highestSpending">-</p>
                <p class="text-blue-700 text-sm mt-2">Department with most spending</p>
            </div>

            <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-lg shadow p-6 border-l-4 border-green-600">
                <h3 class="text-lg font-bold text-green-900 mb-2">Best Utilization</h3>
                <p class="text-3xl font-bold text-green-600" id="bestUtilization">-</p>
                <p class="text-green-700 text-sm mt-2">Optimal budget usage</p>
            </div>

            <div class="bg-gradient-to-br from-orange-50 to-orange-100 rounded-lg shadow p-6 border-l-4 border-orange-600">
                <h3 class="text-lg font-bold text-orange-900 mb-2">Average Utilization</h3>
                <p class="text-3xl font-bold text-orange-600" id="avgUtilization">-</p>
                <p class="text-orange-700 text-sm mt-2">Across all departments</p>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<script>
    let charts = {};

    function formatCurrency(value) {
        return new Intl.NumberFormat('en-US', {
            style: 'currency',
            currency: 'USD',
            minimumFractionDigits: 0,
            maximumFractionDigits: 0,
        }).format(value);
    }

    document.addEventListener('DOMContentLoaded', function() {
        loadDeptSpendingComparison();
        loadUtilizationRate();
        loadSpendingTrendsAnalysis();
        loadBudgetVsSpent();
        loadPerformanceMetrics();
    });

    function loadDeptSpendingComparison() {
        axios.get('/api/analytics/department-spending-percentage')
            .then(response => {
                const data = response.data;
                
                if (charts.deptComparison) charts.deptComparison.destroy();
                
                const ctx = document.getElementById('deptSpendingComparisonChart').getContext('2d');
                charts.deptComparison = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: data.labels,
                        datasets: [
                            {
                                label: 'Allocated ($)',
                                data: data.allocated,
                                backgroundColor: '#e0e7ff',
                            },
                            {
                                label: 'Spent ($)',
                                data: data.spent,
                                backgroundColor: '#818cf8',
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        indexAxis: 'y',
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: { padding: 15, font: { size: 12 } }
                            }
                        },
                        scales: {
                            x: {
                                ticks: {
                                    callback: function(value) {
                                        return '$' + value.toLocaleString();
                                    }
                                }
                            }
                        }
                    }
                });
            })
            .catch(error => console.error('Error loading dept spending comparison:', error));
    }

    function loadUtilizationRate() {
        axios.get('/api/analytics/department-spending-percentage')
            .then(response => {
                const data = response.data;
                
                if (charts.utilization) charts.utilization.destroy();
                
                const ctx = document.getElementById('utilizationRateChart').getContext('2d');
                charts.utilization = new Chart(ctx, {
                    type: 'radar',
                    data: {
                        labels: data.labels,
                        datasets: [{
                            label: 'Utilization Rate (%)',
                            data: data.percentages,
                            borderColor: '#10b981',
                            backgroundColor: 'rgba(16, 185, 129, 0.1)',
                            borderWidth: 2,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            r: {
                                max: 100,
                                ticks: {
                                    callback: function(value) {
                                        return value + '%';
                                    }
                                }
                            }
                        },
                        plugins: {
                            legend: {
                                display: false
                            }
                        }
                    }
                });
            })
            .catch(error => console.error('Error loading utilization rate:', error));
    }

    function loadSpendingTrendsAnalysis() {
        axios.get('/api/analytics/spending-trends')
            .then(response => {
                const data = response.data;
                
                if (charts.spendingTrendsAnalysis) charts.spendingTrendsAnalysis.destroy();
                
                const ctx = document.getElementById('spendingTrendsAnalysisChart').getContext('2d');
                charts.spendingTrendsAnalysis = new Chart(ctx, {
                    type: 'area',
                    data: {
                        labels: data.labels,
                        datasets: [{
                            label: 'Approved Spending ($)',
                            data: data.approved_amounts,
                            borderColor: '#f97316',
                            backgroundColor: 'rgba(249, 115, 22, 0.1)',
                            tension: 0.4,
                            fill: true,
                            borderWidth: 2,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false }
                        },
                        scales: {
                            y: {
                                ticks: {
                                    callback: function(value) {
                                        return '$' + value.toLocaleString();
                                    }
                                }
                            }
                        }
                    }
                });
            })
            .catch(error => console.error('Error loading spending trends analysis:', error));
    }

    function loadBudgetVsSpent() {
        axios.get('/api/analytics/budget-allocation-vs-spending')
            .then(response => {
                const data = response.data;
                
                if (charts.budgetVsSpent) charts.budgetVsSpent.destroy();
                
                const ctx = document.getElementById('budgetVsSpentChart').getContext('2d');
                charts.budgetVsSpent = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: data.labels,
                        datasets: [
                            {
                                label: 'Allocated ($)',
                                data: data.allocated,
                                borderColor: '#8b5cf6',
                                backgroundColor: 'rgba(139, 92, 246, 0.1)',
                                tension: 0.4,
                                fill: true,
                                borderWidth: 2,
                            },
                            {
                                label: 'Spent ($)',
                                data: data.spent,
                                borderColor: '#06b6d4',
                                backgroundColor: 'rgba(6, 182, 212, 0.1)',
                                tension: 0.4,
                                fill: true,
                                borderWidth: 2,
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        interaction: { mode: 'index', intersect: false },
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: { padding: 15, font: { size: 12 } }
                            }
                        },
                        scales: {
                            y: {
                                ticks: {
                                    callback: function(value) {
                                        return '$' + value.toLocaleString();
                                    }
                                }
                            }
                        }
                    }
                });
            })
            .catch(error => console.error('Error loading budget vs spent:', error));
    }

    function loadPerformanceMetrics() {
        axios.get('/api/analytics/department-spending-percentage')
            .then(response => {
                const data = response.data.data;

                // Populate table
                const tableBody = document.getElementById('performanceTableBody');
                tableBody.innerHTML = '';

                if (data.length === 0) {
                    tableBody.innerHTML = '<tr><td colspan="6" class="px-6 py-4 text-center text-gray-500">No data available</td></tr>';
                } else {
                    let highest = { dept: '-', spent: 0 };
                    let totalUtilization = 0;

                    data.forEach((dept, index) => {
                        if (dept.spent > highest.spent) {
                            highest = { dept: dept.department, spent: dept.spent };
                        }
                        totalUtilization += dept.percentage;

                        let statusBadge = 'bg-green-100 text-green-800';
                        let statusText = 'Optimal';
                        
                        if (dept.percentage >= 90) {
                            statusBadge = 'bg-red-100 text-red-800';
                            statusText = 'Critical';
                        } else if (dept.percentage >= 75) {
                            statusBadge = 'bg-yellow-100 text-yellow-800';
                            statusText = 'High';
                        }

                        const row = `
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 text-sm font-medium text-gray-900">${dept.department}</td>
                                <td class="px-6 py-4 text-sm text-gray-600">${formatCurrency(dept.allocated)}</td>
                                <td class="px-6 py-4 text-sm text-gray-600">${formatCurrency(dept.spent)}</td>
                                <td class="px-6 py-4 text-sm text-gray-600">${formatCurrency(dept.remaining)}</td>
                                <td class="px-6 py-4 text-sm font-semibold text-gray-900">${dept.percentage}%</td>
                                <td class="px-6 py-4 text-sm">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium ${statusBadge}">
                                        ${statusText}
                                    </span>
                                </td>
                            </tr>
                        `;
                        tableBody.innerHTML += row;
                    });

                    // Update insights
                    document.getElementById('highestSpending').textContent = highest.dept;
                    document.getElementById('bestUtilization').textContent = data.reduce((max, dept) => dept.percentage < max.percentage ? dept : max).department + ' (' + data.reduce((min, dept) => dept.percentage < min.percentage ? dept : min).percentage + '%)';
                    document.getElementById('avgUtilization').textContent = (totalUtilization / data.length).toFixed(1) + '%';
                }
            })
            .catch(error => console.error('Error loading performance metrics:', error));
    }
</script>
@endsection

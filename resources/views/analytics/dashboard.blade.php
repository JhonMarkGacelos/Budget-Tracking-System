@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-4xl font-bold text-gray-900 mb-2">
                <i class="fas fa-chart-line text-blue-600 mr-3"></i>Financial Analytics Dashboard
            </h1>
            <p class="text-gray-600">Real-time financial reports and forecasts for better decision-making</p>
        </div>

        <!-- Overview Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow p-6 border-l-4 border-blue-500">
                <h3 class="text-gray-500 text-sm font-semibold uppercase mb-2">Total Budget</h3>
                <p class="text-3xl font-bold text-gray-900" id="totalBudget">$0</p>
                <p class="text-green-600 text-sm mt-2"><i class="fas fa-arrow-up mr-1"></i>Allocated</p>
            </div>

            <div class="bg-white rounded-lg shadow p-6 border-l-4 border-orange-500">
                <h3 class="text-gray-500 text-sm font-semibold uppercase mb-2">Spent Budget</h3>
                <p class="text-3xl font-bold text-gray-900" id="spentBudget">$0</p>
                <p class="text-orange-600 text-sm mt-2"><i class="fas fa-money-bill-wave mr-1"></i>Current Spending</p>
            </div>

            <div class="bg-white rounded-lg shadow p-6 border-l-4 border-green-500">
                <h3 class="text-gray-500 text-sm font-semibold uppercase mb-2">Remaining Budget</h3>
                <p class="text-3xl font-bold text-gray-900" id="remainingBudget">$0</p>
                <p class="text-green-600 text-sm mt-2"><i class="fas fa-check-circle mr-1"></i>Available</p>
            </div>

            <div class="bg-white rounded-lg shadow p-6 border-l-4 border-purple-500">
                <h3 class="text-gray-500 text-sm font-semibold uppercase mb-2">Pending Requests</h3>
                <p class="text-3xl font-bold text-gray-900" id="pendingRequests">0</p>
                <p class="text-purple-600 text-sm mt-2"><i class="fas fa-hourglass mr-1"></i>Under Review</p>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <!-- Request Status Chart -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-6">
                    <i class="fas fa-pie-chart text-blue-600 mr-2"></i>Request Status Distribution
                </h2>
                <div style="position: relative; height: 300px;">
                    <canvas id="requestStatusChart"></canvas>
                </div>
            </div>

            <!-- Budget by Department Chart -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-6">
                    <i class="fas fa-building text-green-600 mr-2"></i>Budget by Department
                </h2>
                <div style="position: relative; height: 300px;">
                    <canvas id="budgetByDepartmentChart"></canvas>
                </div>
            </div>

            <!-- Spending Trends Chart -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-6">
                    <i class="fas fa-chart-line text-orange-600 mr-2"></i>6-Month Spending Trends
                </h2>
                <div style="position: relative; height: 300px;">
                    <canvas id="spendingTrendsChart"></canvas>
                </div>
            </div>

            <!-- Allocation vs Spending Chart -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-6">
                    <i class="fas fa-balance-scale text-purple-600 mr-2"></i>Budget Allocation vs Spending
                </h2>
                <div style="position: relative; height: 300px;">
                    <canvas id="allocationVsSpendingChart"></canvas>
                </div>
            </div>

            <!-- Department Spending Percentage Chart -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-6">
                    <i class="fas fa-percent text-red-600 mr-2"></i>Department Spending %
                </h2>
                <div style="position: relative; height: 300px;">
                    <canvas id="departmentSpendingChart"></canvas>
                </div>
            </div>

            <!-- Forecast Chart -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-6">
                    <i class="fas fa-crystal-ball text-indigo-600 mr-2"></i>3-Month Forecast
                </h2>
                <div style="position: relative; height: 300px;">
                    <canvas id="forecastChart"></canvas>
                </div>
            </div>

            <!-- Approval Rate Chart -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-6">
                    <i class="fas fa-check-double text-green-600 mr-2"></i>Request Approval Rate
                </h2>
                <div style="position: relative; height: 300px;">
                    <canvas id="approvalRateChart"></canvas>
                </div>
            </div>

            <!-- Top Departments Chart -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-6">
                    <i class="fas fa-crown text-yellow-600 mr-2"></i>Top 5 Departments by Budget
                </h2>
                <div style="position: relative; height: 300px;">
                    <canvas id="topDepartmentsChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="bg-white rounded-lg shadow p-6 mb-8">
            <h2 class="text-xl font-bold text-gray-900 mb-4">
                <i class="fas fa-file-download text-blue-600 mr-2"></i>Reports & Exports
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <a href="{{ route('analytics.financial-report') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-lg transition inline-block text-center">
                    <i class="fas fa-file-excel mr-2"></i>Financial Report
                </a>
                <a href="{{ route('analytics.spending-analysis') }}" class="bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-6 rounded-lg transition inline-block text-center">
                    <i class="fas fa-chart-bar mr-2"></i>Spending Analysis
                </a>
                <a href="{{ route('analytics.forecast-report') }}" class="bg-purple-600 hover:bg-purple-700 text-white font-bold py-3 px-6 rounded-lg transition inline-block text-center">
                    <i class="fas fa-chart-area mr-2"></i>Forecast Report
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js Script -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<!-- Analytics Script -->
<script>
    // Chart instances
    let charts = {};

    // Initialize all charts on page load
    document.addEventListener('DOMContentLoaded', function() {
        loadOverview();
        loadRequestStatusChart();
        loadBudgetByDepartmentChart();
        loadSpendingTrendsChart();
        loadAllocationVsSpendingChart();
        loadDepartmentSpendingChart();
        loadForecastChart();
        loadApprovalRateChart();
        loadTopDepartmentsChart();
    });

    // Format currency
    function formatCurrency(value) {
        return new Intl.NumberFormat('en-US', {
            style: 'currency',
            currency: 'USD',
            minimumFractionDigits: 0,
            maximumFractionDigits: 0,
        }).format(value);
    }

    // Load Overview Data
    function loadOverview() {
        axios.get('/api/analytics/overview')
            .then(response => {
                const data = response.data;
                document.getElementById('totalBudget').textContent = formatCurrency(data.total_budget);
                document.getElementById('spentBudget').textContent = formatCurrency(data.spent_budget);
                document.getElementById('remainingBudget').textContent = formatCurrency(data.remaining_budget);
                document.getElementById('pendingRequests').textContent = data.pending_requests;
            })
            .catch(error => console.error('Error loading overview:', error));
    }

    // Request Status Chart
    function loadRequestStatusChart() {
        axios.get('/api/analytics/request-status')
            .then(response => {
                const data = response.data;
                
                if (charts.requestStatus) charts.requestStatus.destroy();
                
                const ctx = document.getElementById('requestStatusChart').getContext('2d');
                charts.requestStatus = new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: data.labels,
                        datasets: [{
                            data: data.data,
                            backgroundColor: data.colors,
                            borderColor: '#fff',
                            borderWidth: 2,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: { padding: 20, font: { size: 12 } }
                            }
                        }
                    }
                });
            })
            .catch(error => console.error('Error loading request status chart:', error));
    }

    // Budget by Department Chart
    function loadBudgetByDepartmentChart() {
        axios.get('/api/analytics/budget-by-department')
            .then(response => {
                const data = response.data;
                
                if (charts.budgetByDept) charts.budgetByDept.destroy();
                
                const ctx = document.getElementById('budgetByDepartmentChart').getContext('2d');
                charts.budgetByDept = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: data.labels,
                        datasets: [{
                            label: 'Budget Allocated ($)',
                            data: data.budgets,
                            backgroundColor: '#3b82f6',
                            borderColor: '#1e40af',
                            borderWidth: 1,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        indexAxis: 'y',
                        plugins: {
                            legend: { display: false }
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
            .catch(error => console.error('Error loading budget by department chart:', error));
    }

    // Spending Trends Chart
    function loadSpendingTrendsChart() {
        axios.get('/api/analytics/spending-trends')
            .then(response => {
                const data = response.data;
                
                if (charts.spendingTrends) charts.spendingTrends.destroy();
                
                const ctx = document.getElementById('spendingTrendsChart').getContext('2d');
                charts.spendingTrends = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: data.labels,
                        datasets: [
                            {
                                label: 'Approved Amount ($)',
                                data: data.approved_amounts,
                                borderColor: '#10b981',
                                backgroundColor: 'rgba(16, 185, 129, 0.1)',
                                tension: 0.4,
                                fill: true,
                            },
                            {
                                label: 'Total Requests ($)',
                                data: data.total_amounts,
                                borderColor: '#f59e0b',
                                backgroundColor: 'rgba(245, 158, 11, 0.1)',
                                tension: 0.4,
                                fill: true,
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
            .catch(error => console.error('Error loading spending trends chart:', error));
    }

    // Allocation vs Spending Chart
    function loadAllocationVsSpendingChart() {
        axios.get('/api/analytics/budget-allocation-vs-spending')
            .then(response => {
                const data = response.data;
                
                if (charts.allocationVsSpending) charts.allocationVsSpending.destroy();
                
                const ctx = document.getElementById('allocationVsSpendingChart').getContext('2d');
                charts.allocationVsSpending = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: data.labels,
                        datasets: [
                            {
                                label: 'Allocated ($)',
                                data: data.allocated,
                                backgroundColor: '#8b5cf6',
                            },
                            {
                                label: 'Spent ($)',
                                data: data.spent,
                                backgroundColor: '#ec4899',
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
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
            .catch(error => console.error('Error loading allocation vs spending chart:', error));
    }

    // Department Spending Percentage Chart
    function loadDepartmentSpendingChart() {
        axios.get('/api/analytics/department-spending-percentage')
            .then(response => {
                const data = response.data;
                
                if (charts.deptSpending) charts.deptSpending.destroy();
                
                const ctx = document.getElementById('departmentSpendingChart').getContext('2d');
                charts.deptSpending = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: data.labels,
                        datasets: [{
                            label: 'Spending Percentage (%)',
                            data: data.percentages,
                            backgroundColor: '#f97316',
                            borderColor: '#c2410c',
                            borderWidth: 1,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        indexAxis: 'y',
                        plugins: {
                            legend: { display: false }
                        },
                        scales: {
                            x: {
                                max: 100,
                                ticks: {
                                    callback: function(value) {
                                        return value + '%';
                                    }
                                }
                            }
                        }
                    }
                });
            })
            .catch(error => console.error('Error loading department spending chart:', error));
    }

    // Forecast Chart
    function loadForecastChart() {
        axios.get('/api/analytics/forecast')
            .then(response => {
                const data = response.data;
                
                if (charts.forecast) charts.forecast.destroy();
                
                const ctx = document.getElementById('forecastChart').getContext('2d');
                charts.forecast = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: data.labels,
                        datasets: [
                            {
                                label: 'Historical Data ($)',
                                data: data.historical,
                                borderColor: '#06b6d4',
                                backgroundColor: 'rgba(6, 182, 212, 0.1)',
                                tension: 0.4,
                                fill: true,
                                borderWidth: 2,
                            },
                            {
                                label: 'Forecast ($)',
                                data: data.forecast,
                                borderColor: '#8b5cf6',
                                borderDash: [5, 5],
                                backgroundColor: 'rgba(139, 92, 246, 0.1)',
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
            .catch(error => console.error('Error loading forecast chart:', error));
    }

    // Approval Rate Chart
    function loadApprovalRateChart() {
        axios.get('/api/analytics/approval-rate')
            .then(response => {
                const data = response.data;
                
                if (charts.approvalRate) charts.approvalRate.destroy();
                
                const ctx = document.getElementById('approvalRateChart').getContext('2d');
                charts.approvalRate = new Chart(ctx, {
                    type: 'pie',
                    data: {
                        labels: data.labels,
                        datasets: [{
                            data: data.data,
                            backgroundColor: data.colors,
                            borderColor: '#fff',
                            borderWidth: 2,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: { padding: 20, font: { size: 12 } }
                            }
                        }
                    }
                });
            })
            .catch(error => console.error('Error loading approval rate chart:', error));
    }

    // Top Departments Chart
    function loadTopDepartmentsChart() {
        axios.get('/api/analytics/top-departments')
            .then(response => {
                const data = response.data;
                
                if (charts.topDepts) charts.topDepts.destroy();
                
                const ctx = document.getElementById('topDepartmentsChart').getContext('2d');
                charts.topDepts = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: data.labels,
                        datasets: [{
                            label: 'Budget ($)',
                            data: data.budgets,
                            backgroundColor: '#fbbf24',
                            borderColor: '#d97706',
                            borderWidth: 1,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        indexAxis: 'y',
                        plugins: {
                            legend: { display: false }
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
            .catch(error => console.error('Error loading top departments chart:', error));
    }

    // Refresh data every 30 seconds
    setInterval(() => {
        loadOverview();
        loadRequestStatusChart();
        loadBudgetByDepartmentChart();
        loadSpendingTrendsChart();
        loadAllocationVsSpendingChart();
        loadDepartmentSpendingChart();
        loadForecastChart();
        loadApprovalRateChart();
        loadTopDepartmentsChart();
    }, 30000);
</script>
@endsection

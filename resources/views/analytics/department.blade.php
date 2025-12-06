@extends('layouts.app')

@section('content')
@include('components.sidebar')

<div class="min-h-screen">
    <div class="lg:ml-64">
        <div class="lg:hidden h-16"></div>
        <div class="py-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white bg-opacity-90 backdrop-blur-lg overflow-hidden shadow-xl rounded-2xl">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between items-center mb-6">
                        <h1 class="text-3xl font-bold">Budget Analysis</h1>
                        <div class="flex gap-3">
                            <button onclick="exportToPDF()" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                                <i class="fas fa-file-pdf mr-2"></i>Export PDF
                            </button>
                            <button onclick="exportToExcel()" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                                <i class="fas fa-file-excel mr-2"></i>Export Excel
                            </button>
                            <a href="/dashboard" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                <i class="fas fa-arrow-left mr-2"></i>Back
                            </a>
                        </div>
                    </div>

                    <!-- Analysis Overview -->
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                        <!-- Total Allocated -->
                        <div class="bg-white rounded-lg border border-gray-200 p-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-gray-500 text-sm">Total Allocated</p>
                                    <p class="text-3xl font-bold text-gray-800" id="totalAllocated">$0</p>
                                </div>
                                <i class="fas fa-wallet text-4xl text-blue-500 opacity-20"></i>
                            </div>
                        </div>

                        <!-- Total Spent -->
                        <div class="bg-white rounded-lg border border-gray-200 p-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-gray-500 text-sm">Total Spent</p>
                                    <p class="text-3xl font-bold text-gray-800" id="totalSpent">$0</p>
                                </div>
                                <i class="fas fa-credit-card text-4xl text-red-500 opacity-20"></i>
                            </div>
                        </div>

                        <!-- Remaining Balance -->
                        <div class="bg-white rounded-lg border border-gray-200 p-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-gray-500 text-sm">Remaining Balance</p>
                                    <p class="text-3xl font-bold text-gray-800" id="remainingBalance">$0</p>
                                </div>
                                <i class="fas fa-piggy-bank text-4xl text-green-500 opacity-20"></i>
                            </div>
                        </div>

                        <!-- Spending Percentage -->
                        <div class="bg-white rounded-lg border border-gray-200 p-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-gray-500 text-sm">Budget Used</p>
                                    <p class="text-3xl font-bold text-gray-800" id="spendingPercentage">0%</p>
                                </div>
                                <i class="fas fa-chart-pie text-4xl text-yellow-500 opacity-20"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Fiscal Year Filter -->
                    <div class="bg-white rounded-lg border border-gray-200 p-6 mb-8">
                        <h3 class="text-xl font-bold text-gray-800 mb-4">Filter by Fiscal Year</h3>
                        <div class="flex gap-4 items-end">
                            <div>
                                <label for="fiscalYearFilter" class="block text-sm font-medium text-gray-700 mb-2">
                                    Select Fiscal Year
                                </label>
                                <select id="fiscalYearFilter" class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="">-- All Years --</option>
                                </select>
                            </div>
                            <button onclick="loadAnalytics()" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded">
                                <i class="fas fa-search mr-2"></i>Filter
                            </button>
                        </div>
                    </div>

                    <!-- Charts Row -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                        <!-- Spending Breakdown Pie Chart -->
                        <div class="bg-white rounded-lg border border-gray-200 p-6">
                            <h3 class="text-xl font-bold text-gray-800 mb-4">Spending Breakdown</h3>
                            <div style="position: relative; height: 300px;">
                                <canvas id="spendingChart"></canvas>
                            </div>
                        </div>

                        <!-- Budget Utilization Doughnut Chart -->
                        <div class="bg-white rounded-lg border border-gray-200 p-6">
                            <h3 class="text-xl font-bold text-gray-800 mb-4">Budget Utilization</h3>
                            <div style="position: relative; height: 300px;">
                                <canvas id="utilizationChart"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- Allocations Table -->
                    <div class="bg-white rounded-lg border border-gray-200 p-6 mb-8">
                        <h3 class="text-xl font-bold text-gray-800 mb-4">Budget Allocations</h3>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left">
                                <thead class="bg-gray-100">
                                    <tr>
                                        <th class="px-4 py-2 font-semibold text-gray-700">Fiscal Year</th>
                                        <th class="px-4 py-2 font-semibold text-gray-700">Allocated</th>
                                        <th class="px-4 py-2 font-semibold text-gray-700">Spent</th>
                                        <th class="px-4 py-2 font-semibold text-gray-700">Remaining</th>
                                        <th class="px-4 py-2 font-semibold text-gray-700">Usage %</th>
                                        <th class="px-4 py-2 font-semibold text-gray-700">Status</th>
                                    </tr>
                                </thead>
                                <tbody id="allocationsTableBody">
                                    <tr class="border-b hover:bg-gray-50">
                                        <td colspan="6" class="px-4 py-2 text-center text-gray-500">Loading allocations...</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.js"></script>
<script>
    let spendingChartInstance = null;
    let utilizationChartInstance = null;

    function loadAnalytics() {
        const token = localStorage.getItem('api_token');
        if (!token) {
            console.log('No token available');
            return;
        }

        const fiscalYearFilter = document.getElementById('fiscalYearFilter').value;

        // Load allocations
        axios.get('/api/budget-allocations', {
            headers: { 'Authorization': 'Bearer ' + token }
        })
        .then(response => {
            const allocations = response.data.data || [];
            console.log('Allocations:', allocations);

            // Populate fiscal year filter
            const years = [...new Set(allocations.map(a => a.fiscal_year))].sort().reverse();
            const fiscalYearSelect = document.getElementById('fiscalYearFilter');
            years.forEach(year => {
                if (!Array.from(fiscalYearSelect.options).some(opt => opt.value === year.toString())) {
                    const option = document.createElement('option');
                    option.value = year;
                    option.textContent = year;
                    fiscalYearSelect.appendChild(option);
                }
            });

            // Filter allocations
            let filtered = allocations;
            if (fiscalYearFilter) {
                filtered = allocations.filter(a => a.fiscal_year.toString() === fiscalYearFilter.toString());
            }

            // Calculate totals
            const totals = {
                allocated: filtered.reduce((sum, a) => sum + parseFloat(a.allocated_amount || 0), 0),
                spent: filtered.reduce((sum, a) => sum + parseFloat(a.spent_amount || 0), 0),
                remaining: filtered.reduce((sum, a) => sum + parseFloat(a.remaining_balance || 0), 0),
            };

            const percentage = totals.allocated > 0 
                ? ((totals.spent / totals.allocated) * 100).toFixed(1)
                : 0;

            // Update summary cards
            document.getElementById('totalAllocated').textContent = `₱${totals.allocated.toLocaleString('en-US', {maximumFractionDigits: 0})}`;
            document.getElementById('totalSpent').textContent = `₱${totals.spent.toLocaleString('en-US', {maximumFractionDigits: 0})}`;
            document.getElementById('remainingBalance').textContent = `₱${totals.remaining.toLocaleString('en-US', {maximumFractionDigits: 0})}`;
            document.getElementById('spendingPercentage').textContent = `${percentage}%`;

            // Update spending breakdown chart
            updateSpendingChart(totals);

            // Update utilization chart
            updateUtilizationChart(percentage, (100 - percentage));

            // Populate allocations table
            const tbody = document.getElementById('allocationsTableBody');
            if (filtered.length === 0) {
                tbody.innerHTML = '<tr><td colspan="6" class="px-4 py-2 text-center text-gray-500">No allocations found</td></tr>';
                return;
            }

            tbody.innerHTML = filtered.map(alloc => {
                const usage = alloc.allocated_amount > 0 
                    ? ((alloc.spent_amount / alloc.allocated_amount) * 100).toFixed(1)
                    : 0;
                const statusColor = alloc.status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800';
                
                return `
                    <tr class="border-b hover:bg-gray-50">
                        <td class="px-4 py-2 font-medium">${alloc.fiscal_year}</td>
                        <td class="px-4 py-2">₱${parseFloat(alloc.allocated_amount || 0).toLocaleString('en-US', {maximumFractionDigits: 0})}</td>
                        <td class="px-4 py-2">₱${parseFloat(alloc.spent_amount || 0).toLocaleString('en-US', {maximumFractionDigits: 0})}</td>
                        <td class="px-4 py-2">₱${parseFloat(alloc.remaining_balance || 0).toLocaleString('en-US', {maximumFractionDigits: 0})}</td>
                        <td class="px-4 py-2">
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-blue-600 h-2 rounded-full" style="width: ${usage}%"></div>
                            </div>
                            <span class="text-sm text-gray-600">${usage}%</span>
                        </td>
                        <td class="px-4 py-2"><span class="${statusColor} px-2 py-1 rounded text-sm font-semibold">${alloc.status}</span></td>
                    </tr>
                `;
            }).join('');
        })
        .catch(err => {
            console.error('Error loading analytics:', err);
            document.getElementById('allocationsTableBody').innerHTML = '<tr><td colspan="6" class="px-4 py-2 text-center text-red-500">Error loading data</td></tr>';
        });
    }

    function updateSpendingChart(totals) {
        const ctx = document.getElementById('spendingChart').getContext('2d');
        
        if (spendingChartInstance) {
            spendingChartInstance.destroy();
        }

        spendingChartInstance = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: ['Spent', 'Remaining'],
                datasets: [{
                    data: [totals.spent, totals.remaining],
                    backgroundColor: [
                        '#ef4444',
                        '#10b981'
                    ],
                    borderColor: ['#fff', '#fff'],
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return '₱' + context.parsed.toLocaleString('en-US', {maximumFractionDigits: 0});
                            }
                        }
                    }
                }
            }
        });
    }

    function updateUtilizationChart(spent, remaining) {
        const ctx = document.getElementById('utilizationChart').getContext('2d');
        
        if (utilizationChartInstance) {
            utilizationChartInstance.destroy();
        }

        utilizationChartInstance = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Used', 'Available'],
                datasets: [{
                    data: [spent, remaining],
                    backgroundColor: [
                        '#3b82f6',
                        '#e5e7eb'
                    ],
                    borderColor: ['#fff', '#fff'],
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.label + ': ' + context.parsed.toFixed(1) + '%';
                            }
                        }
                    }
                }
            }
        });
    }

    // Export to PDF
    function exportToPDF() {
        window.print();
    }

    // Export to Excel (CSV format)
    function exportToExcel() {
        const fiscalYear = document.getElementById('fiscalYearFilter').value || 'All Years';
        const timestamp = new Date().toISOString().split('T')[0];
        
        // Collect data
        let csv = 'Budget Analysis Report\n';
        csv += `Fiscal Year: ${fiscalYear}\n`;
        csv += `Generated: ${timestamp}\n\n`;
        
        // Summary data
        csv += 'Summary\n';
        csv += 'Metric,Amount\n';
        csv += `Total Allocated,${document.getElementById('totalAllocated').textContent}\n`;
        csv += `Total Spent,${document.getElementById('totalSpent').textContent}\n`;
        csv += `Remaining Balance,${document.getElementById('remainingBalance').textContent}\n`;
        csv += `Budget Used,${document.getElementById('spendingPercentage').textContent}\n\n`;
        
        // Budget allocations table
        csv += 'Budget Allocations\n';
        csv += 'Fiscal Year,Allocated Amount,Spent Amount,Remaining Balance,Status\n';
        
        const table = document.querySelector('table tbody');
        if (table) {
            const rows = table.querySelectorAll('tr');
            rows.forEach(row => {
                const cols = row.querySelectorAll('td');
                if (cols.length >= 5) {
                    csv += `${cols[0].textContent.trim()},`;
                    csv += `${cols[1].textContent.trim()},`;
                    csv += `${cols[2].textContent.trim()},`;
                    csv += `${cols[3].textContent.trim()},`;
                    csv += `${cols[4].textContent.trim()}\n`;
                }
            });
        }
        
        // Download
        const blob = new Blob([csv], { type: 'text/csv' });
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = `budget-analysis-${timestamp}.csv`;
        a.click();
        window.URL.revokeObjectURL(url);
    }

    // Load on page init
    document.addEventListener('DOMContentLoaded', function() {
        const token = localStorage.getItem('api_token');
        if (token) {
            loadAnalytics();
        }
    });

    window.addEventListener('tokenReady', loadAnalytics);
</script>
@endsection


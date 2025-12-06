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
                        <h1 class="text-3xl font-bold">Budget Analysis - All Departments</h1>
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

                    <!-- Filters -->
                    <div class="bg-white rounded-lg border border-gray-200 p-6 mb-8">
                        <h3 class="text-xl font-bold text-gray-800 mb-4">Filters</h3>
                        <div class="flex gap-4 items-end flex-wrap">
                            <div>
                                <label for="adminFiscalYearFilter" class="block text-sm font-medium text-gray-700 mb-2">
                                    Fiscal Year
                                </label>
                                <select id="adminFiscalYearFilter" class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="">-- All Years --</option>
                                </select>
                            </div>
                            <div>
                                <label for="adminDepartmentFilter" class="block text-sm font-medium text-gray-700 mb-2">
                                    Department
                                </label>
                                <select id="adminDepartmentFilter" class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="">-- All Departments --</option>
                                </select>
                            </div>
                            <button onclick="loadAdminAnalytics()" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded">
                                <i class="fas fa-search mr-2"></i>Filter
                            </button>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                        <!-- Total Budget -->
                        <div class="bg-white rounded-lg border border-gray-200 p-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-gray-500 text-sm">Total Budget</p>
                                    <p class="text-3xl font-bold text-gray-800" id="totalBudget">$0</p>
                                </div>
                                <i class="fas fa-wallet text-4xl text-blue-500 opacity-20"></i>
                            </div>
                        </div>

                        <!-- Total Spent -->
                        <div class="bg-white rounded-lg border border-gray-200 p-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-gray-500 text-sm">Total Spent</p>
                                    <p class="text-3xl font-bold text-gray-800" id="adminTotalSpent">$0</p>
                                </div>
                                <i class="fas fa-credit-card text-4xl text-red-500 opacity-20"></i>
                            </div>
                        </div>

                        <!-- Total Remaining -->
                        <div class="bg-white rounded-lg border border-gray-200 p-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-gray-500 text-sm">Total Remaining</p>
                                    <p class="text-3xl font-bold text-gray-800" id="adminTotalRemaining">$0</p>
                                </div>
                                <i class="fas fa-piggy-bank text-4xl text-green-500 opacity-20"></i>
                            </div>
                        </div>

                        <!-- Departments Count -->
                        <div class="bg-white rounded-lg border border-gray-200 p-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-gray-500 text-sm">Departments</p>
                                    <p class="text-3xl font-bold text-gray-800" id="departmentsCount">0</p>
                                </div>
                                <i class="fas fa-building text-4xl text-purple-500 opacity-20"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Charts Row -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                        <!-- Overall Spending Breakdown -->
                        <div class="bg-white rounded-lg border border-gray-200 p-6">
                            <h3 class="text-xl font-bold text-gray-800 mb-4">Spending Breakdown</h3>
                            <div style="position: relative; height: 300px;">
                                <canvas id="adminSpendingChart"></canvas>
                            </div>
                        </div>

                        <!-- Budget Utilization -->
                        <div class="bg-white rounded-lg border border-gray-200 p-6">
                            <h3 class="text-xl font-bold text-gray-800 mb-4">Overall Utilization</h3>
                            <div style="position: relative; height: 300px;">
                                <canvas id="adminUtilizationChart"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- Department Comparison Chart -->
                    <div class="bg-white rounded-lg border border-gray-200 p-6 mb-8">
                        <h3 class="text-xl font-bold text-gray-800 mb-4">Budget by Department</h3>
                        <div style="position: relative; height: 350px;">
                            <canvas id="departmentComparisonChart"></canvas>
                        </div>
                    </div>

                    <!-- Allocations by Department -->
                    <div class="bg-white rounded-lg border border-gray-200 p-6 mb-8">
                        <h3 class="text-xl font-bold text-gray-800 mb-4">Budget Allocations by Department</h3>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left">
                                <thead class="bg-gray-100">
                                    <tr>
                                        <th class="px-4 py-2 font-semibold text-gray-700">Department</th>
                                        <th class="px-4 py-2 font-semibold text-gray-700">Fiscal Year</th>
                                        <th class="px-4 py-2 font-semibold text-gray-700">Allocated</th>
                                        <th class="px-4 py-2 font-semibold text-gray-700">Spent</th>
                                        <th class="px-4 py-2 font-semibold text-gray-700">Remaining</th>
                                        <th class="px-4 py-2 font-semibold text-gray-700">Usage %</th>
                                        <th class="px-4 py-2 font-semibold text-gray-700">Status</th>
                                    </tr>
                                </thead>
                                <tbody id="adminAllocationsTableBody">
                                    <tr class="border-b hover:bg-gray-50">
                                        <td colspan="7" class="px-4 py-2 text-center text-gray-500">Loading allocations...</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Department Summary -->
                    <div class="bg-white rounded-lg border border-gray-200 p-6">
                        <h3 class="text-xl font-bold text-gray-800 mb-4">Department Summary</h3>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left">
                                <thead class="bg-gray-100">
                                    <tr>
                                        <th class="px-4 py-2 font-semibold text-gray-700">Department</th>
                                        <th class="px-4 py-2 font-semibold text-gray-700">Total Allocated</th>
                                        <th class="px-4 py-2 font-semibold text-gray-700">Total Spent</th>
                                        <th class="px-4 py-2 font-semibold text-gray-700">Total Remaining</th>
                                        <th class="px-4 py-2 font-semibold text-gray-700">Avg Usage %</th>
                                    </tr>
                                </thead>
                                <tbody id="departmentSummaryBody">
                                    <tr class="border-b hover:bg-gray-50">
                                        <td colspan="5" class="px-4 py-2 text-center text-gray-500">Loading summary...</td>
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
    let adminSpendingChartInstance = null;
    let adminUtilizationChartInstance = null;
    let departmentComparisonChartInstance = null;

    function loadAdminAnalytics() {
        const token = localStorage.getItem('api_token');
        if (!token) {
            console.log('No token available');
            return;
        }

        const fiscalYearFilter = document.getElementById('adminFiscalYearFilter').value;
        const departmentFilter = document.getElementById('adminDepartmentFilter').value;

        // Load allocations
        axios.get('/api/budget-allocations', {
            headers: { 'Authorization': 'Bearer ' + token }
        })
        .then(response => {
            const allocations = response.data.data || [];
            console.log('Allocations:', allocations);

            // Populate filters
            const years = [...new Set(allocations.map(a => a.fiscal_year))].sort().reverse();
            const departments = [...new Set(allocations.map(a => a.department?.name || 'Unknown'))].sort();
            
            const fiscalYearSelect = document.getElementById('adminFiscalYearFilter');
            const departmentSelect = document.getElementById('adminDepartmentFilter');

            years.forEach(year => {
                if (!Array.from(fiscalYearSelect.options).some(opt => opt.value === year.toString())) {
                    const option = document.createElement('option');
                    option.value = year;
                    option.textContent = year;
                    fiscalYearSelect.appendChild(option);
                }
            });

            departments.forEach(dept => {
                if (!Array.from(departmentSelect.options).some(opt => opt.value === dept)) {
                    const option = document.createElement('option');
                    option.value = dept;
                    option.textContent = dept;
                    departmentSelect.appendChild(option);
                }
            });

            // Filter allocations
            let filtered = allocations;
            if (fiscalYearFilter) {
                filtered = filtered.filter(a => a.fiscal_year.toString() === fiscalYearFilter.toString());
            }
            if (departmentFilter) {
                filtered = filtered.filter(a => (a.department?.name || 'Unknown') === departmentFilter);
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
            document.getElementById('totalBudget').textContent = `₱${totals.allocated.toLocaleString('en-US', {maximumFractionDigits: 0})}`;
            document.getElementById('adminTotalSpent').textContent = `₱${totals.spent.toLocaleString('en-US', {maximumFractionDigits: 0})}`;
            document.getElementById('adminTotalRemaining').textContent = `₱${totals.remaining.toLocaleString('en-US', {maximumFractionDigits: 0})}`;
            document.getElementById('departmentsCount').textContent = [...new Set(filtered.map(a => a.department_id))].length;

            // Update charts
            updateAdminSpendingChart(totals);
            updateAdminUtilizationChart(percentage, (100 - percentage));
            updateDepartmentComparisonChart(filtered);

            // Populate allocations table
            const tbody = document.getElementById('adminAllocationsTableBody');
            if (filtered.length === 0) {
                tbody.innerHTML = '<tr><td colspan="7" class="px-4 py-2 text-center text-gray-500">No allocations found</td></tr>';
            } else {
                tbody.innerHTML = filtered.map(alloc => {
                    const usage = alloc.allocated_amount > 0 
                        ? ((alloc.spent_amount / alloc.allocated_amount) * 100).toFixed(1)
                        : 0;
                    const statusColor = alloc.status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800';
                    
                    return `
                        <tr class="border-b hover:bg-gray-50">
                            <td class="px-4 py-2 font-medium">${alloc.department?.name || 'Unknown'}</td>
                            <td class="px-4 py-2">${alloc.fiscal_year}</td>
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
            }

            // Populate department summary
            const summaryBody = document.getElementById('departmentSummaryBody');
            const departmentSummary = {};
            
            filtered.forEach(alloc => {
                const deptName = alloc.department?.name || 'Unknown';
                if (!departmentSummary[deptName]) {
                    departmentSummary[deptName] = {
                        allocated: 0,
                        spent: 0,
                        remaining: 0,
                        count: 0,
                    };
                }
                departmentSummary[deptName].allocated += parseFloat(alloc.allocated_amount || 0);
                departmentSummary[deptName].spent += parseFloat(alloc.spent_amount || 0);
                departmentSummary[deptName].remaining += parseFloat(alloc.remaining_balance || 0);
                departmentSummary[deptName].count += 1;
            });

            if (Object.keys(departmentSummary).length === 0) {
                summaryBody.innerHTML = '<tr><td colspan="5" class="px-4 py-2 text-center text-gray-500">No allocations found</td></tr>';
            } else {
                summaryBody.innerHTML = Object.entries(departmentSummary).map(([dept, data]) => {
                    const avgUsage = data.allocated > 0 
                        ? ((data.spent / data.allocated) * 100).toFixed(1)
                        : 0;
                    
                    return `
                        <tr class="border-b hover:bg-gray-50">
                            <td class="px-4 py-2 font-medium">${dept}</td>
                            <td class="px-4 py-2">₱${data.allocated.toLocaleString('en-US', {maximumFractionDigits: 0})}</td>
                            <td class="px-4 py-2">₱${data.spent.toLocaleString('en-US', {maximumFractionDigits: 0})}</td>
                            <td class="px-4 py-2">₱${data.remaining.toLocaleString('en-US', {maximumFractionDigits: 0})}</td>
                            <td class="px-4 py-2">${avgUsage}%</td>
                        </tr>
                    `;
                }).join('');
            }
        })
        .catch(err => {
            console.error('Error loading analytics:', err);
            document.getElementById('adminAllocationsTableBody').innerHTML = '<tr><td colspan="7" class="px-4 py-2 text-center text-red-500">Error loading data</td></tr>';
        });
    }

    function updateAdminSpendingChart(totals) {
        const ctx = document.getElementById('adminSpendingChart').getContext('2d');
        
        if (adminSpendingChartInstance) {
            adminSpendingChartInstance.destroy();
        }

        adminSpendingChartInstance = new Chart(ctx, {
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

    function updateAdminUtilizationChart(spent, remaining) {
        const ctx = document.getElementById('adminUtilizationChart').getContext('2d');
        
        if (adminUtilizationChartInstance) {
            adminUtilizationChartInstance.destroy();
        }

        adminUtilizationChartInstance = new Chart(ctx, {
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

    function updateDepartmentComparisonChart(allocations) {
        const ctx = document.getElementById('departmentComparisonChart').getContext('2d');
        
        if (departmentComparisonChartInstance) {
            departmentComparisonChartInstance.destroy();
        }

        // Group by department
        const departmentData = {};
        allocations.forEach(alloc => {
            const deptName = alloc.department?.name || 'Unknown';
            if (!departmentData[deptName]) {
                departmentData[deptName] = {
                    allocated: 0,
                    spent: 0
                };
            }
            departmentData[deptName].allocated += parseFloat(alloc.allocated_amount || 0);
            departmentData[deptName].spent += parseFloat(alloc.spent_amount || 0);
        });

        const labels = Object.keys(departmentData);
        const allocatedData = labels.map(dept => departmentData[dept].allocated);
        const spentData = labels.map(dept => departmentData[dept].spent);

        departmentComparisonChartInstance = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Allocated',
                        data: allocatedData,
                        backgroundColor: '#3b82f6',
                        borderColor: '#1e40af',
                        borderWidth: 1
                    },
                    {
                        label: 'Spent',
                        data: spentData,
                        backgroundColor: '#ef4444',
                        borderColor: '#991b1b',
                        borderWidth: 1
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top'
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label + ': ₱' + context.parsed.y.toLocaleString('en-US', {maximumFractionDigits: 0});
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return '₱' + value.toLocaleString('en-US', {maximumFractionDigits: 0});
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
        const fiscalYear = document.getElementById('adminFiscalYearFilter').value || 'All Years';
        const department = document.getElementById('adminDepartmentFilter').value || 'All Departments';
        const timestamp = new Date().toISOString().split('T')[0];
        
        // Collect data
        let csv = 'Budget Analysis Report - All Departments\n';
        csv += `Fiscal Year: ${fiscalYear}\n`;
        csv += `Department: ${department}\n`;
        csv += `Generated: ${timestamp}\n\n`;
        
        // Summary data
        csv += 'System Overview\n';
        csv += 'Metric,Amount\n';
        csv += `Total Budget,${document.getElementById('totalBudget').textContent}\n`;
        csv += `Total Spent,${document.getElementById('totalSpent').textContent}\n`;
        csv += `Remaining Budget,${document.getElementById('remainingBudget').textContent}\n`;
        csv += `Active Departments,${document.getElementById('departmentsCount').textContent}\n\n`;
        
        // Budget allocations table
        csv += 'Budget Allocations\n';
        csv += 'Fiscal Year,Allocated Amount,Spent Amount,Remaining Balance,Status\n';
        
        const allocTable = document.getElementById('allocationsTableBody');
        if (allocTable) {
            const rows = allocTable.querySelectorAll('tr');
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
        
        csv += '\n';
        
        // Department summary table
        csv += 'Department Summary\n';
        csv += 'Department,Total Allocated,Total Spent,Remaining Balance,Utilization Rate\n';
        
        const deptTable = document.getElementById('departmentSummaryBody');
        if (deptTable) {
            const rows = deptTable.querySelectorAll('tr');
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
        a.download = `admin-budget-analysis-${timestamp}.csv`;
        a.click();
        window.URL.revokeObjectURL(url);
    }

    // Load on page init
    document.addEventListener('DOMContentLoaded', function() {
        const token = localStorage.getItem('api_token');
        if (token) {
            loadAdminAnalytics();
        }
    });

    window.addEventListener('tokenReady', loadAdminAnalytics);
</script>
@endsection

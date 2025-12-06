@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4">
        <!-- Header -->
        <div class="mb-8 flex justify-between items-start">
            <div>
                <h1 class="text-4xl font-bold text-gray-900 mb-2">
                    <i class="fas fa-file-invoice-dollar text-green-600 mr-3"></i>Financial Report
                </h1>
                <p class="text-gray-600">Comprehensive financial analysis and department-wise breakdown</p>
            </div>
            <a href="{{ route('analytics.dashboard') }}" class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                <i class="fas fa-arrow-left mr-2"></i>Back to Dashboard
            </a>
        </div>

        <!-- Summary Section -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow p-6 border-l-4 border-blue-500">
                <h3 class="text-gray-500 text-sm font-semibold uppercase mb-2">Total Budget Allocated</h3>
                <p class="text-3xl font-bold text-gray-900" id="summaryTotalBudget">$0</p>
            </div>

            <div class="bg-white rounded-lg shadow p-6 border-l-4 border-orange-500">
                <h3 class="text-gray-500 text-sm font-semibold uppercase mb-2">Total Budget Spent</h3>
                <p class="text-3xl font-bold text-gray-900" id="summarySpentBudget">$0</p>
            </div>

            <div class="bg-white rounded-lg shadow p-6 border-l-4 border-green-500">
                <h3 class="text-gray-500 text-sm font-semibold uppercase mb-2">Total Remaining</h3>
                <p class="text-3xl font-bold text-gray-900" id="summaryRemainingBudget">$0</p>
            </div>
        </div>

        <!-- Department-wise Breakdown -->
        <div class="bg-white rounded-lg shadow p-6 mb-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">
                <i class="fas fa-building text-blue-600 mr-2"></i>Department-wise Financial Breakdown
            </h2>
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-900 uppercase">Department</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-900 uppercase">Allocated</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-900 uppercase">Spent</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-900 uppercase">Remaining</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-900 uppercase">Spending %</th>
                        </tr>
                    </thead>
                    <tbody id="departmentTableBody" class="divide-y divide-gray-200">
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-gray-500">Loading...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Request Status Breakdown -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">
                <i class="fas fa-list-check text-purple-600 mr-2"></i>Request Status Summary
            </h2>
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-900 uppercase">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-900 uppercase">Count</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-900 uppercase">Total Amount</th>
                        </tr>
                    </thead>
                    <tbody id="statusTableBody" class="divide-y divide-gray-200">
                        <tr>
                            <td colspan="3" class="px-6 py-4 text-center text-gray-500">Loading...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    function formatCurrency(value) {
        return new Intl.NumberFormat('en-US', {
            style: 'currency',
            currency: 'USD',
            minimumFractionDigits: 0,
            maximumFractionDigits: 0,
        }).format(value);
    }

    document.addEventListener('DOMContentLoaded', function() {
        axios.get('/api/analytics/financial-report')
            .then(response => {
                const data = response.data;

                // Update Summary
                document.getElementById('summaryTotalBudget').textContent = formatCurrency(data.summary.total_budget);
                document.getElementById('summarySpentBudget').textContent = formatCurrency(data.summary.total_spent);
                document.getElementById('summaryRemainingBudget').textContent = formatCurrency(data.summary.total_remaining);

                // Populate Department Table
                const deptTableBody = document.getElementById('departmentTableBody');
                deptTableBody.innerHTML = '';

                if (data.by_department.length === 0) {
                    deptTableBody.innerHTML = '<tr><td colspan="5" class="px-6 py-4 text-center text-gray-500">No data available</td></tr>';
                } else {
                    data.by_department.forEach(dept => {
                        const row = `
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 text-sm font-medium text-gray-900">${dept.department}</td>
                                <td class="px-6 py-4 text-sm text-gray-600">${formatCurrency(dept.allocated)}</td>
                                <td class="px-6 py-4 text-sm text-gray-600">${formatCurrency(dept.spent)}</td>
                                <td class="px-6 py-4 text-sm text-gray-600">${formatCurrency(dept.remaining)}</td>
                                <td class="px-6 py-4 text-sm">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium ${dept.percentage >= 80 ? 'bg-red-100 text-red-800' : dept.percentage >= 50 ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800'}">
                                        ${dept.percentage}%
                                    </span>
                                </td>
                            </tr>
                        `;
                        deptTableBody.innerHTML += row;
                    });
                }

                // Populate Status Table
                const statusTableBody = document.getElementById('statusTableBody');
                statusTableBody.innerHTML = '';

                if (data.by_status.length === 0) {
                    statusTableBody.innerHTML = '<tr><td colspan="3" class="px-6 py-4 text-center text-gray-500">No data available</td></tr>';
                } else {
                    data.by_status.forEach(status => {
                        const row = `
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 text-sm font-medium text-gray-900">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                        ${status.status}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600"><strong>${status.count}</strong></td>
                                <td class="px-6 py-4 text-sm text-gray-600">${formatCurrency(status.amount)}</td>
                            </tr>
                        `;
                        statusTableBody.innerHTML += row;
                    });
                }
            })
            .catch(error => {
                console.error('Error loading financial report:', error);
                document.getElementById('departmentTableBody').innerHTML = '<tr><td colspan="5" class="px-6 py-4 text-center text-red-500">Error loading data</td></tr>';
                document.getElementById('statusTableBody').innerHTML = '<tr><td colspan="3" class="px-6 py-4 text-center text-red-500">Error loading data</td></tr>';
            });
    });
</script>
@endsection

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
                        <h1 class="text-3xl font-bold">Budget Allocations</h1>
                        <a href="/budget-allocations/create" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            <i class="fas fa-plus mr-2"></i>Create Allocation
                        </a>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 border border-gray-300">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Department</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fiscal Year</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Allocated Amount</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Spent Amount</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Remaining</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Utilization %</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200" id="allocationsTable">
                                <tr>
                                    <td colspan="7" class="px-6 py-4 text-center text-gray-500">Loading budget allocations...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function loadBudgetAllocations() {
        let apiToken = localStorage.getItem('api_token');
        const tbody = document.getElementById('allocationsTable');
        
        if (!apiToken) {
            tbody.innerHTML = '<tr><td colspan="7" class="px-6 py-4 text-center text-orange-500"><i class="fas fa-exclamation-triangle mr-2"></i>Please refresh the page or log in again to load allocations</td></tr>';
            return;
        }
        
        // Load budget allocations via API
        axios.get('/api/budget-allocations', {
            headers: {
                'Authorization': 'Bearer ' + apiToken
            }
        })
        .then(response => {
            const allocations = response.data.data || [];
            
            if (allocations.length === 0) {
                tbody.innerHTML = '<tr><td colspan="7" class="px-6 py-4 text-center text-gray-500">No budget allocations found</td></tr>';
                return;
            }

            tbody.innerHTML = allocations.map(alloc => {
                const utilization = alloc.allocated_amount > 0 ? Math.round((alloc.spent_amount / alloc.allocated_amount) * 100) : 0;
                const statusClass = utilization > 80 ? 'bg-red-100 text-red-800' : utilization > 50 ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800';
                
                return `
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">${alloc.department?.name || 'N/A'}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${alloc.fiscal_year}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">₱${parseFloat(alloc.allocated_amount).toLocaleString()}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">₱${parseFloat(alloc.spent_amount).toLocaleString()}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">₱${parseFloat(alloc.allocated_amount - alloc.spent_amount).toLocaleString()}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${utilization}%</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${statusClass}">
                                ${utilization > 80 ? 'Critical' : utilization > 50 ? 'High' : 'Optimal'}
                            </span>
                        </td>
                    </tr>
                `;
            }).join('');
        })
        .catch(error => {
            console.error('Error loading allocations:', error);
            tbody.innerHTML = '<tr><td colspan="7" class="px-6 py-4 text-center text-red-500"><i class="fas fa-exclamation-circle mr-2"></i>Error loading budget allocations. Please try again.</td></tr>';
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Wait for token to be set from layout
        setTimeout(loadBudgetAllocations, 100);
    });
    
    // Also listen for tokenReady event
    window.addEventListener('tokenReady', loadBudgetAllocations);
</script>
@endsection

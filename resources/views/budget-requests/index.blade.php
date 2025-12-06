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
                        <h1 class="text-3xl font-bold">Budget Requests</h1>
                        <a href="{{ route('budget-requests.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            <i class="fas fa-plus mr-2"></i>New Budget Request
                        </a>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 border border-gray-300">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Department</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created At</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200" id="budgetRequestsTable">
                                <tr>
                                    <td colspan="6" class="px-6 py-4 text-center text-gray-500">Loading budget requests...</td>
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
    function loadBudgetRequests() {
        let apiToken = localStorage.getItem('api_token');
        const tbody = document.getElementById('budgetRequestsTable');
        
        if (!apiToken) {
            tbody.innerHTML = '<tr><td colspan="6" class="px-6 py-4 text-center text-orange-500"><i class="fas fa-exclamation-triangle mr-2"></i>Please refresh the page or log in again to load budget requests</td></tr>';
            return;
        }
        
        // Load budget requests via API
        axios.get('/api/budget-requests', {
            headers: {
                'Authorization': 'Bearer ' + apiToken
            }
        })
        .then(response => {
            const requests = response.data.data || [];
            
            if (requests.length === 0) {
                tbody.innerHTML = '<tr><td colspan="6" class="px-6 py-4 text-center text-gray-500">No budget requests found</td></tr>';
                return;
            }

            tbody.innerHTML = requests.map(req => `
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">${req.title}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">₱${parseFloat(req.amount || req.requested_amount || 0).toLocaleString()}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${getStatusBadgeClass(req.status)}">
                            ${req.status}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${req.department?.name || 'N/A'}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${new Date(req.created_at).toLocaleDateString()}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <a href="/budget-requests/${req.id}" class="text-blue-600 hover:text-blue-900">View</a>
                    </td>
                </tr>
            `).join('');
        })
        .catch(error => {
            console.error('Error loading budget requests:', error);
            tbody.innerHTML = '<tr><td colspan="6" class="px-6 py-4 text-center text-red-500"><i class="fas fa-exclamation-circle mr-2"></i>Error loading budget requests. Please try again.</td></tr>';
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Wait for token to be set from layout
        setTimeout(loadBudgetRequests, 100);
    });
    
    // Also listen for tokenReady event
    window.addEventListener('tokenReady', loadBudgetRequests);

    function getStatusBadgeClass(status) {
        const classes = {
            'submitted': 'bg-blue-100 text-blue-800',
            'approved': 'bg-green-100 text-green-800',
            'rejected': 'bg-red-100 text-red-800',
            'pending': 'bg-yellow-100 text-yellow-800'
        };
        return classes[status] || 'bg-gray-100 text-gray-800';
    }
</script>
@endsection

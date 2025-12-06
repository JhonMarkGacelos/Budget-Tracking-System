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
                        <h1 class="text-3xl font-bold">Budget Allocation Details</h1>
                        <a href="{{ route('budget-allocations.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                            <i class="fas fa-arrow-left mr-2"></i>Back
                        </a>
                    </div>

                    <div id="allocationDetails" class="space-y-6">
                        <div class="text-center">
                            <p class="text-gray-500"><i class="fas fa-spinner fa-spin mr-2"></i>Loading allocation details...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function loadAllocationDetails() {
        let apiToken = localStorage.getItem('api_token');
        const allocationId = window.location.pathname.split('/').pop();
        const detailsDiv = document.getElementById('allocationDetails');
        
        console.log('Loading allocation:', allocationId);
        console.log('Token available:', !!apiToken);
        console.log('Token value:', apiToken ? apiToken.substring(0, 10) + '...' : 'none');
        
        if (!apiToken) {
            detailsDiv.innerHTML = '<div class="text-center"><p class="text-orange-500"><i class="fas fa-exclamation-triangle mr-2"></i>Please log in again to view allocation details</p></div>';
            return;
        }
        
        // Load budget allocation details via API
        axios.get(`/api/budget-allocations/${allocationId}`, {
            headers: {
                'Authorization': 'Bearer ' + apiToken
            }
        })
        .then(response => {
            const alloc = response.data.data;
            const utilization = alloc.allocated_amount > 0 ? Math.round((alloc.spent_amount / alloc.allocated_amount) * 100) : 0;
            const remaining = alloc.allocated_amount - alloc.spent_amount;
            const statusClass = utilization > 80 ? 'bg-red-100 text-red-800' : utilization > 50 ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800';
            const statusText = utilization > 80 ? 'Critical' : utilization > 50 ? 'High' : 'Optimal';
            
            detailsDiv.innerHTML = `
                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-700 mb-4">Allocation Information</h3>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Department</label>
                                <p class="text-lg text-gray-900">${alloc.department?.name || 'N/A'}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Fiscal Year</label>
                                <p class="text-gray-900">${alloc.fiscal_year}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Allocated Amount</label>
                                <p class="text-2xl font-bold text-blue-600">$${parseFloat(alloc.allocated_amount).toLocaleString()}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Spent Amount</label>
                                <p class="text-xl font-semibold text-red-600">$${parseFloat(alloc.spent_amount).toLocaleString()}</p>
                            </div>
                        </div>
                    </div>

                    <div>
                        <h3 class="text-lg font-semibold text-gray-700 mb-4">Budget Status</h3>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Remaining Budget</label>
                                <p class="text-2xl font-bold text-green-600">₱${parseFloat(remaining).toLocaleString()}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Utilization Rate</label>
                                <div class="flex items-center gap-2">
                                    <p class="text-2xl font-bold">${utilization}%</p>
                                    <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full ${statusClass}">${statusText}</span>
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500 mb-2">Budget Progress</label>
                                <div class="w-full bg-gray-200 rounded-full h-4">
                                    <div class="bg-${utilization > 80 ? 'red' : utilization > 50 ? 'yellow' : 'green'}-600 h-4 rounded-full" style="width: ${Math.min(utilization, 100)}%"></div>
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Last Updated</label>
                                <p class="text-gray-900">${new Date(alloc.updated_at).toLocaleString()}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-8 pt-6 border-t border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-700 mb-4">Details</h3>
                    <div class="space-y-2 text-sm text-gray-600">
                        <p><i class="fas fa-info-circle mr-2"></i>Allocation ID: ${alloc.id}</p>
                        <p><i class="fas fa-calendar mr-2"></i>Created: ${new Date(alloc.created_at).toLocaleString()}</p>
                        <p><i class="fas fa-building mr-2"></i>Department: ${alloc.department?.name || 'N/A'}</p>
                    </div>
                </div>
            `;
        })
        .catch(error => {
            console.error('Error loading allocation details:', error);
            console.error('Full error:', error.response);
            console.error('Status:', error.response?.status);
            console.error('Data:', error.response?.data);
            
            let errorMsg = 'Error loading allocation details. Please try again.';
            if (error.response?.status === 403) {
                errorMsg = 'You do not have permission to view this allocation.';
            } else if (error.response?.status === 404) {
                errorMsg = 'Allocation not found.';
            } else if (error.response?.status === 401) {
                errorMsg = 'Your session has expired. Please log in again.';
            }
            
            detailsDiv.innerHTML = `<div class="text-center"><p class="text-red-500"><i class="fas fa-exclamation-circle mr-2"></i>${errorMsg}</p></div>`;
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Wait for token to be set from layout
        setTimeout(loadAllocationDetails, 100);
    });
    
    // Also listen for tokenReady event
    window.addEventListener('tokenReady', loadAllocationDetails);
</script>
@endsection

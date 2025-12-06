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
                        <h1 class="text-3xl font-bold">Budget Request Details</h1>
                        <a href="{{ route('budget-requests.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                            <i class="fas fa-arrow-left mr-2"></i>Back
                        </a>
                    </div>

                    <div id="requestDetails" class="space-y-6">
                        <div class="text-center">
                            <p class="text-gray-500"><i class="fas fa-spinner fa-spin mr-2"></i>Loading request details...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function loadRequestDetails() {
        let apiToken = localStorage.getItem('api_token');
        const requestId = window.location.pathname.split('/').pop();
        const detailsDiv = document.getElementById('requestDetails');
        
        console.log('Loading request:', requestId);
        console.log('Token available:', !!apiToken);
        console.log('Token value:', apiToken ? apiToken.substring(0, 10) + '...' : 'none');
        
        if (!apiToken) {
            detailsDiv.innerHTML = '<div class="text-center"><p class="text-orange-500"><i class="fas fa-exclamation-triangle mr-2"></i>Please log in again to view request details</p></div>';
            return;
        }
        
        // Load budget request details via API
        axios.get(`/api/budget-requests/${requestId}`, {
            headers: {
                'Authorization': 'Bearer ' + apiToken
            }
        })
        .then(response => {
            // Handle both response formats: {data: {...}} and {...}
            const req = response.data.data || response.data;
            
            if (!req || !req.id) {
                throw new Error('Invalid response data');
            }
            
            detailsDiv.innerHTML = `
                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-700 mb-4">Request Information</h3>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Title</label>
                                <p class="text-lg text-gray-900">${req.title}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Description</label>
                                <p class="text-gray-900">${req.description || 'N/A'}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Requested Amount</label>
                                <p class="text-2xl font-bold text-blue-600">₱${parseFloat(req.amount || req.requested_amount || 0).toLocaleString()}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Fiscal Year</label>
                                <p class="text-gray-900">${req.fiscal_year}</p>
                            </div>
                        </div>
                    </div>

                    <div>
                        <h3 class="text-lg font-semibold text-gray-700 mb-4">Status & Details</h3>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Status</label>
                                <p><span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full ${getStatusBadgeClass(req.status)}">${req.status}</span></p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Department</label>
                                <p class="text-gray-900">${req.department?.name || 'N/A'}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Submitted By</label>
                                <p class="text-gray-900">${req.user?.name || 'Unknown'}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Created At</label>
                                <p class="text-gray-900">${new Date(req.created_at).toLocaleString()}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Last Updated</label>
                                <p class="text-gray-900">${new Date(req.updated_at).toLocaleString()}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-8 pt-6 border-t border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-700 mb-4">Actions</h3>
                    <div id="actionsContainer" class="space-y-4">
                        <div class="text-sm text-gray-600 space-y-2">
                            <p><i class="fas fa-info-circle mr-2"></i>Request ID: ${req.id}</p>
                            <p><i class="fas fa-calendar mr-2"></i>Request Status: ${req.status}</p>
                        </div>
                    </div>
                </div>
            `;
            
            // Load approval actions after rendering details
            loadApprovalActions(req);
        })
        .catch(error => {
            console.error('Error loading request details:', error);
            console.error('Full error:', error.response);
            console.error('Status:', error.response?.status);
            console.error('Data:', error.response?.data);
            
            let errorMsg = 'Error loading request details. Please try again.';
            if (error.response?.status === 403) {
                errorMsg = 'You do not have permission to view this request.';
            } else if (error.response?.status === 404) {
                errorMsg = 'Request not found.';
            } else if (error.response?.status === 401) {
                errorMsg = 'Your session has expired. Please log in again.';
            }
            
            detailsDiv.innerHTML = `<div class="text-center"><p class="text-red-500"><i class="fas fa-exclamation-circle mr-2"></i>${errorMsg}</p></div>`;
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Wait for token to be set from layout
        setTimeout(loadRequestDetails, 100);
    });
    
    // Also listen for tokenReady event
    window.addEventListener('tokenReady', loadRequestDetails);

    function getStatusBadgeClass(status) {
        const classes = {
            'submitted': 'bg-blue-100 text-blue-800',
            'approved': 'bg-green-100 text-green-800',
            'rejected': 'bg-red-100 text-red-800',
            'pending': 'bg-yellow-100 text-yellow-800',
            'department_approved': 'bg-purple-100 text-purple-800',
            'department_rejected': 'bg-red-100 text-red-800',
            'admin_approved': 'bg-green-100 text-green-800',
            'admin_rejected': 'bg-red-100 text-red-800'
        };
        return classes[status] || 'bg-gray-100 text-gray-800';
    }

    function loadApprovalActions(req) {
        const token = localStorage.getItem('api_token');
        const actionsContainer = document.getElementById('actionsContainer');
        const requestId = req.id;
        const amount = parseFloat(req.amount || req.requested_amount || 0);
        const threshold = 20000;
        
        if (!token) {
            console.error('No token available');
            return;
        }
        
        // Get current user info via API
        axios.get('/api/user', {
            headers: { 'Authorization': 'Bearer ' + token }
        })
        .then(userResponse => {
            const user = userResponse.data;
            console.log('Current user:', user);
            console.log('User role:', user.role);
            console.log('Request status:', req.status);
            console.log('Request amount:', amount);
            
            let actionsHTML = `
                <div class="text-sm text-gray-600 space-y-2 mb-4">
                    <p><i class="fas fa-info-circle mr-2"></i>Request ID: ${req.id}</p>
                    <p><i class="fas fa-calendar mr-2"></i>Request Status: ${req.status}</p>
                    <p><i class="fas fa-peso-sign mr-2"></i>Amount: ₱${amount.toLocaleString()}</p>
                    <p><i class="fas fa-user mr-2"></i>Your Role: ${user.role}</p>
                </div>
            `;
            
            // Department Head Actions - First Level Approval
            if ((user.role === 'department_head' || user.role === 'department') && user.department_id === req.department_id && (req.status === 'submitted' || req.status === 'pending')) {
                console.log('Showing department head approval buttons');
                actionsHTML += `
                    <div class="border-t pt-4">
                        <h4 class="font-semibold text-gray-700 mb-2">Department Head Review</h4>
                        ${amount <= threshold 
                            ? `<p class="text-sm text-green-600 mb-3"><i class="fas fa-info-circle mr-1"></i>Amount ₱${amount.toLocaleString()} is ≤ ₱${threshold.toLocaleString()}: Only department approval needed</p>` 
                            : `<p class="text-sm text-yellow-600 mb-3"><i class="fas fa-exclamation-triangle mr-1"></i>Amount ₱${amount.toLocaleString()} exceeds ₱${threshold.toLocaleString()}: Will also require admin approval</p>`
                        }
                        <div class="flex gap-3">
                            <button onclick="approveDepartment(${requestId})" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                                <i class="fas fa-check mr-2"></i>Approve
                            </button>
                            <button onclick="rejectDepartmentRequest(${requestId})" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                                <i class="fas fa-times mr-2"></i>Reject
                            </button>
                        </div>
                    </div>
                `;
            }
            
            // Admin Actions - Second Level Approval (Only for amounts > $20,000)
            if (user.role === 'admin' && req.status === 'department_approved' && amount > threshold) {
                console.log('Showing admin approval buttons for amount > 20000');
                actionsHTML += `
                    <div class="border-t pt-4">
                        <h4 class="font-semibold text-gray-700 mb-2">Admin Approval Required</h4>
                        <p class="text-sm text-yellow-600 mb-3">
                            <i class="fas fa-exclamation-triangle mr-1"></i>Amount ₱${amount.toLocaleString()} exceeds ₱${threshold.toLocaleString()} threshold - requires admin final approval
                        </p>
                        <div class="flex gap-3">
                            <button onclick="approveAdmin(${requestId})" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                                <i class="fas fa-check mr-2"></i>Approve
                            </button>
                            <button onclick="rejectAdminRequest(${requestId})" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                                <i class="fas fa-times mr-2"></i>Reject
                            </button>
                        </div>
                    </div>
                `;
            }
            
            // Admin can also directly approve department-approved requests under $20,000
            if (user.role === 'admin' && req.status === 'department_approved' && amount <= threshold) {
                console.log('Showing admin final approval buttons for amount <= 20000');
                actionsHTML += `
                    <div class="border-t pt-4">
                        <h4 class="font-semibold text-gray-700 mb-2">Admin Final Approval</h4>
                        <p class="text-sm text-green-600 mb-3">
                            <i class="fas fa-check-circle mr-1"></i>Amount ₱${amount.toLocaleString()} is ≤ ₱${threshold.toLocaleString()} - You can give final approval
                        </p>
                        <div class="flex gap-3">
                            <button onclick="approveAdmin(${requestId})" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                                <i class="fas fa-check mr-2"></i>Approve
                            </button>
                            <button onclick="rejectAdminRequest(${requestId})" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                                <i class="fas fa-times mr-2"></i>Reject
                            </button>
                        </div>
                    </div>
                `;
            }
            
            // Admin bypass approval for submitted requests (if needed for urgent cases)
            if (user.role === 'admin' && (req.status === 'submitted' || req.status === 'pending')) {
                console.log('Showing admin direct approval for submitted request');
                actionsHTML += `
                    <div class="border-t pt-4 bg-yellow-50 p-3 rounded">
                        <h4 class="font-semibold text-gray-700 mb-2">Admin Override</h4>
                        <p class="text-sm text-yellow-700 mb-3">
                            <i class="fas fa-shield-alt mr-1"></i>As admin, you can directly approve this request even though it hasn't been reviewed by the department head
                        </p>
                        <div class="flex gap-3">
                            <button onclick="approveAdmin(${requestId})" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                <i class="fas fa-arrow-right mr-2"></i>Direct Approve
                            </button>
                        </div>
                    </div>
                `;
            }
            
            if (actionsHTML === `
                <div class="text-sm text-gray-600 space-y-2 mb-4">
                    <p><i class="fas fa-info-circle mr-2"></i>Request ID: ${req.id}</p>
                    <p><i class="fas fa-calendar mr-2"></i>Request Status: ${req.status}</p>
                    <p><i class="fas fa-peso-sign mr-2"></i>Amount: ₱${amount.toLocaleString()}</p>
                    <p><i class="fas fa-user mr-2"></i>Your Role: ${user.role}</p>
                </div>
            `) {
                console.log('No approval actions available');
                actionsHTML += `
                    <div class="border-t pt-4 text-gray-600">
                        <p class="text-sm"><i class="fas fa-info-circle mr-2"></i>No approval actions available for this request.</p>
                        <p class="text-xs text-gray-500 mt-2">
                            Status: ${req.status} | Amount: ₱${amount.toLocaleString()} | Your Role: ${user.role}
                        </p>
                    </div>
                `;
            }
            
            actionsContainer.innerHTML = actionsHTML;
        })
        .catch(err => {
            console.error('Error loading user info:', err);
            console.error('Error details:', err.response?.data);
            
            const actionsHTML = `
                <div class="border-t pt-4 bg-red-50 p-3 rounded">
                    <p class="text-sm text-red-600">
                        <i class="fas fa-exclamation-circle mr-2"></i>Could not load approval options: ${err.response?.data?.message || err.message}
                    </p>
                </div>
            `;
            actionsContainer.innerHTML = actionsHTML;
        });
    }

    function approveDepartment(requestId) {
        const token = localStorage.getItem('api_token');
        if (!token) {
            alert('Session expired. Please log in again.');
            return;
        }
        
        if (!confirm('Approve this budget request at department level?')) return;
        
        axios.post(`/api/budget-requests/${requestId}/approve-department`, {}, {
            headers: { 'Authorization': 'Bearer ' + token }
        })
        .then(response => {
            alert('Request approved successfully!');
            location.reload();
        })
        .catch(error => {
            console.error('Error approving request:', error);
            alert('Error: ' + (error.response?.data?.message || 'Failed to approve request'));
        });
    }

    function rejectDepartmentRequest(requestId) {
        const token = localStorage.getItem('api_token');
        if (!token) {
            alert('Session expired. Please log in again.');
            return;
        }
        
        const feedback = prompt('Please provide feedback for rejection:');
        if (!feedback) return;
        
        axios.post(`/api/budget-requests/${requestId}/reject-department`, {
            feedback: feedback
        }, {
            headers: { 'Authorization': 'Bearer ' + token }
        })
        .then(response => {
            alert('Request rejected successfully!');
            location.reload();
        })
        .catch(error => {
            console.error('Error rejecting request:', error);
            alert('Error: ' + (error.response?.data?.message || 'Failed to reject request'));
        });
    }

    function approveAdmin(requestId) {
        const token = localStorage.getItem('api_token');
        if (!token) {
            alert('Session expired. Please log in again.');
            return;
        }
        
        if (!confirm('Approve this budget request at admin level?')) return;
        
        axios.post(`/api/budget-requests/${requestId}/approve-admin`, {}, {
            headers: { 'Authorization': 'Bearer ' + token }
        })
        .then(response => {
            alert('Request approved successfully!');
            location.reload();
        })
        .catch(error => {
            console.error('Error approving request:', error);
            alert('Error: ' + (error.response?.data?.message || 'Failed to approve request'));
        });
    }

    function rejectAdminRequest(requestId) {
        const token = localStorage.getItem('api_token');
        if (!token) {
            alert('Session expired. Please log in again.');
            return;
        }
        
        const feedback = prompt('Please provide feedback for rejection:');
        if (!feedback) return;
        
        axios.post(`/api/budget-requests/${requestId}/reject-admin`, {
            feedback: feedback
        }, {
            headers: { 'Authorization': 'Bearer ' + token }
        })
        .then(response => {
            alert('Request rejected successfully!');
            location.reload();
        })
        .catch(error => {
            console.error('Error rejecting request:', error);
            alert('Error: ' + (error.response?.data?.message || 'Failed to reject request'));
        });
    }

</script>
@endsection

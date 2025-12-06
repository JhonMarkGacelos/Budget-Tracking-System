<!-- Faculty Dashboard -->
<div class="space-y-8">
    <!-- Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- My Requests -->
        <div class="bg-gradient-to-br from-blue-600 to-blue-700 rounded-xl shadow-lg p-6 card-hover text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm font-medium uppercase tracking-wider">My Requests</p>
                    <p class="text-4xl font-bold mt-2" id="myRequests">-</p>
                    <p class="text-xs text-blue-100 mt-1">Total Submitted</p>
                </div>
                <div class="bg-white bg-opacity-20 rounded-full p-4">
                    <i class="fas fa-file-invoice-dollar text-3xl"></i>
                </div>
            </div>
        </div>

        <!-- Approved -->
        <div class="bg-gradient-to-br from-blue-600 to-blue-700 rounded-xl shadow-lg p-6 card-hover text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm font-medium uppercase tracking-wider">Approved</p>
                    <p class="text-4xl font-bold mt-2" id="approved">-</p>
                    <p class="text-xs text-blue-100 mt-1">Successfully Approved</p>
                </div>
                <div class="bg-white bg-opacity-20 rounded-full p-4">
                    <i class="fas fa-check-circle text-3xl"></i>
                </div>
            </div>
        </div>

        <!-- Pending -->
        <div class="bg-gradient-to-br from-blue-600 to-blue-700 rounded-xl shadow-lg p-6 card-hover text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm font-medium uppercase tracking-wider">In Progress</p>
                    <p class="text-4xl font-bold mt-2" id="pending">-</p>
                    <p class="text-xs text-blue-100 mt-1">Awaiting Approval</p>
                </div>
                <div class="bg-white bg-opacity-20 rounded-full p-4">
                    <i class="fas fa-hourglass-half text-3xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- My Recent Requests -->
    <div class="bg-white bg-opacity-90 rounded-xl shadow-lg p-6">
        <h3 class="text-2xl font-bold text-gray-800 mb-6 flex items-center">
            <i class="fas fa-file-alt text-blue-600 mr-3"></i>Recent Requests
        </h3>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-2 font-semibold text-gray-700">Title</th>
                        <th class="px-4 py-2 font-semibold text-gray-700">Amount</th>
                        <th class="px-4 py-2 font-semibold text-gray-700">Status</th>
                        <th class="px-4 py-2 font-semibold text-gray-700">Date</th>
                        <th class="px-4 py-2 font-semibold text-gray-700">Action</th>
                    </tr>
                </thead>
                <tbody id="recentRequestsTable">
                    <tr class="border-b hover:bg-gray-50">
                        <td colspan="5" class="px-4 py-2 text-center text-gray-500">Loading your requests...</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Department Info -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-2xl font-bold text-gray-800 mb-4">Department Information</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <p class="text-gray-500 text-sm">Your Department</p>
                <p class="text-lg font-semibold text-gray-800">{{ auth()->user()->department->name ?? 'N/A' }}</p>
            </div>
            <div>
                <p class="text-gray-500 text-sm">Department Head</p>
                <p class="text-lg font-semibold text-gray-800">Contact administration</p>
            </div>
        </div>
    </div>
</div>

<script>
    function loadFacultyStats() {
        const token = localStorage.getItem('api_token');
        if (!token) {
            console.log('No token available for stats');
            return;
        }

        // Load user's budget requests
        axios.get('/api/budget-requests', {
            headers: { 'Authorization': 'Bearer ' + token }
        })
        .then(response => {
            const requests = response.data.data || [];
            document.getElementById('myRequests').textContent = requests.length;
            
            // Count approved
            const approved = requests.filter(r => r.status === 'approved').length;
            document.getElementById('approved').textContent = approved;
            
            // Count pending/in progress
            const pending = requests.filter(r => r.status !== 'approved' && r.status !== 'rejected').length;
            document.getElementById('pending').textContent = pending;

            // Load recent requests table
            loadRecentRequests(requests);
        })
        .catch(err => console.error('Error loading requests:', err));
    }

    function loadRecentRequests(requests) {
        const tbody = document.getElementById('recentRequestsTable');
        
        if (requests.length === 0) {
            tbody.innerHTML = '<tr><td colspan="5" class="px-4 py-2 text-center text-gray-500">No requests found. <a href="/budget-requests/create" class="text-blue-600 hover:text-blue-800">Submit one now</a></td></tr>';
            return;
        }

        // Show last 5 requests
        const recentRequests = requests.slice(0, 5);
        tbody.innerHTML = recentRequests.map(req => `
            <tr class="border-b hover:bg-gray-50">
                <td class="px-4 py-2 font-medium">${req.title || 'Untitled Request'}</td>
                <td class="px-4 py-2">₱${parseFloat(req.amount || req.requested_amount || 0).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</td>
                <td class="px-4 py-2">
                    <span class="bg-${getStatusBadgeColor(req.status)}-100 text-${getStatusBadgeColor(req.status)}-800 px-2 py-1 rounded text-sm">
                        ${req.status.charAt(0).toUpperCase() + req.status.slice(1)}
                    </span>
                </td>
                <td class="px-4 py-2 text-sm text-gray-500">${new Date(req.created_at).toLocaleDateString()}</td>
                <td class="px-4 py-2">
                    <a href="/budget-requests/${req.id}" class="text-blue-600 hover:text-blue-800 font-semibold">View</a>
                </td>
            </tr>
        `).join('');
    }

    function getStatusBadgeColor(status) {
        const colors = {
            'approved': 'green',
            'rejected': 'red',
            'pending': 'yellow',
            'submitted': 'blue',
            'in_review': 'blue'
        };
        return colors[status] || 'gray';
    }

    // Load stats when page loads
    document.addEventListener('DOMContentLoaded', function() {
        setTimeout(loadFacultyStats, 100);
    });

    // Also load when token is ready
    window.addEventListener('tokenReady', loadFacultyStats);
</script>

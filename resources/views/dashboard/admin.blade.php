<!-- Admin Dashboard -->
<div class="space-y-8">
    <!-- Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Total Budget Allocated -->
        <div class="bg-gradient-to-br from-blue-600 to-blue-700 rounded-xl shadow-lg p-6 card-hover text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm font-medium uppercase tracking-wider">Total Budget</p>
                    <p class="text-4xl font-bold mt-2" id="totalBudgetAdmin">₱0</p>
                    <p class="text-xs text-blue-100 mt-1">All Departments</p>
                </div>
                <div class="bg-white bg-opacity-20 rounded-full p-4">
                    <i class="fas fa-money-bill-wave text-3xl"></i>
                </div>
            </div>
        </div>

        <!-- Total Requests -->
        <div class="bg-gradient-to-br from-blue-600 to-blue-700 rounded-xl shadow-lg p-6 card-hover text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm font-medium uppercase tracking-wider">Total Requests</p>
                    <p class="text-4xl font-bold mt-2" id="totalRequests">-</p>
                    <p class="text-xs text-blue-100 mt-1">All Departments</p>
                </div>
                <div class="bg-white bg-opacity-20 rounded-full p-4">
                    <i class="fas fa-file-invoice-dollar text-3xl"></i>
                </div>
            </div>
        </div>

        <!-- Pending Approvals -->
        <div class="bg-gradient-to-br from-blue-600 to-blue-700 rounded-xl shadow-lg p-6 card-hover text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm font-medium uppercase tracking-wider">Pending Approvals</p>
                    <p class="text-4xl font-bold mt-2" id="pendingApprovals">-</p>
                    <p class="text-xs text-blue-100 mt-1">Needs Review</p>
                </div>
                <div class="bg-white bg-opacity-20 rounded-full p-4">
                    <i class="fas fa-clock text-3xl"></i>
                </div>
            </div>
        </div>

        <!-- Total Departments -->
        <div class="bg-gradient-to-br from-blue-600 to-blue-700 rounded-xl shadow-lg p-6 card-hover text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm font-medium uppercase tracking-wider">Departments</p>
                    <p class="text-4xl font-bold mt-2" id="totalDepartments">-</p>
                    <p class="text-xs text-blue-100 mt-1">Active</p>
                </div>
                <div class="bg-white bg-opacity-20 rounded-full p-4">
                    <i class="fas fa-building text-3xl"></i>
                </div>
            </div>
        </div>

        <!-- Total Users -->
        <div class="bg-gradient-to-br from-blue-600 to-blue-700 rounded-xl shadow-lg p-6 card-hover text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm font-medium uppercase tracking-wider">Users</p>
                    <p class="text-4xl font-bold mt-2" id="totalUsers">-</p>
                    <p class="text-xs text-blue-100 mt-1">System Wide</p>
                </div>
                <div class="bg-white bg-opacity-20 rounded-full p-4">
                    <i class="fas fa-users text-3xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="bg-white bg-opacity-90 rounded-xl shadow-lg p-6">
        <h3 class="text-2xl font-bold text-gray-800 mb-6 flex items-center">
            <i class="fas fa-history text-blue-600 mr-3"></i>Recent Activity
        </h3>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-2 font-semibold text-gray-700">Action</th>
                        <th class="px-4 py-2 font-semibold text-gray-700">User</th>
                        <th class="px-4 py-2 font-semibold text-gray-700">Model</th>
                        <th class="px-4 py-2 font-semibold text-gray-700">Date</th>
                    </tr>
                </thead>
                <tbody id="recentActivityTable">
                    <tr class="border-b hover:bg-gray-50">
                        <td colspan="4" class="px-4 py-2 text-center text-gray-500">Loading recent activity...</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    function loadDashboardStats() {
        const token = localStorage.getItem('api_token');
        if (!token) {
            console.log('No token available for stats');
            return;
        }

        // Load users count
        axios.get('/api/users', {
            headers: { 'Authorization': 'Bearer ' + token }
        })
        .then(response => {
            const count = response.data.data ? response.data.data.length : 0;
            document.getElementById('totalUsers').textContent = count;
        })
        .catch(err => console.error('Error loading users:', err));

        // Load budget requests count
        axios.get('/api/budget-requests', {
            headers: { 'Authorization': 'Bearer ' + token }
        })
        .then(response => {
            const count = response.data.data ? response.data.data.length : 0;
            document.getElementById('totalRequests').textContent = count;
            
            // Count pending approvals (requests that need admin attention)
            const pending = response.data.data ? response.data.data.filter(r => 
                r.status === 'pending' || 
                r.status === 'submitted' || 
                r.status === 'department_approved'
            ).length : 0;
            document.getElementById('pendingApprovals').textContent = pending;
        })
        .catch(err => console.error('Error loading requests:', err));

        // Load departments count
        axios.get('/api/departments', {
            headers: { 'Authorization': 'Bearer ' + token }
        })
        .then(response => {
            const count = response.data.data ? response.data.data.length : 0;
            document.getElementById('totalDepartments').textContent = count;
        })
        .catch(err => console.error('Error loading departments:', err));

        // Load total budget from all allocations
        axios.get('/api/budget-allocations', {
            headers: { 'Authorization': 'Bearer ' + token }
        })
        .then(response => {
            const allocations = response.data.data || [];
            const totalBudget = allocations.reduce((sum, alloc) => sum + parseFloat(alloc.allocated_amount || 0), 0);
            document.getElementById('totalBudgetAdmin').textContent = '₱' + totalBudget.toLocaleString('en-US', {maximumFractionDigits: 0});
        })
        .catch(err => console.error('Error loading budget:', err));

        // Load recent activity from audit logs
        loadRecentActivity();
    }

    function loadRecentActivity() {
        const token = localStorage.getItem('api_token');
        if (!token) {
            console.log('No token available for recent activity');
            return;
        }

        const tbody = document.getElementById('recentActivityTable');

        axios.get('/api/audit-logs', {
            headers: { 'Authorization': 'Bearer ' + token }
        })
        .then(response => {
            const logs = response.data.data || [];
            
            if (logs.length === 0) {
                tbody.innerHTML = '<tr><td colspan="4" class="px-4 py-2 text-center text-gray-500">No recent activity</td></tr>';
                return;
            }

            // Show last 10 activities
            const recentLogs = logs.slice(0, 10);
            tbody.innerHTML = recentLogs.map(log => `
                <tr class="border-b hover:bg-gray-50">
                    <td class="px-4 py-2">
                        <span class="bg-${getActionBadgeColor(log.action)}-100 text-${getActionBadgeColor(log.action)}-800 px-2 py-1 rounded text-sm">
                            ${log.action.charAt(0).toUpperCase() + log.action.slice(1)}
                        </span>
                    </td>
                    <td class="px-4 py-2">${log.user_name || 'System'}</td>
                    <td class="px-4 py-2">${log.model_type || 'Unknown'}</td>
                    <td class="px-4 py-2 text-sm text-gray-500">${new Date(log.created_at).toLocaleString()}</td>
                </tr>
            `).join('');
        })
        .catch(err => {
            console.error('Error loading recent activity:', err);
            tbody.innerHTML = '<tr><td colspan="4" class="px-4 py-2 text-center text-red-500">Failed to load recent activity</td></tr>';
        });
    }

    function getActionBadgeColor(action) {
        const colors = {
            'create': 'blue',
            'update': 'yellow',
            'delete': 'red',
            'approve': 'green',
            'reject': 'orange'
        };
        return colors[action] || 'gray';
    }

    // Load stats when page loads
    document.addEventListener('DOMContentLoaded', function() {
        setTimeout(loadDashboardStats, 100);
    });

    // Also load when token is ready
    window.addEventListener('tokenReady', loadDashboardStats);
</script>

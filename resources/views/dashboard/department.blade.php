<!-- Department Dashboard -->
<div class="space-y-8">
    <!-- Store current user data for JavaScript -->
    <script>
        window.currentUser = {
            id: {{ auth()->user()->id }},
            department_id: {{ auth()->user()->department_id }},
            name: '{{ auth()->user()->name }}',
            role: '{{ auth()->user()->role }}'
        };
    </script>
    <!-- Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Allocated Budget -->
        <div class="bg-gradient-to-br from-blue-600 to-blue-700 rounded-xl shadow-lg p-6 card-hover text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm font-medium uppercase tracking-wider">Allocated Budget</p>
                    <p class="text-4xl font-bold mt-2" id="allocatedBudget">₱0</p>
                    <p class="text-xs text-blue-100 mt-1">Current Fiscal Year</p>
                </div>
                <div class="bg-white bg-opacity-20 rounded-full p-4">
                    <i class="fas fa-money-bill-wave text-3xl"></i>
                </div>
            </div>
        </div>

        <!-- Department Requests -->
        <div class="bg-gradient-to-br from-blue-600 to-blue-700 rounded-xl shadow-lg p-6 card-hover text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm font-medium uppercase tracking-wider">Department Requests</p>
                    <p class="text-4xl font-bold mt-2" id="departmentRequests">-</p>
                    <p class="text-xs text-blue-100 mt-1">Total Submitted</p>
                </div>
                <div class="bg-white bg-opacity-20 rounded-full p-4">
                    <i class="fas fa-file-invoice-dollar text-3xl"></i>
                </div>
            </div>
        </div>

        <!-- Pending Reviews -->
        <div class="bg-gradient-to-br from-blue-600 to-blue-700 rounded-xl shadow-lg p-6 card-hover text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm font-medium uppercase tracking-wider">Pending Reviews</p>
                    <p class="text-4xl font-bold mt-2" id="pendingReviews">-</p>
                    <p class="text-xs text-blue-100 mt-1">Need Your Review</p>
                </div>
                <div class="bg-white bg-opacity-20 rounded-full p-4">
                    <i class="fas fa-tasks text-3xl"></i>
                </div>
            </div>
        </div>

        <!-- Faculty Members -->
        <div class="bg-gradient-to-br from-blue-600 to-blue-700 rounded-xl shadow-lg p-6 card-hover text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm font-medium uppercase tracking-wider">Faculty Members</p>
                    <p class="text-4xl font-bold mt-2" id="facultyCount">-</p>
                    <p class="text-xs text-blue-100 mt-1">In Department</p>
                </div>
                <div class="bg-white bg-opacity-20 rounded-full p-4">
                    <i class="fas fa-users text-3xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Pending Requests -->
    <div class="bg-white bg-opacity-90 rounded-xl shadow-lg p-6">
        <h3 class="text-2xl font-bold text-gray-800 mb-6 flex items-center">
            <i class="fas fa-clipboard-list text-blue-600 mr-3"></i>Pending Requests for Review
        </h3>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-2 font-semibold text-gray-700">Title</th>
                        <th class="px-4 py-2 font-semibold text-gray-700">Submitter</th>
                        <th class="px-4 py-2 font-semibold text-gray-700">Amount</th>
                        <th class="px-4 py-2 font-semibold text-gray-700">Status</th>
                        <th class="px-4 py-2 font-semibold text-gray-700">Action</th>
                    </tr>
                </thead>
                <tbody id="pendingRequestsTable">
                    <tr class="border-b hover:bg-gray-50">
                        <td colspan="5" class="px-4 py-2 text-center text-gray-500">Loading requests...</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    function loadPendingRequests() {
        const token = localStorage.getItem('api_token');
        const tbody = document.getElementById('pendingRequestsTable');
        
        if (!token) {
            tbody.innerHTML = '<tr><td colspan="5" class="px-4 py-2 text-center text-orange-500">Please log in to see pending requests</td></tr>';
            return;
        }

        axios.get('/api/budget-requests', {
            headers: { 'Authorization': 'Bearer ' + token }
        })
        .then(response => {
            const requests = response.data.data || [];
            
            // Filter for pending requests only
            const pending = requests.filter(r => r.status === 'pending' || r.status === 'submitted');
            
            if (pending.length === 0) {
                tbody.innerHTML = '<tr><td colspan="5" class="px-4 py-2 text-center text-gray-500">No pending requests for review</td></tr>';
                return;
            }

            tbody.innerHTML = pending.map(req => `
                <tr class="border-b hover:bg-gray-50">
                    <td class="px-4 py-2 font-medium">${req.title || 'N/A'}</td>
                    <td class="px-4 py-2">${req.user?.name || 'Unknown'}</td>
                    <td class="px-4 py-2">₱${parseFloat(req.amount || 0).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</td>
                    <td class="px-4 py-2"><span class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded text-sm">${req.status || 'Pending'}</span></td>
                    <td class="px-4 py-2">
                        <a href="/budget-requests/${req.id}" class="text-blue-600 hover:text-blue-800 font-semibold">Review</a>
                    </td>
                </tr>
            `).join('');
        })
        .catch(err => {
            console.error('Error loading pending requests:', err);
            tbody.innerHTML = '<tr><td colspan="5" class="px-4 py-2 text-center text-red-500">Error loading requests</td></tr>';
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        setTimeout(loadPendingRequests, 100);
    });

    window.addEventListener('tokenReady', loadPendingRequests);

    function loadDepartmentStats() {
        const token = localStorage.getItem('api_token');
        console.log('Token available:', !!token);
        
        if (!token) {
            console.log('No token available for stats');
            return;
        }

        // Get current user info from window object
        const departmentId = window.currentUser ? window.currentUser.department_id : null;
        console.log('Current User:', window.currentUser);
        console.log('Department ID from user:', departmentId);

        // Load budget allocations for current department
        console.log('Fetching allocations from /api/budget-allocations');
        axios.get('/api/budget-allocations', {
            headers: { 'Authorization': 'Bearer ' + token }
        })
        .then(response => {
            console.log('API Response:', response);
            const allocations = response.data.data || [];
            
            // Get current fiscal year (just the year like 2026)
            const currentYear = new Date().getFullYear().toString();
            
            console.log('Raw Allocations:', allocations);
            console.log('Department ID:', departmentId);
            console.log('Current Year:', currentYear);
            
            // Find allocation for current department and fiscal year
            const currentAllocation = allocations.find(a => {
                console.log('Checking allocation:', a);
                console.log('  fiscal_year:', a.fiscal_year, '=== currentYear:', currentYear, '?', a.fiscal_year === currentYear);
                console.log('  department_id:', a.department_id, '=== departmentId:', departmentId, '?', parseInt(a.department_id) === departmentId);
                return a.fiscal_year === currentYear && parseInt(a.department_id) === departmentId;
            });
            
            console.log('Matching Allocation:', currentAllocation);
            
            if (currentAllocation) {
                const allocated = parseFloat(currentAllocation.allocated_amount || 0);
                console.log('Setting allocated budget to:', allocated);
                document.getElementById('allocatedBudget').textContent = `₱${allocated.toLocaleString('en-US', {maximumFractionDigits: 0})}`;
            } else {
                console.log('No matching allocation found');
                document.getElementById('allocatedBudget').textContent = '$0';
            }
        })
        .catch(err => console.error('Error loading allocations:', err));

        // Load budget requests for current user's department
        axios.get('/api/budget-requests', {
            headers: { 'Authorization': 'Bearer ' + token }
        })
        .then(response => {
            const requests = response.data.data || [];
            document.getElementById('departmentRequests').textContent = requests.length;
            
            // Count pending reviews (requests waiting for department approval)
            const pending = requests.filter(r => 
                r.status === 'pending' || 
                r.status === 'submitted'
            ).length;
            document.getElementById('pendingReviews').textContent = pending;
        })
        .catch(err => console.error('Error loading requests:', err));

        // Load faculty members in department
        axios.get('/api/users', {
            headers: { 'Authorization': 'Bearer ' + token }
        })
        .then(response => {
            const users = response.data.data || [];
            const faculty = users.filter(u => u.role === 'faculty').length;
            document.getElementById('facultyCount').textContent = faculty;
        })
        .catch(err => console.error('Error loading users:', err));
    }

    // Load stats when page loads
    document.addEventListener('DOMContentLoaded', function() {
        console.log('DOMContentLoaded - checking token');
        const token = localStorage.getItem('api_token');
        if (token) {
            console.log('Token found in localStorage, calling loadDepartmentStats');
            loadDepartmentStats();
        } else {
            console.log('Token not in localStorage, will load on tokenReady');
        }
    });

    // Also load when token is ready
    window.addEventListener('tokenReady', function() {
        console.log('tokenReady event fired, calling loadDepartmentStats');
        loadDepartmentStats();
    });
</script>

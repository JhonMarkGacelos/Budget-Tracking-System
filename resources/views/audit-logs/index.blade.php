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
                    <h1 class="text-3xl font-bold mb-6">Audit Logs</h1>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 border border-gray-300">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Model</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Timestamp</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200" id="auditLogsTable">
                                <tr>
                                    <td colspan="5" class="px-6 py-4 text-center text-gray-500">Loading audit logs...</td>
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
    function loadAuditLogs() {
        let apiToken = localStorage.getItem('api_token');
        const tbody = document.getElementById('auditLogsTable');
        
        if (!apiToken) {
            tbody.innerHTML = '<tr><td colspan="5" class="px-6 py-4 text-center text-orange-500"><i class="fas fa-exclamation-triangle mr-2"></i>Please refresh the page or log in again to load audit logs</td></tr>';
            return;
        }
        
        // Load audit logs via API
        axios.get('/api/audit-logs', {
            headers: {
                'Authorization': 'Bearer ' + apiToken
            }
        })
        .then(response => {
            const logs = response.data.data || [];
            
            if (logs.length === 0) {
                tbody.innerHTML = '<tr><td colspan="5" class="px-6 py-4 text-center text-gray-500">No audit logs found</td></tr>';
                return;
            }

            tbody.innerHTML = logs.map(log => `
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">${log.user?.name || 'System'}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${getActionBadgeClass(log.action)}">
                            ${log.action}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${log.model_type || 'N/A'}</td>
                    <td class="px-6 py-4 text-sm text-gray-500 max-w-xs truncate">${log.description || 'No description'}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${new Date(log.created_at).toLocaleString()}</td>
                </tr>
            `).join('');
        })
        .catch(error => {
            console.error('Error loading audit logs:', error);
            tbody.innerHTML = '<tr><td colspan="5" class="px-6 py-4 text-center text-red-500"><i class="fas fa-exclamation-circle mr-2\"></i>Error loading audit logs. Please try again.</td></tr>';
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Wait for token to be set from layout
        setTimeout(loadAuditLogs, 100);
    });
    
    // Also listen for tokenReady event
    window.addEventListener('tokenReady', loadAuditLogs);

    function getActionBadgeClass(action) {
        const classes = {
            'created': 'bg-blue-100 text-blue-800',
            'updated': 'bg-green-100 text-green-800',
            'deleted': 'bg-red-100 text-red-800',
            'approved': 'bg-purple-100 text-purple-800',
            'rejected': 'bg-orange-100 text-orange-800'
        };
        return classes[action] || 'bg-gray-100 text-gray-800';
    }
</script>
@endsection

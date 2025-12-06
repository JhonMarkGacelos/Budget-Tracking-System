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
                        <h1 class="text-3xl font-bold text-gray-800 flex items-center">
                            <i class="fas fa-users text-blue-600 mr-3"></i>Users
                        </h1>
                        <a href="{{ route('users.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-xl transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                            <i class="fas fa-user-plus mr-2"></i>Add New User
                        </a>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 border border-gray-300">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Department</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created At</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200" id="usersTable">
                                <tr>
                                    <td colspan="6" class="px-6 py-4 text-center text-gray-500">Loading users...</td>
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
    function loadUsers() {
        let apiToken = localStorage.getItem('api_token');
        const tbody = document.getElementById('usersTable');
        
        if (!apiToken) {
            tbody.innerHTML = '<tr><td colspan="6" class="px-6 py-4 text-center text-orange-500"><i class="fas fa-exclamation-triangle mr-2"></i>Please refresh the page or log in again to view users</td></tr>';
            return;
        }
        
        // Load users via API
        axios.get('/api/users', {
            headers: {
                'Authorization': 'Bearer ' + apiToken
            }
        })
        .then(response => {
            const users = response.data.data || [];
            
            if (users.length === 0) {
                tbody.innerHTML = '<tr><td colspan="6" class="px-6 py-4 text-center text-gray-500">No users found</td></tr>';
                return;
            }

            tbody.innerHTML = users.map(user => `
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">${user.name}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${user.email}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full ${getRoleBadgeClass(user.role)}">
                            ${ucfirst(user.role)}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${user.department?.name || 'N/A'}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${new Date(user.created_at).toLocaleDateString()}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <a href="/users/${user.id}" class="text-blue-600 hover:text-blue-900 mr-3">View</a>
                        <a href="/users/${user.id}/edit" class="text-green-600 hover:text-green-900 mr-3">Edit</a>
                        <button onclick="deleteUser(${user.id})" class="text-red-600 hover:text-red-900">Delete</button>
                    </td>
                </tr>
            `).join('');
        })
        .catch(error => {
            console.error('Error loading users:', error);
            tbody.innerHTML = '<tr><td colspan="6" class="px-6 py-4 text-center text-red-500"><i class="fas fa-exclamation-circle mr-2"></i>Error loading users. Please try again.</td></tr>';
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Wait a moment for token to be set from layout
        setTimeout(loadUsers, 100);
    });
    
    // Also listen for tokenReady event
    window.addEventListener('tokenReady', loadUsers);

    function getRoleBadgeClass(role) {
        const classes = {
            'admin': 'bg-red-100 text-red-800',
            'department': 'bg-blue-100 text-blue-800',
            'faculty': 'bg-green-100 text-green-800'
        };
        return classes[role] || 'bg-gray-100 text-gray-800';
    }

    function ucfirst(str) {
        return str.charAt(0).toUpperCase() + str.slice(1);
    }

    function deleteUser(userId) {
        if (!confirm('Are you sure you want to delete this user?')) {
            return;
        }

        let apiToken = localStorage.getItem('api_token');
        
        axios.delete(`/api/users/${userId}`, {
            headers: {
                'Authorization': 'Bearer ' + apiToken
            }
        })
        .then(response => {
            alert('User deleted successfully!');
            location.reload();
        })
        .catch(error => {
            console.error('Error deleting user:', error);
            alert('Error deleting user: ' + (error.response?.data?.message || error.message));
        });
    }
</script>
@endsection

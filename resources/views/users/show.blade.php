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
                        <h1 class="text-3xl font-bold">User Details</h1>
                        <a href="{{ route('users.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                            <i class="fas fa-arrow-left mr-2"></i>Back
                        </a>
                    </div>

                    <div id="userDetails" class="space-y-6">
                        <div class="text-center">
                            <p class="text-gray-500"><i class="fas fa-spinner fa-spin mr-2"></i>Loading user details...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function loadUserDetails() {
        let apiToken = localStorage.getItem('api_token');
        const userId = window.location.pathname.split('/')[2];
        const detailsDiv = document.getElementById('userDetails');
        
        if (!apiToken) {
            detailsDiv.innerHTML = '<div class="text-center"><p class="text-orange-500"><i class="fas fa-exclamation-triangle mr-2"></i>Please log in again to view user details</p></div>';
            return;
        }
        
        // Load user details via API
        axios.get(`/api/users/${userId}`, {
            headers: {
                'Authorization': 'Bearer ' + apiToken
            }
        })
        .then(response => {
            const user = response.data.data;
            
            detailsDiv.innerHTML = `
                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-700 mb-4">User Information</h3>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Name</label>
                                <p class="text-lg text-gray-900">${user.name}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Email</label>
                                <p class="text-gray-900">${user.email}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Role</label>
                                <p><span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full ${getRoleBadgeClass(user.role)}">${ucfirst(user.role)}</span></p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Department</label>
                                <p class="text-gray-900">${user.department?.name || 'N/A'}</p>
                            </div>
                        </div>
                    </div>

                    <div>
                        <h3 class="text-lg font-semibold text-gray-700 mb-4">Additional Information</h3>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Created At</label>
                                <p class="text-gray-900">${new Date(user.created_at).toLocaleString()}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Last Updated</label>
                                <p class="text-gray-900">${new Date(user.updated_at).toLocaleString()}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Status</label>
                                <p class="text-gray-900"><span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-green-100 text-green-800">Active</span></p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-8 pt-6 border-t border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-700 mb-4">Actions</h3>
                    <div class="flex gap-3">
                        <a href="/users/${user.id}/edit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            <i class="fas fa-edit mr-2"></i>Edit User
                        </a>
                        <button onclick="deleteUser(${user.id})" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                            <i class="fas fa-trash mr-2"></i>Delete User
                        </button>
                        <a href="/users" class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                            <i class="fas fa-list mr-2"></i>Back to Users
                        </a>
                    </div>
                </div>
            `;
        })
        .catch(error => {
            console.error('Error loading user details:', error);
            detailsDiv.innerHTML = '<div class="text-center"><p class="text-red-500"><i class="fas fa-exclamation-circle mr-2"></i>Error loading user details. Please try again.</p></div>';
        });
    }

    function deleteUser(userId) {
        if (!confirm('Are you sure you want to delete this user?')) return;
        
        const token = localStorage.getItem('api_token');
        axios.delete(`/api/users/${userId}`, {
            headers: {
                'Authorization': 'Bearer ' + token
            }
        })
        .then(() => {
            alert('User deleted successfully');
            window.location.href = '/users';
        })
        .catch(error => {
            alert('Error deleting user: ' + (error.response?.data?.message || error.message));
        });
    }

    function getRoleBadgeClass(role) {
        const classes = {
            'admin': 'bg-red-100 text-red-800',
            'faculty': 'bg-blue-100 text-blue-800',
            'department': 'bg-green-100 text-green-800'
        };
        return classes[role] || 'bg-gray-100 text-gray-800';
    }

    function ucfirst(str) {
        return str.charAt(0).toUpperCase() + str.slice(1);
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Wait for token to be set from layout
        setTimeout(loadUserDetails, 100);
    });
    
    // Also listen for tokenReady event
    window.addEventListener('tokenReady', loadUserDetails);
</script>
@endsection

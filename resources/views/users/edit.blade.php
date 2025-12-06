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
                    <h1 class="text-3xl font-bold mb-6">Edit User</h1>

                    <form id="editUserForm" class="space-y-6">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700">Name <span class="text-red-500">*</span></label>
                            <input type="text" id="name" name="name" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" required>
                        </div>

                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700">Email <span class="text-red-500">*</span></label>
                            <input type="email" id="email" name="email" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" required>
                        </div>

                        <div>
                            <label for="role" class="block text-sm font-medium text-gray-700">Role <span class="text-red-500">*</span></label>
                            <select id="role" name="role" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" required>
                                <option value="">Select a role</option>
                                <option value="faculty">Faculty</option>
                                <option value="department">Department Head</option>
                            </select>
                        </div>

                        <div>
                            <label for="department_id" class="block text-sm font-medium text-gray-700">Department <span class="text-red-500">*</span></label>
                            <select id="department_id" name="department_id" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" required>
                                <option value="">Loading departments...</option>
                            </select>
                        </div>

                        <div id="errorMessages" class="hidden bg-red-50 border border-red-200 rounded-lg p-4"></div>

                        <div class="flex gap-3">
                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded">
                                <i class="fas fa-save mr-2"></i>Save Changes
                            </button>
                            <a href="/users" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-6 rounded">
                                <i class="fas fa-times mr-2"></i>Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const userId = window.location.pathname.split('/')[2];

    // Load departments
    function loadDepartments() {
        const token = localStorage.getItem('api_token');
        if (!token) return;

        axios.get('/api/departments', {
            headers: { 'Authorization': 'Bearer ' + token }
        })
        .then(response => {
            const select = document.getElementById('department_id');
            select.innerHTML = '<option value="">Select a department</option>';
            response.data.data.forEach(dept => {
                select.innerHTML += `<option value="${dept.id}">${dept.name}</option>`;
            });
            // Load user data after departments are loaded
            loadUserData();
        })
        .catch(err => console.error('Error loading departments:', err));
    }

    // Load user data
    function loadUserData() {
        const token = localStorage.getItem('api_token');
        if (!token) return;

        axios.get(`/api/users/${userId}`, {
            headers: { 'Authorization': 'Bearer ' + token }
        })
        .then(response => {
            const user = response.data.data;
            document.getElementById('name').value = user.name;
            document.getElementById('email').value = user.email;
            document.getElementById('role').value = user.role;
            document.getElementById('department_id').value = user.department_id || '';
        })
        .catch(err => {
            console.error('Error loading user:', err);
            alert('Error loading user data');
        });
    }

    // Form submission
    document.getElementById('editUserForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const token = localStorage.getItem('api_token');
        if (!token) {
            alert('Please log in again');
            return;
        }

        const data = {
            name: document.getElementById('name').value,
            email: document.getElementById('email').value,
            role: document.getElementById('role').value,
            department_id: document.getElementById('department_id').value
        };

        axios.put(`/api/users/${userId}`, data, {
            headers: { 'Authorization': 'Bearer ' + token }
        })
        .then(() => {
            alert('User updated successfully');
            window.location.href = '/users';
        })
        .catch(error => {
            const errorDiv = document.getElementById('errorMessages');
            errorDiv.classList.remove('hidden');
            
            if (error.response?.data?.errors) {
                errorDiv.innerHTML = Object.values(error.response.data.errors)
                    .flat()
                    .map(err => `<p>${err}</p>`)
                    .join('');
            } else {
                errorDiv.innerHTML = `<p>${error.response?.data?.message || error.message}</p>`;
            }
        });
    });

    // Load on page load
    document.addEventListener('DOMContentLoaded', loadDepartments);
    window.addEventListener('tokenReady', loadDepartments);
</script>
@endsection

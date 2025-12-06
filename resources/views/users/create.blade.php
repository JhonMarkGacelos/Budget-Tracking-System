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
                    <h1 class="text-3xl font-bold mb-6">Create User</h1>

                    <form id="userForm" class="space-y-6">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700">Full Name</label>
                            <input type="text" id="name" name="name" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                            <input type="email" id="email" name="email" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                            <input type="password" id="password" name="password" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <div>
                            <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm Password</label>
                            <input type="password" id="password_confirmation" name="password_confirmation" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <div>
                            <label for="role" class="block text-sm font-medium text-gray-700">Role</label>
                            <select id="role" name="role" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Select a role</option>
                                <option value="faculty">Faculty</option>
                                <option value="department">Department Head</option>
                            </select>
                        </div>

                        <div>
                            <label for="department_id" class="block text-sm font-medium text-gray-700">Department <span class="text-red-500">*</span></label>
                            <select id="department_id" name="department_id" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Select a department</option>
                            </select>
                        </div>

                        <div class="flex gap-4">
                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                <i class="fas fa-user-plus mr-2"></i>Create User
                            </button>
                            <button type="button" onclick="history.back()" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                <i class="fas fa-times mr-2"></i>Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Get or create API token for current user
    let apiToken = localStorage.getItem('api_token');
    
    function loadDepartments() {
        axios.get('/api/departments', {
            headers: {
                'Authorization': 'Bearer ' + apiToken
            }
        })
        .then(response => {
            const departments = response.data.data || [];
            const select = document.getElementById('department_id');
            
            departments.forEach(dept => {
                const option = document.createElement('option');
                option.value = dept.id;
                option.textContent = dept.name;
                select.appendChild(option);
            });
        })
        .catch(error => {
            console.error('Error loading departments:', error);
            alert('Error loading departments. Please ensure you are logged in.');
        });
    }
    
    // Load departments when page loads
    document.addEventListener('DOMContentLoaded', loadDepartments);

    document.getElementById('userForm').addEventListener('submit', function(e) {
        e.preventDefault();

        const formData = {
            name: document.getElementById('name').value,
            email: document.getElementById('email').value,
            password: document.getElementById('password').value,
            password_confirmation: document.getElementById('password_confirmation').value,
            role: document.getElementById('role').value,
            department_id: document.getElementById('department_id').value || null
        };

        axios.post('/api/users', formData, {
            headers: {
                'Authorization': 'Bearer ' + apiToken,
                'Content-Type': 'application/json'
            }
        })
        .then(response => {
            alert('User created successfully!');
            history.back();
        })
        .catch(error => {
            console.error('Error creating user:', error);
            const errorMessages = error.response?.data?.errors ? Object.values(error.response.data.errors).flat().join(', ') : (error.response?.data?.message || error.message);
            alert('Error creating user: ' + errorMessages);
        });
    });

</script>
@endsection

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
                        <h1 class="text-3xl font-bold">Create Budget Allocation</h1>
                        <a href="{{ route('budget-allocations.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                            <i class="fas fa-arrow-left mr-2"></i>Back
                        </a>
                    </div>

                    <form id="allocationForm" class="space-y-6" onsubmit="handleSubmit(event)">
                        <!-- Department Selection -->
                        <div>
                            <label for="department" class="block text-sm font-medium text-gray-700 mb-2">
                                Department <span class="text-red-500">*</span>
                            </label>
                            <select id="department" name="department_id" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">Select a department...</option>
                            </select>
                            <div id="departmentError" class="text-red-500 text-sm mt-1" style="display: none;"></div>
                        </div>

                        <!-- Fiscal Year -->
                        <div>
                            <label for="fiscalYear" class="block text-sm font-medium text-gray-700 mb-2">
                                Fiscal Year <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="fiscalYear" name="fiscal_year" placeholder="e.g., 2025-2026 or 2026" required 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <div id="fiscalYearError" class="text-red-500 text-sm mt-1" style="display: none;"></div>
                            <p class="text-gray-500 text-sm mt-1">Enter as "2025-2026" or "2026"</p>
                        </div>

                        <!-- Allocated Amount -->
                        <div>
                            <label for="amount" class="block text-sm font-medium text-gray-700 mb-2">
                                Allocated Amount ($) <span class="text-red-500">*</span>
                            </label>
                            <input type="number" id="amount" name="allocated_amount" placeholder="0.00" step="0.01" min="0" required
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <div id="amountError" class="text-red-500 text-sm mt-1" style="display: none;"></div>
                            <p class="text-gray-500 text-sm mt-1">The total budget available for this department in the specified fiscal year</p>
                        </div>

                        <!-- Status -->
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                                Status <span class="text-red-500">*</span>
                            </label>
                            <select id="status" name="status" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">Select a status...</option>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                                <option value="pending">Pending</option>
                            </select>
                            <div id="statusError" class="text-red-500 text-sm mt-1" style="display: none;"></div>
                        </div>

                        <!-- Notes -->
                        <div>
                            <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                            <textarea id="notes" name="notes" rows="4" placeholder="Additional notes about this allocation..."
                                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                        </div>

                        <!-- Buttons -->
                        <div class="flex gap-4 pt-6">
                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded">
                                <i class="fas fa-check mr-2"></i>Create Allocation
                            </button>
                            <a href="{{ route('budget-allocations.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-6 rounded">
                                <i class="fas fa-times mr-2"></i>Cancel
                            </a>
                        </div>
                    </form>

                    <!-- Success/Error Messages -->
                    <div id="successMessage" class="mt-4 p-4 bg-green-100 text-green-700 rounded" style="display: none;">
                        <i class="fas fa-check-circle mr-2"></i><span id="successText"></span>
                    </div>
                    <div id="errorMessage" class="mt-4 p-4 bg-red-100 text-red-700 rounded" style="display: none;">
                        <i class="fas fa-exclamation-circle mr-2"></i><span id="errorText"></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    let departmentsLoaded = false;

    // Load departments on page load
    document.addEventListener('DOMContentLoaded', function() {
        loadDepartments();
    });

    window.addEventListener('tokenReady', function() {
        if (!departmentsLoaded) {
            loadDepartments();
        }
    });

    function loadDepartments() {
        const token = localStorage.getItem('api_token');
        if (!token) return;

        axios.get('/api/departments', {
            headers: { 'Authorization': 'Bearer ' + token }
        })
        .then(response => {
            const departments = response.data.data || [];
            const selectElement = document.getElementById('department');
            
            // Clear existing options except the first one
            while (selectElement.options.length > 1) {
                selectElement.remove(1);
            }
            
            departments.forEach(dept => {
                const option = document.createElement('option');
                option.value = dept.id;
                option.textContent = dept.name;
                selectElement.appendChild(option);
            });
            
            departmentsLoaded = true;
        })
        .catch(error => {
            console.error('Error loading departments:', error);
            showError('Failed to load departments. Please try again.');
        });
    }

    function handleSubmit(event) {
        event.preventDefault();
        
        const token = localStorage.getItem('api_token');
        if (!token) {
            showError('Session expired. Please log in again.');
            return;
        }

        // Validate inputs
        const departmentId = document.getElementById('department').value;
        const fiscalYear = document.getElementById('fiscalYear').value;
        const amount = document.getElementById('amount').value;
        const status = document.getElementById('status').value;
        const notes = document.getElementById('notes').value;

        if (!departmentId || !fiscalYear || !amount || !status) {
            showError('Please fill in all required fields.');
            return;
        }

        if (parseFloat(amount) <= 0) {
            showError('Allocated amount must be greater than 0.');
            return;
        }

        // Submit form
        axios.post('/api/budget-allocations', {
            department_id: departmentId,
            fiscal_year: fiscalYear,
            allocated_amount: parseFloat(amount),
            status: status,
            notes: notes || null
        }, {
            headers: { 'Authorization': 'Bearer ' + token }
        })
        .then(response => {
            showSuccess('Budget allocation created successfully!');
            setTimeout(() => {
                window.location.href = '/budget-allocations';
            }, 1500);
        })
        .catch(error => {
            console.error('Error creating allocation:', error);
            const message = error.response?.data?.message || 
                           error.response?.data?.errors?.department_id?.[0] ||
                           'Failed to create budget allocation. Please try again.';
            showError(message);
        });
    }

    function showSuccess(message) {
        document.getElementById('successText').textContent = message;
        document.getElementById('successMessage').style.display = 'block';
        document.getElementById('errorMessage').style.display = 'none';
    }

    function showError(message) {
        document.getElementById('errorText').textContent = message;
        document.getElementById('errorMessage').style.display = 'block';
        document.getElementById('successMessage').style.display = 'none';
    }
</script>
@endsection

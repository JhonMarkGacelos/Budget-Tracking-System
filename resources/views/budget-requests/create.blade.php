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
                    <h1 class="text-3xl font-bold mb-6">Create Budget Request</h1>

                    <form id="budgetRequestForm" class="space-y-6">
                        <div>
                            <label for="title" class="block text-sm font-medium text-gray-700">Request Title</label>
                            <input type="text" id="title" name="title" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                            <textarea id="description" name="description" rows="4" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"></textarea>
                        </div>

                        <div>
                            <label for="requested_amount" class="block text-sm font-medium text-gray-700">Requested Amount</label>
                            <input type="number" id="requested_amount" name="requested_amount" step="0.01" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <div>
                            <label for="fiscal_year" class="block text-sm font-medium text-gray-700">Fiscal Year</label>
                            <input type="text" id="fiscal_year" name="fiscal_year" placeholder="e.g., 2024-2025" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <div class="flex gap-4">
                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                <i class="fas fa-check mr-2"></i>Submit Request
                            </button>
                            <a href="{{ route('budget-requests.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
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
    function submitBudgetRequest(e) {
        e.preventDefault();

        let apiToken = localStorage.getItem('api_token');
        
        if (!apiToken) {
            alert('Error: Please log in again to submit the request');
            window.location.href = '/login';
            return;
        }

        const formData = {
            title: document.getElementById('title').value,
            description: document.getElementById('description').value,
            amount: parseFloat(document.getElementById('requested_amount').value),
            fiscal_year: document.getElementById('fiscal_year').value
        };

        // Validate that amount is a number
        if (isNaN(formData.amount) || formData.amount <= 0) {
            alert('Error: Please enter a valid amount greater than 0');
            return;
        }

        axios.post('/api/budget-requests', formData, {
            headers: {
                'Authorization': 'Bearer ' + apiToken,
                'Content-Type': 'application/json'
            }
        })
        .then(response => {
            alert('Budget request submitted successfully!');
            window.location.href = '/budget-requests';
        })
        .catch(error => {
            console.error('Error submitting request:', error);
            const errorMsg = error.response?.data?.message || 
                           (error.response?.data?.errors ? Object.values(error.response.data.errors).flat().join(', ') : error.message);
            alert('Error submitting budget request: ' + errorMsg);
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('budgetRequestForm').addEventListener('submit', submitBudgetRequest);
    });

    window.addEventListener('tokenReady', function() {
        // Token is ready, form can be used
    });
</script>
@endsection

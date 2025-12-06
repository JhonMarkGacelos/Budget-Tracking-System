@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-100 p-8">
    <div class="max-w-4xl mx-auto bg-white rounded-lg shadow-lg p-6">
        <h1 class="text-3xl font-bold mb-6">Token Debug Page</h1>
        
        <div class="space-y-4">
            <div class="bg-blue-50 p-4 rounded border border-blue-200">
                <h2 class="font-semibold text-blue-900 mb-2">Session Info</h2>
                <pre class="bg-white p-3 rounded text-sm overflow-auto">
Session has 'api_token': {{ session()->has('api_token') ? 'YES' : 'NO' }}
Authenticated: {{ auth()->check() ? 'YES' : 'NO' }}
User: {{ auth()->user()?->name ?? 'Not authenticated' }}
                </pre>
            </div>

            <div class="bg-green-50 p-4 rounded border border-green-200">
                <h2 class="font-semibold text-green-900 mb-2">localStorage Debug</h2>
                <div id="localStorageInfo" class="bg-white p-3 rounded text-sm">
                    <p>Checking localStorage...</p>
                </div>
            </div>

            <div class="bg-yellow-50 p-4 rounded border border-yellow-200">
                <h2 class="font-semibold text-yellow-900 mb-2">Token Event Log</h2>
                <div id="eventLog" class="bg-white p-3 rounded text-sm font-mono">
                    <p>Listening for events...</p>
                </div>
            </div>

            <div class="bg-purple-50 p-4 rounded border border-purple-200">
                <h2 class="font-semibold text-purple-900 mb-2">Test API Call</h2>
                <button onclick="testApiCall()" class="bg-purple-600 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded">
                    Test API Call
                </button>
                <div id="apiTestResult" class="mt-3 bg-white p-3 rounded text-sm font-mono">
                    <p>Click button to test...</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    let eventLog = [];

    function log(message) {
        eventLog.push(`${new Date().toLocaleTimeString()}: ${message}`);
        document.getElementById('eventLog').innerHTML = eventLog.join('<br>');
    }

    function updateLocalStorage() {
        const token = localStorage.getItem('api_token');
        document.getElementById('localStorageInfo').innerHTML = `
            <strong>api_token:</strong> ${token ? token.substring(0, 50) + '...' : 'NOT SET'}
        `;
    }

    function testApiCall() {
        const token = localStorage.getItem('api_token');
        const result = document.getElementById('apiTestResult');
        
        if (!token) {
            result.innerHTML = '<span class="text-red-600">ERROR: No token in localStorage</span>';
            return;
        }

        result.innerHTML = '<span class="text-blue-600">Testing...</span>';

        axios.get('/api/users', {
            headers: {
                'Authorization': 'Bearer ' + token
            }
        })
        .then(response => {
            result.innerHTML = `<span class="text-green-600">✓ SUCCESS</span><br>Users count: ${response.data.data.length}`;
        })
        .catch(error => {
            result.innerHTML = `<span class="text-red-600">✗ ERROR: ${error.response?.status} - ${error.response?.data?.message || error.message}</span>`;
        });
    }

    // Log initialization
    log('Page loaded');

    // Listen for tokenReady
    window.addEventListener('tokenReady', function() {
        log('✓ tokenReady event fired');
        updateLocalStorage();
    });

    document.addEventListener('DOMContentLoaded', function() {
        log('DOMContentLoaded fired');
        updateLocalStorage();
        
        // Check for token after delay
        setTimeout(() => {
            const token = localStorage.getItem('api_token');
            if (token) {
                log('✓ Token found in localStorage after delay');
            } else {
                log('✗ Token NOT found after delay');
            }
        }, 100);
    });

    // Initial check
    setTimeout(() => {
        updateLocalStorage();
        if (localStorage.getItem('api_token')) {
            log('✓ Token exists in localStorage');
        }
    }, 50);
</script>
@endsection

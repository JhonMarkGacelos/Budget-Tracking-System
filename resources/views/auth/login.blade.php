@extends('layouts.app')

@section('content')
<div class="min-h-screen relative flex items-center justify-center overflow-hidden" style="background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);">
    <!-- Background Pattern -->
    <div class="absolute inset-0 opacity-10">
        <img src="/images/background.webp" alt="Background" class="w-full h-full object-cover">
    </div>
    
    <div class="relative w-full max-w-md px-4">
        <!-- Logo/Title -->
        <div class="text-center mb-8 animate-fade-in">
            <div class="mb-4 flex justify-center">
                <img src="/images/SSU-Logo.webp" alt="SSU Logo" class="h-24 w-24 drop-shadow-2xl">
            </div>
            <h1 class="text-4xl font-bold text-white mb-2">
                SSU Budget Tracking System
            </h1>
            <p class="text-blue-100 text-lg">Samar State University</p>
        </div>

        <!-- Login Card -->
        <div class="bg-white rounded-2xl shadow-2xl p-8 backdrop-blur-sm">
            <h2 class="text-2xl font-bold text-gray-800 mb-6">Welcome Back</h2>

            @if ($errors->any())
                <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-4">
                @csrf

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                        Email Address
                    </label>
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        value="{{ old('email') }}"
                        placeholder="admin@budgettracking.com"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('email') border-red-500 @enderror"
                        required
                    >
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                        Password
                    </label>
                    <input 
                        type="password" 
                        id="password" 
                        name="password"
                        placeholder="••••••••"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('password') border-red-500 @enderror"
                        required
                    >
                    @error('password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Remember Me -->
                <div class="flex items-center">
                    <input 
                        type="checkbox" 
                        id="remember" 
                        name="remember"
                        class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                    >
                    <label for="remember" class="ml-2 block text-sm text-gray-700">
                        Remember me
                    </label>
                </div>

                <!-- Login Button -->
                <button 
                    type="submit"
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg transition duration-200 mt-6"
                >
                    <i class="fas fa-sign-in-alt mr-2"></i>Sign In
                </button>
            </form>

            <!-- Demo Credentials -->
            <div class="mt-6 pt-6 border-t border-gray-200">
                <p class="text-sm text-gray-600 mb-3">Demo Credentials:</p>
                <div class="space-y-2 text-sm bg-gray-50 p-3 rounded">
                    <p><strong>Admin:</strong> admin@budgettracking.com / admin@123456</p>
                    <p class="text-xs text-gray-500 mt-2">Create department and faculty accounts to test different roles.</p>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="text-center mt-8 text-blue-100">
            <p>&copy; 2025 Budget Tracking System. All rights reserved.</p>
        </div>
    </div>
</div>
@endsection

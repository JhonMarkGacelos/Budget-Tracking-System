@extends('layouts.app')

@section('content')
<div class="min-h-screen relative flex flex-col items-center justify-center overflow-hidden">
    <!-- Background Image with Overlay -->
    <div class="absolute inset-0">
        <img src="/images/background.webp" alt="SSU Background" class="w-full h-full object-cover">
        <div class="absolute inset-0 bg-gradient-to-br from-blue-900 via-blue-800 to-blue-900 opacity-90"></div>
    </div>

    <div class="relative z-10 text-center text-white max-w-4xl px-4">
        <!-- Logo and Title -->
        <div class="mb-12 animate-fade-in">
            <div class="mb-6 flex justify-center">
                <img src="/images/SSU-Logo.webp" alt="SSU Logo" class="h-32 w-32 drop-shadow-2xl">
            </div>
            <h1 class="text-5xl md:text-6xl font-bold mb-4">SSU Budget Tracking System</h1>
            <p class="text-2xl text-blue-200 mb-2">Samar State University</p>
            <p class="text-xl text-blue-300">Streamlined budget management for institutional excellence</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
            <div class="bg-white bg-opacity-10 backdrop-blur-lg rounded-xl p-6 border border-white border-opacity-30 shadow-xl hover:bg-opacity-20 transition-all duration-300 transform hover:-translate-y-1">
                <i class="fas fa-lock text-4xl text-blue-300 mb-4"></i>
                <h3 class="text-xl font-semibold mb-3">Hierarchical Control</h3>
                <p class="text-blue-200">Admin, Department, and Faculty levels with granular permissions</p>
            </div>

            <div class="bg-white bg-opacity-10 backdrop-blur-lg rounded-xl p-6 border border-white border-opacity-30 shadow-xl hover:bg-opacity-20 transition-all duration-300 transform hover:-translate-y-1">
                <i class="fas fa-check-circle text-4xl text-blue-300 mb-4"></i>
                <h3 class="text-xl font-semibold mb-3">Approval Workflows</h3>
                <p class="text-blue-200">Multi-level approval process: Faculty → Department → Admin</p>
            </div>

            <div class="bg-white bg-opacity-10 backdrop-blur-lg rounded-xl p-6 border border-white border-opacity-30 shadow-xl hover:bg-opacity-20 transition-all duration-300 transform hover:-translate-y-1">
                <i class="fas fa-chart-bar text-4xl text-blue-300 mb-4"></i>
                <h3 class="text-xl font-semibold mb-3">Real-time Analytics</h3>
                <p class="text-blue-200">Track allocations, spending, and budget status instantly</p>
            </div>
        </div>

        <div class="space-y-4">
            @auth
                <div class="text-lg mb-6 bg-white bg-opacity-10 backdrop-blur-lg rounded-xl p-4 border border-white border-opacity-30">
                    <p class="text-blue-200">Welcome back, <span class="font-bold text-white text-xl">{{ Auth::user()->name }}</span>!</p>
                    <p class="text-blue-300 text-sm mt-1">{{ Auth::user()->department->name ?? 'Samar State University' }}</p>
                </div>
                <a href="{{ route('dashboard') }}" class="inline-block bg-white hover:bg-blue-50 text-blue-900 font-bold py-4 px-10 rounded-xl transition-all duration-300 transform hover:scale-105 shadow-xl">
                    <i class="fas fa-arrow-right mr-2"></i>Go to Dashboard
                </a>
            @else
                <div class="text-lg mb-8 text-blue-200">
                    <p class="text-xl">Manage your institutional budgets efficiently and securely</p>
                </div>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="{{ route('login') }}" class="bg-white hover:bg-blue-50 text-blue-900 font-bold py-4 px-10 rounded-xl transition-all duration-300 transform hover:scale-105 shadow-xl">
                        <i class="fas fa-sign-in-alt mr-2"></i>Sign In
                    </a>
                    <a href="#features" class="bg-white bg-opacity-20 hover:bg-opacity-30 text-white font-bold py-4 px-10 rounded-xl border-2 border-white border-opacity-50 transition-all duration-300 backdrop-blur-lg">
                        <i class="fas fa-info-circle mr-2"></i>Learn More
                    </a>
                </div>
            @endauth
        </div>

        <div class="mt-16 pt-8 border-t border-white border-opacity-30">
            <div class="flex flex-col md:flex-row items-center justify-center gap-6 text-blue-200">
                <div class="flex items-center">
                    <i class="fas fa-shield-alt text-xl mr-2"></i>
                    <span>Enterprise-grade Security</span>
                </div>
                <div class="hidden md:block text-blue-400">•</div>
                <div class="flex items-center">
                    <i class="fas fa-users-cog text-xl mr-2"></i>
                    <span>Role-based Access Control</span>
                </div>
                <div class="hidden md:block text-blue-400">•</div>
                <div class="flex items-center">
                    <i class="fas fa-chart-line text-xl mr-2"></i>
                    <span>Real-time Reporting</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


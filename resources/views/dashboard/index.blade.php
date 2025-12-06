@extends('layouts.app')

@section('content')
@include('components.sidebar')

<div class="min-h-screen">
    <div class="lg:ml-64">
        <div class="lg:hidden h-16"></div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Welcome Header -->
        <div class="gradient-bg text-white rounded-2xl shadow-2xl p-8 mb-8 relative overflow-hidden">
            <div class="absolute top-0 right-0 opacity-10">
                <img src="/images/SSU-Logo.webp" alt="SSU Logo" class="h-40 w-40">
            </div>
            <div class="relative z-10">
                <h2 class="text-3xl md:text-4xl font-bold mb-2">Welcome, {{ auth()->user()->name }}!</h2>
                <p class="text-blue-100 text-lg">{{ auth()->user()->department->name ?? 'Samar State University' }}</p>
                <p class="text-blue-200 text-sm mt-1">Manage your budget requests and approvals efficiently</p>
            </div>
        </div>

        <!-- Dashboard Content based on Role -->
        @if(auth()->user()->isAdmin())
            @include('dashboard.admin')
        @elseif(auth()->user()->isDepartment())
            @include('dashboard.department')
        @else
            @include('dashboard.faculty')
        @endif
        </div>
    </div>
</div>

<script>
    // Store the token passed from session for use in Axios calls
    @if(session()->has('api_token'))
        localStorage.setItem('api_token', '{{ session("api_token") }}');
    @endif
</script>
@endsection

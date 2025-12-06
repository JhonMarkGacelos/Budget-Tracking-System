@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-600 to-blue-800 flex items-center justify-center">
    <div class="text-center text-white">
        <div class="mb-6">
            <i class="fas fa-exclamation-circle text-6xl text-yellow-300"></i>
        </div>
        <h1 class="text-4xl font-bold mb-4">{{ $code ?? '403' }} - {{ $message ?? 'Access Denied' }}</h1>
        <p class="text-xl text-blue-100 mb-8">{{ $description ?? 'You do not have permission to access this resource.' }}</p>
        
        <div class="space-x-4">
            <a href="{{ route('dashboard') }}" class="inline-block bg-white text-blue-600 hover:bg-blue-50 font-bold py-2 px-6 rounded-lg transition">
                <i class="fas fa-home mr-2"></i>Go to Dashboard
            </a>
            <a href="{{ route('logout') }}" class="inline-block bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-6 rounded-lg transition">
                <i class="fas fa-sign-out-alt mr-2"></i>Logout
            </a>
        </div>
    </div>
</div>
@endsection

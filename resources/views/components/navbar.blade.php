<nav class="gradient-bg shadow-lg sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
            <!-- Logo and Title -->
            <div class="flex items-center space-x-4">
                <img src="/images/SSU-Logo.webp" alt="SSU Logo" class="h-10 w-10">
                <div>
                    <h1 class="text-white font-bold text-lg hidden sm:block">SSU Budget Tracking System</h1>
                    <p class="text-blue-200 text-xs hidden md:block">Samar State University</p>
                </div>
            </div>

            <!-- Navigation Links -->
            <div class="hidden md:flex items-center space-x-6">
                @auth
                    <a href="/dashboard" class="text-white hover:text-blue-200 transition-colors duration-200 font-medium">
                        <i class="fas fa-home mr-2"></i>Dashboard
                    </a>
                    
                    @if(auth()->user()->role === 'admin')
                        <a href="/users" class="text-white hover:text-blue-200 transition-colors duration-200 font-medium">
                            <i class="fas fa-users mr-2"></i>Users
                        </a>
                        <a href="/departments" class="text-white hover:text-blue-200 transition-colors duration-200 font-medium">
                            <i class="fas fa-building mr-2"></i>Departments
                        </a>
                        <a href="/budget-allocations" class="text-white hover:text-blue-200 transition-colors duration-200 font-medium">
                            <i class="fas fa-wallet mr-2"></i>Allocations
                        </a>
                        <a href="/audit-logs" class="text-white hover:text-blue-200 transition-colors duration-200 font-medium">
                            <i class="fas fa-history mr-2"></i>Audit Logs
                        </a>
                        <a href="/analytics/admin" class="text-white hover:text-blue-200 transition-colors duration-200 font-medium">
                            <i class="fas fa-chart-line mr-2"></i>Analytics
                        </a>
                    @endif
                    
                    <a href="/budget-requests" class="text-white hover:text-blue-200 transition-colors duration-200 font-medium">
                        <i class="fas fa-file-invoice mr-2"></i>Requests
                    </a>
                    
                    @if(auth()->user()->role === 'department_head')
                        <a href="/budget-allocations" class="text-white hover:text-blue-200 transition-colors duration-200 font-medium">
                            <i class="fas fa-wallet mr-2"></i>Allocations
                        </a>
                        <a href="/analytics/department" class="text-white hover:text-blue-200 transition-colors duration-200 font-medium">
                            <i class="fas fa-chart-line mr-2"></i>Analytics
                        </a>
                    @endif
                @endauth
            </div>

            <!-- User Menu -->
            <div class="flex items-center space-x-4">
                @auth
                    <div class="hidden sm:flex items-center space-x-3 bg-white bg-opacity-20 rounded-lg px-4 py-2">
                        <div class="text-right">
                            <p class="text-white font-medium text-sm">{{ auth()->user()->name }}</p>
                            <p class="text-blue-200 text-xs">{{ ucfirst(str_replace('_', ' ', auth()->user()->role)) }}</p>
                        </div>
                        <div class="w-10 h-10 bg-white rounded-full flex items-center justify-center">
                            <i class="fas fa-user text-blue-600"></i>
                        </div>
                    </div>
                    
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="bg-white bg-opacity-20 hover:bg-opacity-30 text-white px-4 py-2 rounded-lg transition-all duration-200 flex items-center space-x-2">
                            <i class="fas fa-sign-out-alt"></i>
                            <span class="hidden sm:inline">Logout</span>
                        </button>
                    </form>
                @endauth
            </div>

            <!-- Mobile Menu Button -->
            <button id="mobile-menu-button" class="md:hidden text-white">
                <i class="fas fa-bars text-2xl"></i>
            </button>
        </div>
    </div>

    <!-- Mobile Menu -->
    <div id="mobile-menu" class="hidden md:hidden bg-blue-900 bg-opacity-95">
        <div class="px-4 py-3 space-y-2">
            @auth
                <a href="/dashboard" class="block text-white hover:bg-blue-800 px-3 py-2 rounded-lg transition-colors">
                    <i class="fas fa-home mr-2"></i>Dashboard
                </a>
                
                @if(auth()->user()->role === 'admin')
                    <a href="/users" class="block text-white hover:bg-blue-800 px-3 py-2 rounded-lg transition-colors">
                        <i class="fas fa-users mr-2"></i>Users
                    </a>
                    <a href="/departments" class="block text-white hover:bg-blue-800 px-3 py-2 rounded-lg transition-colors">
                        <i class="fas fa-building mr-2"></i>Departments
                    </a>
                    <a href="/budget-allocations" class="block text-white hover:bg-blue-800 px-3 py-2 rounded-lg transition-colors">
                        <i class="fas fa-wallet mr-2"></i>Allocations
                    </a>
                    <a href="/audit-logs" class="block text-white hover:bg-blue-800 px-3 py-2 rounded-lg transition-colors">
                        <i class="fas fa-history mr-2"></i>Audit Logs
                    </a>
                    <a href="/analytics/admin" class="block text-white hover:bg-blue-800 px-3 py-2 rounded-lg transition-colors">
                        <i class="fas fa-chart-line mr-2"></i>Analytics
                    </a>
                @endif
                
                <a href="/budget-requests" class="block text-white hover:bg-blue-800 px-3 py-2 rounded-lg transition-colors">
                    <i class="fas fa-file-invoice mr-2"></i>Requests
                </a>
                
                @if(auth()->user()->role === 'department_head')
                    <a href="/budget-allocations" class="block text-white hover:bg-blue-800 px-3 py-2 rounded-lg transition-colors">
                        <i class="fas fa-wallet mr-2"></i>Allocations
                    </a>
                    <a href="/analytics/department" class="block text-white hover:bg-blue-800 px-3 py-2 rounded-lg transition-colors">
                        <i class="fas fa-chart-line mr-2"></i>Analytics
                    </a>
                @endif
            @endauth
        </div>
    </div>
</nav>

<script>
    document.getElementById('mobile-menu-button')?.addEventListener('click', function() {
        const menu = document.getElementById('mobile-menu');
        menu.classList.toggle('hidden');
    });
</script>

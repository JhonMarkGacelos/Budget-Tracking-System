<!-- Sidebar -->
<div id="sidebar" class="fixed inset-y-0 left-0 z-40 w-64 bg-gradient-to-b from-blue-900 to-blue-800 shadow-2xl transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out">
    <!-- Logo Section -->
    <div class="flex items-center justify-center h-20 border-b border-blue-700 bg-blue-900">
        <img src="/images/SSU-Logo.webp" alt="SSU Logo" class="h-12 w-12 mr-3">
        <div>
            <h1 class="text-white font-bold text-lg">SSU Budget</h1>
            <p class="text-blue-300 text-xs">Tracking System</p>
        </div>
    </div>

    <!-- Navigation Menu -->
    <nav class="mt-6 px-4">
        @auth
            <!-- User Info -->
            <div class="mb-6 p-4 bg-white bg-opacity-10 rounded-xl">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-white rounded-full flex items-center justify-center">
                        <i class="fas fa-user text-blue-600"></i>
                    </div>
                    <div class="flex-1">
                        <p class="text-white font-medium text-sm truncate">{{ auth()->user()->name }}</p>
                        <p class="text-blue-300 text-xs">{{ ucfirst(str_replace('_', ' ', auth()->user()->role)) }}</p>
                    </div>
                </div>
            </div>

            <!-- Menu Items -->
            <div class="space-y-2">
                <!-- Dashboard -->
                <a href="/dashboard" class="flex items-center px-4 py-3 text-white hover:bg-white hover:bg-opacity-10 rounded-lg transition-all duration-200 group {{ request()->is('dashboard') ? 'bg-white bg-opacity-20' : '' }}">
                    <i class="fas fa-home w-5 text-blue-300 group-hover:text-white"></i>
                    <span class="ml-3">Dashboard</span>
                </a>

                <!-- Budget Requests -->
                <a href="/budget-requests" class="flex items-center px-4 py-3 text-white hover:bg-white hover:bg-opacity-10 rounded-lg transition-all duration-200 group {{ request()->is('budget-requests*') ? 'bg-white bg-opacity-20' : '' }}">
                    <i class="fas fa-file-invoice w-5 text-blue-300 group-hover:text-white"></i>
                    <span class="ml-3">Budget Requests</span>
                </a>

                @if(auth()->user()->role === 'admin' || auth()->user()->role === 'department_head' || auth()->user()->role === 'department')
                    <!-- Budget Allocations -->
                    <a href="/budget-allocations" class="flex items-center px-4 py-3 text-white hover:bg-white hover:bg-opacity-10 rounded-lg transition-all duration-200 group {{ request()->is('budget-allocations*') ? 'bg-white bg-opacity-20' : '' }}">
                        <i class="fas fa-wallet w-5 text-blue-300 group-hover:text-white"></i>
                        <span class="ml-3">Budget Allocations</span>
                    </a>
                @endif

                @if(auth()->user()->role === 'department_head' || auth()->user()->role === 'department')
                    <!-- Add New User (Department Head) -->
                    <a href="/users/create" class="flex items-center px-4 py-3 text-white hover:bg-white hover:bg-opacity-10 rounded-lg transition-all duration-200 group {{ request()->is('users/create') ? 'bg-white bg-opacity-20' : '' }}">
                        <i class="fas fa-user-plus w-5 text-blue-300 group-hover:text-white"></i>
                        <span class="ml-3">Add New User</span>
                    </a>

                    <!-- Department Analytics -->
                    <a href="/analytics/department" class="flex items-center px-4 py-3 text-white hover:bg-white hover:bg-opacity-10 rounded-lg transition-all duration-200 group {{ request()->is('analytics/department*') ? 'bg-white bg-opacity-20' : '' }}">
                        <i class="fas fa-chart-line w-5 text-blue-300 group-hover:text-white"></i>
                        <span class="ml-3">Budget Analysis</span>
                    </a>
                @endif

                @if(auth()->user()->role === 'admin')
                    <!-- Users Management -->
                    <a href="/users" class="flex items-center px-4 py-3 text-white hover:bg-white hover:bg-opacity-10 rounded-lg transition-all duration-200 group {{ request()->is('users*') ? 'bg-white bg-opacity-20' : '' }}">
                        <i class="fas fa-users w-5 text-blue-300 group-hover:text-white"></i>
                        <span class="ml-3">Users</span>
                    </a>

                    <!-- Audit Logs -->
                    <a href="/audit-logs" class="flex items-center px-4 py-3 text-white hover:bg-white hover:bg-opacity-10 rounded-lg transition-all duration-200 group {{ request()->is('audit-logs*') ? 'bg-white bg-opacity-20' : '' }}">
                        <i class="fas fa-history w-5 text-blue-300 group-hover:text-white"></i>
                        <span class="ml-3">Audit Logs</span>
                    </a>

                    <!-- Admin Analytics -->
                    <a href="/analytics/admin" class="flex items-center px-4 py-3 text-white hover:bg-white hover:bg-opacity-10 rounded-lg transition-all duration-200 group {{ request()->is('analytics/admin*') ? 'bg-white bg-opacity-20' : '' }}">
                        <i class="fas fa-chart-line w-5 text-blue-300 group-hover:text-white"></i>
                        <span class="ml-3">Budget Analysis</span>
                    </a>
                @endif
            </div>

            <!-- Logout -->
            <div class="mt-6 pt-6 border-t border-blue-700">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full flex items-center px-4 py-3 text-white hover:bg-red-500 hover:bg-opacity-20 rounded-lg transition-all duration-200 group">
                        <i class="fas fa-sign-out-alt w-5 text-red-400 group-hover:text-red-300"></i>
                        <span class="ml-3">Logout</span>
                    </button>
                </form>
            </div>
        @endauth
    </nav>
</div>

<!-- Sidebar Overlay (Mobile) -->
<div id="sidebar-overlay" class="fixed inset-0 bg-black bg-opacity-50 z-30 lg:hidden hidden" onclick="toggleSidebar()"></div>

<!-- Mobile Header -->
<div class="lg:hidden fixed top-0 left-0 right-0 z-20 bg-gradient-to-r from-blue-900 to-blue-800 shadow-lg">
    <div class="flex items-center justify-between h-16 px-4">
        <button onclick="toggleSidebar()" class="text-white">
            <i class="fas fa-bars text-2xl"></i>
        </button>
        <div class="flex items-center space-x-2">
            <img src="/images/SSU-Logo.webp" alt="SSU Logo" class="h-8 w-8">
            <span class="text-white font-bold">SSU Budget</span>
        </div>
        <div class="w-10"></div>
    </div>
</div>

<script>
    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebar-overlay');
        
        sidebar.classList.toggle('-translate-x-full');
        overlay.classList.toggle('hidden');
    }
</script>

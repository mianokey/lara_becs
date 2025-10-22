<div class="w-64 bg-white shadow-lg border-r border-gray-100 min-h-screen flex flex-col">
    {{-- Logo Header --}}
    <div class="px-6 py-4 border-b border-gray-100">
        <div class="flex items-center space-x-3">
            <div class="w-10 h-10 bg-becs-navy rounded-full flex items-center justify-center">
                <span class="text-white font-bold text-lg">BC</span>
            </div>

            <div>
                <h1 class="text-lg font-semibold text-gray-900">BECS Consultancy</h1>
                <p class="text-xs text-gray-500">LLP</p>
            </div>
        </div>
    </div>

    {{-- User Section --}}
    <div class="px-6 py-4 border-b border-gray-100">
        <div class="flex items-center space-x-3">
            <div class="w-8 h-8 bg-yellow-500 rounded-full flex items-center justify-center text-white font-semibold">
                {{ strtoupper(substr(Auth::user()->first_name ?? 'U', 0, 1)) }}
            </div>
            <div>
                <p class="text-sm font-medium text-gray-900">{{ Auth::user()->name ?? 'Guest' }}</p>
                <p class="text-xs text-gray-500 capitalize">{{ Auth::user()->getRoleNames()->first() ?? 'User' }}</p>
            </div>
        </div>
    </div>

    {{-- Navigation --}}
    <nav class="flex-1 px-4 py-6 space-y-2">
        <a href="{{ route('home') }}" class="block px-3 py-2 rounded-lg hover:bg-blue-50 hover:text-blue-700">🏠 Dashboard</a>
        <a href="{{ route('tasks.index') }}" class="block px-3 py-2 rounded-lg hover:bg-blue-50 hover:text-blue-700">✅ Tasks</a>
        @role('admin|director')
        {{-- <a href="#" class="block px-3 py-2 rounded-lg hover:bg-blue-50 hover:text-blue-700">⚙️ Admin Panel</a> --}}
      
    <div x-data="{ open: false }" class="space-y-1">
    <!-- Menu Title -->
    <button @click="open = !open"
            class="w-full flex justify-between items-center px-3 py-2 rounded-lg hover:bg-blue-50 hover:text-blue-700 focus:outline-none">
        <span>👤 Users</span>
        <svg :class="{ 'rotate-90': open }" class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
        </svg>
    </button>

    <!-- Submenu -->
    <div x-show="open" x-cloak x-collapse class="pl-4 space-y-1">
        <a href="{{ route('users.create') }}" class="block px-3 py-2 rounded-lg hover:bg-blue-50 hover:text-blue-700">➕ Create User</a>
        {{-- <a href="{{ route('users.index') }}" class="block px-3 py-2 rounded-lg hover:bg-blue-50 hover:text-blue-700">📋 View Users</a> --}}
    </div>
</div>
 <div x-data="{ open: false }" class="space-y-1">
    <!-- Menu Title -->
    <button @click="open = !open"
            class="w-full flex justify-between items-center px-3 py-2 rounded-lg hover:bg-blue-50 hover:text-blue-700 focus:outline-none">
        <span>📁 Projects</span>
        <svg :class="{ 'rotate-90': open }" class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
        </svg>
    </button>

    <!-- Submenu -->
    <div x-show="open" x-cloak x-collapse class="pl-4 space-y-1">
        <a href="{{ route('projects.create') }}" class="block px-3 py-2 rounded-lg hover:bg-blue-50 hover:text-blue-700"> + New Project</a>
        {{-- <a href="{{ route('projects.create') }}" class="block px-3 py-2 rounded-lg hover:bg-blue-50 hover:text-blue-700"> + Deliverables Mgt</a>
        <a href="{{ route('projects.create') }}" class="block px-3 py-2 rounded-lg hover:bg-blue-50 hover:text-blue-700"> + Project Overview</a>
        <a href="{{ route('projects.create') }}" class="block px-3 py-2 rounded-lg hover:bg-blue-50 hover:text-blue-700"> + Analytics</a> --}}
    </div>
</div>
    @endrole
    </nav>

    {{-- Footer --}}
    <div class="p-4 border-t border-gray-100">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="w-full text-left text-red-600 hover:text-red-800 text-sm">
                🔒 Logout
            </button>
        </form>
    </div>
</div>

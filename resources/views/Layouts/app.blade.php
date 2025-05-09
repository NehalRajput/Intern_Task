<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }}</title>
    
    <!-- Scripts and Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <!-- Pusher -->
    <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
    
    <!-- Alpine.js -->
    <script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.x.x/dist/alpine.min.js" defer></script>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @stack('styles')
</head>
<body class="bg-gray-100">
    <div>
        <!-- Navigation -->
        @auth
            <nav class="bg-white shadow">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between h-16">
                        <div class="flex">
                            <!-- Logo -->
                            <div class="flex-shrink-0 flex items-center">
                                <a href="{{ route('dashboard') }}">
                                   </a>
                            </div>

                            <!-- Navigation Links -->
                            <div class="hidden sm:ml-6 sm:flex sm:space-x-8">
                                @if(Auth::guard('user')->check())
                                    <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                                        Dashboard
                                    </a>
                                    <a href="{{ route('intern.tasks.index') }}" class="nav-link {{ request()->routeIs('intern.tasks.*') ? 'active' : '' }}">
                                        Tasks
                                    </a>
                                    <a href="{{ route('chat') }}" class="nav-link {{ request()->routeIs('chat') ? 'active' : '' }}">
                                        Messages
                                        @if(auth()->user()->receivedMessages()->whereNull('read_at')->count() > 0)
                                            <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                {{ auth()->user()->receivedMessages()->whereNull('read_at')->count() }}
                                            </span>
                                        @endif
                                    </a>
                                  
                                @elseif(Auth::guard('admin')->check())
                                    <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                                        Dashboard
                                    </a>

                                    @can('read_interns')
                                    <a href="{{ route('admin.interns.index') }}" class="nav-link {{ request()->routeIs('admin.interns.*') ? 'active' : '' }}">
                                        Intern
                                    </a>
                                    @endcan

                                    @can('read_admins')
                                    <a href="{{ route('admin.admins.index') }}" class="nav-link {{ request()->routeIs('admin.admins.*') ? 'active' : '' }}">
                                        Admins
                                    </a>
                                    @endcan

                                    @can('read_tasks')
                                    <a href="{{ route('admin.tasks.index') }}" class="nav-link {{ request()->routeIs('admin.tasks.*') ? 'active' : '' }}">
                                        Tasks
                                    </a>
                                    @endcan

                                    <a href="{{ route('chat') }}" class="nav-link {{ request()->routeIs('chat') ? 'active' : '' }}">
                                        Messages
                                        @if(auth()->user()->receivedMessages()->whereNull('read_at')->count() > 0)
                                            <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                {{ auth()->user()->receivedMessages()->whereNull('read_at')->count() }}
                                            </span>
                                        @endif
                                    </a>
                                @endif
                            </div>
                        </div>

                        <!-- User Dropdown -->
                        <div class="hidden sm:ml-6 sm:flex sm:items-center">
                            <div x-data="{ open: false }" class="ml-3 relative">
                                <div>
                                    <button @click="open = !open" class="flex text-sm border-2 border-transparent rounded-full focus:outline-none focus:border-gray-300">
                                        <span class="text-gray-700">{{ auth()->user()->name }}</span>
                                    </button>
                                </div>
                                <div x-show="open" @click.away="open = false" class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg">
                                    <div class="py-1 rounded-md bg-white shadow-xs">
                                        <form method="POST" action="{{ auth()->user()->isAdmin() ? route('admin.logout') : route('logout') }}">
                                            @csrf
                                            <button type="submit" class="block w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                Logout
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </nav>
        @endauth

        <!-- Page Content -->
        <main class="py-4">
            @yield('content')
        </main>
    </div>

    <style>
        .nav-link {
            @apply inline-flex items-center px-1 pt-1 text-sm font-medium text-gray-500 hover:text-gray-700;
        }
        .nav-link.active {
            @apply border-b-2 border-indigo-500 text-gray-900;
        }
    </style>

    @stack('scripts')
</body>
</html>
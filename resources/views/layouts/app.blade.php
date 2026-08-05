<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name', 'POSApp') }} - Employee Management</title>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <style>
            body {
                font-family: 'Plus Jakarta Sans', sans-serif;
            }
        </style>
    </head>
    <body class="h-full antialiased bg-slate-50 text-slate-800 flex flex-col min-h-screen">
        <!-- Top Navigation Bar -->
        <header class="sticky top-0 z-30 bg-white/95 backdrop-blur-md border-b border-slate-200/80 shadow-xs">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between h-16">
                    <!-- Brand Logo -->
                    <div class="flex items-center space-x-3">
                        <a href="{{ route('employees.index') }}" class="flex items-center space-x-2.5">
                            <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-indigo-600 via-indigo-700 to-violet-600 flex items-center justify-center text-white shadow-md shadow-indigo-500/20 font-bold text-lg">
                                P
                            </div>
                            <div>
                                <span class="font-bold text-lg tracking-tight text-slate-900 block leading-none">POS<span class="text-indigo-600">App</span></span>
                                <span class="text-[10px] font-semibold text-slate-400 uppercase tracking-widest block mt-0.5">Staff Hub</span>
                            </div>
                        </a>
                    </div>

                    <!-- Navigation Links -->
                    <nav class="flex items-center space-x-1 sm:space-x-2 overflow-x-auto">
                        <a href="{{ route('dashboard') }}" class="px-3 py-2 rounded-lg text-sm font-semibold transition-all duration-150 {{ request()->routeIs('dashboard') ? 'bg-indigo-50 text-indigo-700 font-bold' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                            Dashboard
                        </a>
                        <a href="{{ route('pos.index') }}" class="px-3 py-2 rounded-lg text-sm font-semibold transition-all duration-150 {{ request()->routeIs('pos.*') ? 'bg-indigo-50 text-indigo-700 font-bold' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                            POS
                        </a>
                        <a href="{{ route('orders.index') }}" class="px-3 py-2 rounded-lg text-sm font-semibold transition-all duration-150 {{ request()->routeIs('orders.*') ? 'bg-indigo-50 text-indigo-700 font-bold' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                            Orders
                        </a>
                        <a href="{{ route('products.index') }}" class="px-3 py-2 rounded-lg text-sm font-semibold transition-all duration-150 {{ request()->routeIs('products.*', 'categories.*') ? 'bg-indigo-50 text-indigo-700 font-bold' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                            Inventory
                        </a>
                        <a href="{{ route('customers.index') }}" class="px-3 py-2 rounded-lg text-sm font-semibold transition-all duration-150 {{ request()->routeIs('customers.*') ? 'bg-indigo-50 text-indigo-700 font-bold' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                            Customers
                        </a>
                        <a href="{{ route('employees.index') }}" class="px-3 py-2 rounded-lg text-sm font-semibold transition-all duration-150 {{ request()->routeIs('employees.*') ? 'bg-indigo-50 text-indigo-700 font-bold' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                            Staff
                        </a>
                    </nav>
                </div>
            </div>
        </header>

        <!-- Main Content Wrapper -->
        <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Flash Notification Messages -->
            @if (session('success'))
                <div class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50/90 p-4 shadow-xs flex items-center justify-between transition-all" id="flash-banner">
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 rounded-lg bg-emerald-500/15 text-emerald-700 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                        <p class="text-sm font-medium text-emerald-900">{{ session('success') }}</p>
                    </div>
                    <button onclick="document.getElementById('flash-banner').remove()" class="text-emerald-700 hover:text-emerald-900 p-1 rounded-lg hover:bg-emerald-100/60">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            @endif

            @yield('content')
        </main>

        <!-- Footer -->
        <footer class="mt-auto bg-white border-t border-slate-200/80 py-6 text-center text-xs text-slate-500">
            <div class="max-w-7xl mx-auto px-4">
                <p>&copy; {{ date('Y') }} POSApp - Point of Sale & Staff Management System</p>
            </div>
        </footer>
    </body>
</html>

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name', 'POSApp') }}</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-slate-50 text-slate-900">
        <header class="bg-white border-b shadow-sm">
            <div class="container mx-auto px-4 py-4 flex items-center justify-between">
                <a href="{{ url('/') }}" class="font-semibold text-lg text-slate-900">{{ config('app.name', 'POSApp') }}</a>
                <nav class="space-x-4 text-sm text-slate-600">
                    <a href="{{ route('employees.index') }}" class="hover:text-slate-900">Employees</a>
                </nav>
            </div>
        </header>
        <main class="container mx-auto px-4 py-6">
            @if (session('success'))
                <div class="mb-6 rounded border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
                    {{ session('success') }}
                </div>
            @endif

            @yield('content')
        </main>
    </body>
</html>

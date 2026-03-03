<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', ($header ?? 'Admin') . ' - ' . config('app.name'))</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="font-sans antialiased bg-slate-50 text-slate-800">
    <div class="min-h-screen flex">
        {{-- Sidebar fixa --}}
        <aside class="hidden lg:flex lg:flex-shrink-0 lg:flex-col w-64 bg-slate-900 border-r border-slate-700">
            <div class="flex items-center gap-2 h-16 px-6 border-b border-slate-700">
                <span class="bg-indigo-500 w-9 h-9 rounded-lg flex items-center justify-center text-white font-bold text-sm">H</span>
                <span class="text-white font-semibold">Admin</span>
            </div>
            <nav class="flex-1 py-4 overflow-y-auto">
                <p class="px-4 mb-2 text-xs font-semibold text-slate-400 uppercase tracking-wider">Principal</p>
                <ul class="space-y-0.5 px-3">
                    <li><a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('admin.dashboard') ? 'bg-slate-700 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">Dashboard</a></li>
                    <li><a href="{{ route('admin.properties.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('admin.properties.*') ? 'bg-slate-700 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">Imóveis</a></li>
                    <li><a href="{{ route('admin.owners.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('admin.owners.*') ? 'bg-slate-700 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">Owners</a></li>
                    <li><a href="{{ route('admin.reservations.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('admin.reservations.*') ? 'bg-slate-700 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">Reservas</a></li>
                </ul>
                <p class="px-4 mt-6 mb-2 text-xs font-semibold text-slate-400 uppercase tracking-wider">Financeiro</p>
                <ul class="space-y-0.5 px-3">
                    <li><a href="{{ route('admin.markup-rules.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('admin.markup-rules.*') ? 'bg-slate-700 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">Regras de preço</a></li>
                </ul>
            </nav>
            <div class="p-3 border-t border-slate-700">
                <a href="{{ route('marketplace.index') }}" target="_blank" class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm text-slate-300 hover:bg-slate-800 hover:text-white">Ver site →</a>
                <a href="{{ route('profile.edit') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm text-slate-300 hover:bg-slate-800 hover:text-white">Perfil</a>
                <form method="POST" action="{{ route('logout') }}" class="mt-1">@csrf<button type="submit" class="w-full text-left px-3 py-2 rounded-lg text-sm text-slate-300 hover:bg-slate-800 hover:text-white">Sair</button></form>
            </div>
        </aside>

        <div class="flex-1 flex flex-col min-w-0">
            {{-- Top bar (mobile: menu + título; desktop: só título) --}}
            <header class="bg-white border-b-2 border-slate-300 shadow-sm flex-shrink-0">
                <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8 flex justify-between items-center">
                    <h1 class="text-xl font-bold text-slate-900">{{ $header ?? 'Admin' }}</h1>
                    <div class="flex items-center gap-4 lg:hidden">
                        <a href="{{ route('marketplace.index') }}" target="_blank" class="text-sm text-indigo-600 hover:text-indigo-800">Ver site</a>
                        <a href="{{ route('profile.edit') }}" class="text-sm text-slate-600 hover:text-slate-800">Perfil</a>
                        <form method="POST" action="{{ route('logout') }}" class="inline">@csrf<button type="submit" class="text-sm text-slate-600 hover:text-slate-800">Sair</button></form>
                    </div>
                </div>
            </header>

            <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8">
                @if (session('success'))
                    <div class="mb-6 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3">{{ session('success') }}</div>
                @endif
                @if (session('error'))
                    <div class="mb-6 rounded-lg bg-red-50 border border-red-200 text-red-800 px-4 py-3">{{ session('error') }}</div>
                @endif
                @if (session('info'))
                    <div class="mb-6 rounded-lg bg-sky-50 border border-sky-200 text-sky-800 px-4 py-3">{{ session('info') }}</div>
                @endif
                {{ $slot }}
            </main>
        </div>
    </div>
    @stack('scripts')
</body>
</html>

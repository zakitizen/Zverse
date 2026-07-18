<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') — Zverse</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>body{font-family:'Inter',sans-serif;}</style>
</head>
<body class="bg-gray-50 min-h-screen flex">

{{-- Sidebar --}}
<aside class="w-64 bg-gray-900 min-h-screen flex flex-col shrink-0">
    <div class="p-5 border-b border-gray-800">
        <a href="{{ route('home') }}" class="flex items-center gap-3">
            <div class="flex h-9 w-9 items-center justify-center rounded-2xl bg-gradient-to-br from-orange-500 via-orange-500 to-purple-600 shadow-lg shadow-orange-500/20">
                <svg viewBox="0 0 24 24" class="h-4 w-4 text-white" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M5 6h14"/>
                    <path d="M7 6l5 6-5 6"/>
                    <path d="M17 6l-5 6 5 6"/>
                </svg>
            </div>
            <span class="text-white text-lg font-black tracking-tight">Zverse</span>
        </a>
        <span class="text-xs text-gray-500 mt-1 block">Admin Panel</span>
    </div>

    <nav class="flex-1 p-4 space-y-1">
        <a href="{{ route('admin.dashboard') }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-colors {{ request()->routeIs('admin.dashboard') ? 'bg-orange-600 text-white font-semibold' : 'text-gray-400 hover:text-white hover:bg-gray-800' }}">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="7" height="9" x="3" y="3" rx="1"/><rect width="7" height="5" x="14" y="3" rx="1"/><rect width="7" height="9" x="14" y="12" rx="1"/><rect width="7" height="5" x="3" y="16" rx="1"/></svg>
            Dashboard
        </a>
        <a href="{{ route('admin.articles.create') }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-colors {{ request()->routeIs('admin.articles.create') ? 'bg-orange-600 text-white font-semibold' : 'text-gray-400 hover:text-white hover:bg-gray-800' }}">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14"/><path d="M12 5v14"/></svg>
            Artikel Baru
        </a>
        <a href="{{ route('home') }}" target="_blank"
           class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm text-gray-400 hover:text-white hover:bg-gray-800 transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" x2="21" y1="14" y2="3"/></svg>
            Lihat Website
        </a>
    </nav>

    <div class="p-4 border-t border-gray-800">
        <div class="flex items-center gap-3 mb-3">
            <div class="w-9 h-9 bg-gradient-to-br from-orange-500 to-amber-400 rounded-full flex items-center justify-center text-white text-sm font-bold">A</div>
            <div>
                <p class="text-white text-sm font-semibold">Admin</p>
                <p class="text-gray-500 text-xs">Super Admin</p>
            </div>
        </div>
        <form action="{{ route('admin.logout') }}" method="POST">
            @csrf
            <button class="w-full flex items-center gap-2 text-gray-400 hover:text-red-400 text-sm transition-colors px-2 py-1.5 rounded-lg hover:bg-gray-800">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" x2="9" y1="12" y2="12"/></svg>
                Keluar
            </button>
        </form>
    </div>
</aside>

{{-- Main Content --}}
<div class="flex-1 flex flex-col min-h-screen overflow-x-auto">
    <header class="bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between">
        <h1 class="text-gray-900 text-lg font-bold">@yield('title', 'Dashboard')</h1>
        <div class="flex items-center gap-3 text-sm text-gray-500">
            @if(session('success'))
            <span class="text-green-600 font-semibold bg-green-50 px-3 py-1 rounded-full border border-green-200">✓ {{ session('success') }}</span>
            @endif
            @if(session('error'))
            <span class="text-red-600 font-semibold bg-red-50 px-3 py-1 rounded-full border border-red-200">✗ {{ session('error') }}</span>
            @endif
        </div>
    </header>
    <main class="flex-1 p-6">
        @yield('content')
    </main>
</div>

@stack('scripts')
</body>
</html>

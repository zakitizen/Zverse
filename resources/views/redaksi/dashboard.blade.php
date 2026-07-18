<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Dashboard Redaksi — Zverse</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    },
                    colors: {
                        primary: { 500: '#f97316', 600: '#ea580c' },
                        success: { 500: '#10b981', 600: '#059669' },
                        danger: { 500: '#f43f5e', 600: '#e11d48' },
                        warning: { 500: '#f59e0b', 600: '#d97706' },
                        info: { 500: '#3b82f6', 600: '#2563eb' },
                    }
                }
            }
        }
    </script>
    <style>
        body { font-family: 'Inter', sans-serif; }
        .line-clamp-1 { display: -webkit-box; -webkit-line-clamp: 1; -webkit-box-orient: vertical; overflow: hidden; }
        .line-clamp-2 { display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
        /* Scrollbar Styling for SaaS look */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
    </style>
</head>
<body class="bg-slate-50 min-h-screen text-slate-600 flex overflow-hidden selection:bg-orange-500 selection:text-white">

    {{-- Mobile Sidebar Overlay --}}
    <div id="mobile-overlay" class="fixed inset-0 bg-slate-900/50 z-40 hidden lg:hidden backdrop-blur-sm transition-all duration-300" onclick="toggleSidebar()"></div>

    {{-- Sidebar --}}
    <aside id="sidebar" class="fixed lg:static inset-y-0 left-0 z-50 w-72 bg-slate-950 border-r border-slate-800 flex flex-col shrink-0 transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-out shadow-2xl lg:shadow-none">
        <div class="p-6 border-b border-slate-800/60 flex items-center justify-between">
            <a href="{{ route('home') }}" class="flex items-center gap-3 group">
                @php $zvLogo = 'logozverse.png'; @endphp
                <img src="{{ asset($zvLogo) }}?v={{ filemtime(public_path($zvLogo)) }}" alt="Zverse" class="w-9 h-9 shrink-0 object-contain group-hover:scale-105 transition-transform duration-300" />
                <div class="flex flex-col">
                    <span class="text-white text-lg font-black tracking-tight">Zverse</span>
                    <span class="text-[10px] uppercase tracking-wider text-slate-500 font-semibold">Portal Redaksi</span>
                </div>
            </a>
            <button class="lg:hidden text-slate-400 hover:text-white transition-colors" onclick="toggleSidebar()">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        
        <nav class="flex-1 p-4 space-y-2 overflow-y-auto">
            <p class="text-xs font-semibold text-slate-500 mb-3 px-3 uppercase tracking-wider">Menu Utama</p>
            
            <button onclick="setView('pending'); if(window.innerWidth < 1024) toggleSidebar()" class="view-btn group w-full flex items-center justify-between px-4 py-3 rounded-xl text-sm transition-all duration-300 ease-out" data-view="pending">
                <div class="flex items-center gap-3">
                    <i data-lucide="clock-3" class="w-5 h-5 transition-transform group-hover:scale-110"></i>
                    <span class="font-medium">Menunggu Review</span>
                </div>
                @if($pending->count())
                <span class="bg-amber-500/10 border border-amber-500/20 text-amber-500 text-xs font-bold px-2 py-0.5 rounded-full">{{ $pending->count() }}</span>
                @endif
            </button>

            <button onclick="setView('approved'); if(window.innerWidth < 1024) toggleSidebar()" class="view-btn group w-full flex items-center justify-between px-4 py-3 rounded-xl text-sm transition-all duration-300 ease-out" data-view="approved">
                <div class="flex items-center gap-3">
                    <i data-lucide="badge-check" class="w-5 h-5 transition-transform group-hover:scale-110"></i>
                    <span class="font-medium">Disetujui</span>
                </div>
                @if($approved->count())
                <span class="bg-emerald-500/10 border border-emerald-500/20 text-emerald-500 text-xs font-bold px-2 py-0.5 rounded-full">{{ $approved->count() }}</span>
                @endif
            </button>

            <button onclick="setView('all'); if(window.innerWidth < 1024) toggleSidebar()" class="view-btn group w-full flex items-center justify-between px-4 py-3 rounded-xl text-sm transition-all duration-300 ease-out" data-view="all">
                <div class="flex items-center gap-3">
                    <i data-lucide="newspaper" class="w-5 h-5 transition-transform group-hover:scale-110"></i>
                    <span class="font-medium">Semua Artikel</span>
                </div>
            </button>
        </nav>

        <div class="p-4 border-t border-slate-800/60 bg-slate-900/30">
            <div class="bg-slate-900 border border-slate-800 rounded-2xl p-4 shadow-sm">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 bg-gradient-to-br {{ $user->avatar_color }} from-slate-700 to-slate-800 rounded-full flex items-center justify-center text-white text-sm font-bold shadow-inner ring-2 ring-slate-800">
                        {{ strtoupper(substr($user->display_name, 0, 1)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-slate-200 text-sm font-semibold truncate">{{ $user->display_name }}</p>
                        <p class="text-slate-500 text-xs truncate">Pemimpin Redaksi</p>
                    </div>
                </div>
                <form action="{{ route('redaksi.logout') }}" method="POST">
                    @csrf
                    <button class="w-full flex items-center justify-center gap-2 text-slate-400 hover:text-rose-400 hover:bg-rose-400/10 text-sm font-medium transition-all duration-300 px-3 py-2.5 rounded-xl border border-transparent hover:border-rose-400/20">
                        <i data-lucide="log-out" class="w-4 h-4"></i> Keluar
                    </button>
                </form>
            </div>
        </div>
    </aside>

    {{-- Main Content --}}
    <div class="flex-1 flex flex-col min-w-0 h-screen overflow-hidden bg-slate-50">
        
        {{-- Header --}}
        <header class="bg-white border-b border-slate-200 px-6 py-4 flex items-center justify-between shrink-0 z-10 shadow-sm shadow-slate-200/20">
            <div class="flex items-center gap-4">
                <button class="lg:hidden text-slate-500 hover:text-slate-900 bg-slate-100 p-2 rounded-lg transition-colors" onclick="toggleSidebar()">
                    <i data-lucide="layout-dashboard" class="w-5 h-5"></i>
                </button>
                <div>
                    <h1 class="text-2xl font-black text-slate-900 tracking-tight leading-none mb-1">Dashboard Redaksi</h1>
                    <p class="text-slate-500 text-sm font-medium">Selamat datang kembali, {{ explode(' ', $user->display_name)[0] }}</p>
                </div>
            </div>
            
        </header>

        {{-- Scrollable Main Area --}}
        <main class="flex-1 overflow-y-auto p-4 md:p-8">
            <div class="max-w-7xl mx-auto">
                
                @if(session('success'))
                <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm px-4 py-3 rounded-2xl mb-8 flex items-center gap-3 shadow-sm animate-[slideDown_0.3s_ease-out]">
                    <div class="bg-emerald-100 p-1 rounded-full"><i data-lucide="check" class="w-4 h-4"></i></div>
                    <span class="font-semibold">{{ session('success') }}</span>
                </div>
                @endif

                {{-- KPI Cards --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm hover:shadow-md transition-all duration-300 hover:-translate-y-1 cursor-default group">
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-12 h-12 bg-amber-50 text-amber-500 rounded-2xl flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                                <i data-lucide="clock-3" class="w-6 h-6"></i>
                            </div>
                            <span class="text-xs font-bold text-amber-600 bg-amber-50 px-2.5 py-1 rounded-full">Pending</span>
                        </div>
                        <p class="text-slate-500 text-sm font-medium mb-1">Menunggu Review</p>
                        <h3 class="text-3xl font-black text-slate-900 tracking-tight">{{ $pending->count() }}</h3>
                    </div>
                    
                    <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm hover:shadow-md transition-all duration-300 hover:-translate-y-1 cursor-default group">
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-12 h-12 bg-emerald-50 text-emerald-500 rounded-2xl flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                                <i data-lucide="badge-check" class="w-6 h-6"></i>
                            </div>
                            <span class="text-xs font-bold text-emerald-600 bg-emerald-50 px-2.5 py-1 rounded-full">Siap</span>
                        </div>
                        <p class="text-slate-500 text-sm font-medium mb-1">Siap Diterbitkan</p>
                        <h3 class="text-3xl font-black text-slate-900 tracking-tight">{{ $approved->count() }}</h3>
                    </div>

                    <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm hover:shadow-md transition-all duration-300 hover:-translate-y-1 cursor-default group">
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-12 h-12 bg-blue-50 text-blue-500 rounded-2xl flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                                <i data-lucide="newspaper" class="w-6 h-6"></i>
                            </div>
                            <span class="text-xs font-bold text-blue-600 bg-blue-50 px-2.5 py-1 rounded-full">Total</span>
                        </div>
                        <p class="text-slate-500 text-sm font-medium mb-1">Semua Artikel</p>
                        <h3 class="text-3xl font-black text-slate-900 tracking-tight">{{ $all->count() }}</h3>
                    </div>
                </div>

                {{-- Views Container --}}
                <div class="relative">
                    
                    {{-- Pending View --}}
                    <div id="view-pending" class="space-y-4">
                        <div class="flex items-center justify-between mb-6">
                            <h2 class="text-xl font-bold text-slate-900 tracking-tight">Menunggu Review</h2>
                        </div>
                        
                        @forelse($pending as $art)
                        <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm hover:shadow-xl transition-all duration-300 hover:-translate-y-1 group">
                            <div class="flex flex-col lg:flex-row lg:items-start justify-between gap-6">
                                <div class="flex-1 min-w-0">
                                    <div class="flex flex-wrap items-center gap-3 mb-3">
                                        <span class="inline-flex items-center gap-1.5 text-xs bg-amber-50 border border-amber-200 text-amber-700 px-3 py-1 rounded-full font-bold shadow-sm">
                                            <i data-lucide="clock-3" class="w-3.5 h-3.5"></i> Pending Review
                                        </span>
                                        <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-600 bg-slate-100 px-3 py-1 rounded-full border border-slate-200">
                                            <i data-lucide="folder-open" class="w-3.5 h-3.5"></i> {{ ucfirst($art->category) }}
                                        </span>
                                    </div>
                                    <h3 class="text-lg md:text-xl font-bold text-slate-900 mb-2 group-hover:text-orange-500 transition-colors leading-tight">{{ $art->title }}</h3>
                                    <p class="text-slate-500 text-sm mb-4 line-clamp-2 leading-relaxed">{{ $art->excerpt }}</p>
                                    
                                    <div class="flex flex-wrap items-center gap-4 text-xs font-medium text-slate-500">
                                        <div class="flex items-center gap-1.5 bg-slate-50 px-2.5 py-1.5 rounded-lg border border-slate-100"><i data-lucide="pen-line" class="w-4 h-4 text-slate-400"></i> {{ $art->author_name }}</div>
                                        <div class="flex items-center gap-1.5 bg-slate-50 px-2.5 py-1.5 rounded-lg border border-slate-100"><i data-lucide="calendar-days" class="w-4 h-4 text-slate-400"></i> Disubmit: {{ $art->submitted_at?->format('d M Y, H:i') }}</div>
                                    </div>
                                </div>
                                
                                <div class="flex flex-col gap-2 shrink-0 w-full lg:w-44">
                                    <a href="{{ route('redaksi.articles.edit', $art->id) }}" class="w-full flex items-center justify-center gap-2 text-sm bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold px-4 py-2.5 rounded-xl transition-all duration-300">
                                        <i data-lucide="pencil" class="w-4 h-4"></i> Edit
                                    </a>
                                    <form action="{{ route('redaksi.articles.approve', $art->id) }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="note" value="">
                                        <button type="submit" class="w-full flex items-center justify-center gap-2 text-sm bg-emerald-500 hover:bg-emerald-600 text-white font-semibold px-4 py-2.5 rounded-xl transition-all duration-300 shadow-sm hover:shadow-emerald-500/25 hover:scale-[1.02]">
                                            <i data-lucide="check" class="w-4 h-4"></i> Setujui
                                        </button>
                                    </form>
                                    <form action="{{ route('redaksi.articles.publish', $art->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" onclick="return confirm('Terbitkan langsung tanpa review bertahap?')" class="w-full flex items-center justify-center gap-2 text-sm bg-blue-500 hover:bg-blue-600 text-white font-semibold px-4 py-2.5 rounded-xl transition-all duration-300 shadow-sm hover:shadow-blue-500/25 hover:scale-[1.02]">
                                            <i data-lucide="rocket" class="w-4 h-4"></i> Terbitkan
                                        </button>
                                    </form>
                                    <button onclick="toggleReject('reject-{{ $art->id }}')" class="w-full flex items-center justify-center gap-2 text-sm bg-white hover:bg-rose-50 text-rose-600 font-semibold px-4 py-2.5 rounded-xl border border-rose-200 transition-all duration-300 hover:scale-[1.02]">
                                        <i data-lucide="x" class="w-4 h-4"></i> Tolak
                                    </button>
                                </div>
                            </div>
                            
                            {{-- Reject form inline --}}
                            <div id="reject-{{ $art->id }}" class="hidden mt-5 pt-5 border-t border-slate-100 animate-[slideDown_0.2s_ease-out]">
                                <form action="{{ route('redaksi.articles.reject', $art->id) }}" method="POST" class="flex flex-col md:flex-row gap-3">
                                    @csrf
                                    <div class="relative flex-1">
                                        <i data-lucide="file-text" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400"></i>
                                        <input type="text" name="reason" required placeholder="Tuliskan alasan penolakan..." class="w-full pl-9 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 transition-all">
                                    </div>
                                    <button type="submit" class="flex items-center justify-center gap-2 bg-rose-500 hover:bg-rose-600 text-white text-sm font-semibold px-6 py-2.5 rounded-xl transition-all duration-300 shadow-sm hover:shadow-rose-500/25 shrink-0">
                                        <i data-lucide="trash-2" class="w-4 h-4"></i> Konfirmasi Tolak
                                    </button>
                                </form>
                            </div>
                        </div>
                        @empty
                        <div class="bg-white rounded-2xl border border-slate-200 p-16 text-center shadow-sm flex flex-col items-center justify-center">
                            <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mb-4">
                                <i data-lucide="inbox" class="w-10 h-10 text-slate-400"></i>
                            </div>
                            <h3 class="text-lg font-bold text-slate-900 mb-1">Semua Bersih!</h3>
                            <p class="text-slate-500 text-sm">Tidak ada artikel yang menunggu untuk direview saat ini.</p>
                        </div>
                        @endforelse
                    </div>

                    {{-- Approved View --}}
                    <div id="view-approved" class="hidden space-y-4">
                        <div class="flex items-center justify-between mb-6">
                            <h2 class="text-xl font-bold text-slate-900 tracking-tight">Siap Diterbitkan</h2>
                        </div>

                        @forelse($approved as $art)
                        <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm hover:shadow-md transition-all duration-300 group flex flex-col md:flex-row md:items-center justify-between gap-6">
                            <div class="flex-1">
                                <div class="flex flex-wrap items-center gap-3 mb-2">
                                    <span class="inline-flex items-center gap-1.5 text-xs bg-emerald-50 border border-emerald-200 text-emerald-700 px-3 py-1 rounded-full font-bold shadow-sm">
                                        <i data-lucide="badge-check" class="w-3.5 h-3.5"></i> Disetujui
                                    </span>
                                    <span class="text-xs font-semibold text-slate-500 bg-slate-50 border border-slate-100 px-2.5 py-1 rounded-full">{{ ucfirst($art->category) }}</span>
                                </div>
                                <h3 class="text-lg font-bold text-slate-900 mb-2 group-hover:text-emerald-600 transition-colors">{{ $art->title }}</h3>
                                <p class="text-slate-500 text-sm font-medium flex items-center gap-2">
                                    <i data-lucide="circle-user-round" class="w-4 h-4"></i> {{ $art->author_name }} 
                                    @if($art->reviewed_by)
                                    <span class="text-slate-300 mx-1">•</span> 
                                    <i data-lucide="check" class="w-4 h-4 text-emerald-500"></i> Disetujui oleh: {{ $art->reviewed_by }}
                                    @endif
                                </p>
                            </div>
                            <div class="shrink-0 w-full md:w-auto">
                                <form action="{{ route('redaksi.articles.publish', $art->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" onclick="return confirm('Terbitkan artikel ini ke publik?')" class="w-full md:w-auto flex items-center justify-center gap-2 text-sm bg-blue-500 hover:bg-blue-600 text-white font-bold px-6 py-3 rounded-xl transition-all duration-300 shadow-sm hover:shadow-blue-500/25 hover:-translate-y-0.5">
                                        <i data-lucide="rocket" class="w-4 h-4"></i> Terbitkan Sekarang
                                    </button>
                                </form>
                            </div>
                        </div>
                        @empty
                        <div class="bg-white rounded-2xl border border-slate-200 p-16 text-center shadow-sm flex flex-col items-center justify-center">
                            <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mb-4">
                                <i data-lucide="file-search" class="w-10 h-10 text-slate-400"></i>
                            </div>
                            <h3 class="text-lg font-bold text-slate-900 mb-1">Belum Ada Artikel Siap</h3>
                            <p class="text-slate-500 text-sm">Review artikel di tab "Menunggu Review" untuk memindahkannya ke sini.</p>
                        </div>
                        @endforelse
                    </div>

                    {{-- All Articles View --}}
                    <div id="view-all" class="hidden space-y-4">
                        <div class="flex items-center justify-between mb-6">
                            <h2 class="text-xl font-bold text-slate-900 tracking-tight">Semua Artikel Workflow</h2>
                        </div>

                        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                            @forelse($all as $art)
                            @php 
                                $label = \App\Models\Article::$statusLabel[$art->status] ?? $art->status; 
                                $colorStr = \App\Models\Article::$statusColor[$art->status] ?? ''; 
                                
                                // Mapping existing blade color logic to SaaS design system for ALL view
                                $badgeClass = 'bg-slate-50 border-slate-200 text-slate-600';
                                $icon = 'file-text';
                                if(str_contains($colorStr, 'yellow') || str_contains($colorStr, 'amber')) { $badgeClass = 'bg-amber-50 border-amber-200 text-amber-700'; $icon = 'clock-3'; }
                                if(str_contains($colorStr, 'blue')) { $badgeClass = 'bg-blue-50 border-blue-200 text-blue-700'; $icon = 'rocket'; }
                                if(str_contains($colorStr, 'green') || str_contains($colorStr, 'emerald')) { $badgeClass = 'bg-emerald-50 border-emerald-200 text-emerald-700'; $icon = 'badge-check'; }
                                if(str_contains($colorStr, 'red') || str_contains($colorStr, 'rose')) { $badgeClass = 'bg-rose-50 border-rose-200 text-rose-700'; $icon = 'x'; }
                            @endphp
                            
                            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 px-6 py-4 border-b border-slate-100 last:border-0 hover:bg-slate-50/80 transition-colors group">
                                <div class="flex-1 min-w-0">
                                    <p class="text-slate-900 font-bold text-sm mb-1 truncate group-hover:text-orange-500 transition-colors">{{ $art->title }}</p>
                                    <div class="flex items-center gap-3 text-xs font-medium text-slate-500">
                                        <span class="flex items-center gap-1"><i data-lucide="pen-line" class="w-3.5 h-3.5"></i> {{ $art->author_name }}</span>
                                        <span class="text-slate-300">•</span>
                                        <span>{{ ucfirst($art->category) }}</span>
                                        <span class="text-slate-300">•</span>
                                        <span>{{ $art->created_at->format('d M Y') }}</span>
                                    </div>
                                </div>
                                <div class="flex items-center gap-4 shrink-0">
                                    <a href="{{ route('redaksi.articles.edit', $art->id) }}" class="flex items-center gap-1 text-xs text-slate-600 hover:text-slate-800 bg-slate-100 hover:bg-slate-200 px-3 py-1.5 rounded-lg font-semibold transition-colors">
                                        <i data-lucide="pencil" class="w-3.5 h-3.5"></i> Edit
                                    </a>
                                    <span class="inline-flex items-center gap-1.5 text-xs px-3 py-1 rounded-full border font-bold shadow-sm {{ $badgeClass }}">
                                        <i data-lucide="{{ $icon }}" class="w-3.5 h-3.5"></i> {{ $label }}
                                    </span>
                                    
                                    @if($art->status === 'published')
                                    <div class="flex items-center gap-2 border-l border-slate-200 pl-4">
                                        <a href="{{ route('article.show', $art->id) }}" target="_blank" class="flex items-center gap-1 text-xs text-blue-600 hover:text-blue-700 bg-blue-50 hover:bg-blue-100 px-3 py-1.5 rounded-lg font-semibold transition-colors">
                                            <i data-lucide="eye" class="w-3.5 h-3.5"></i> Lihat
                                        </a>
                                        <form action="{{ route('redaksi.articles.unpublish', $art->id) }}" method="POST">
                                            @csrf
                                            <button onclick="return confirm('Tarik artikel ini dari tampilan publik?')" class="flex items-center gap-1 text-xs text-slate-500 hover:text-rose-600 bg-slate-50 hover:bg-rose-50 px-3 py-1.5 rounded-lg font-semibold transition-colors border border-transparent hover:border-rose-200">
                                                <i data-lucide="undo-2" class="w-3.5 h-3.5"></i> Tarik Artikel
                                            </button>
                                        </form>
                                        <form action="{{ route('redaksi.articles.destroy', $art->id) }}" method="POST" onsubmit="return confirm('Hapus artikel ini secara permanen dari sistem?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="flex items-center gap-1 text-xs text-rose-600 hover:text-rose-700 bg-rose-50 hover:bg-rose-100 px-3 py-1.5 rounded-lg font-semibold transition-colors border border-rose-200">
                                                <i data-lucide="trash-2" class="w-3.5 h-3.5"></i> Hapus
                                            </button>
                                        </form>
                                    </div>
                                    @endif
                                </div>
                            </div>
                            @empty
                            <div class="p-16 text-center flex flex-col items-center justify-center">
                                <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mb-3">
                                    <i data-lucide="folder-open" class="w-8 h-8 text-slate-300"></i>
                                </div>
                                <p class="text-slate-500 text-sm font-medium">Belum ada riwayat artikel.</p>
                            </div>
                            @endforelse
                        </div>
                    </div>

                </div>
            </div>
        </main>
    </div>

<style>
    @keyframes slideDown {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>

<script>
    // Initialize Icons
    lucide.createIcons();

    function setView(view) {
        // Toggle view containers
        ['pending','approved','all'].forEach(v => {
            document.getElementById('view-'+v).classList.toggle('hidden', v !== view);
        });

        // Toggle active states on sidebar buttons
        document.querySelectorAll('.view-btn').forEach(b => {
            const active = b.dataset.view === view;
            
            if(active) {
                b.className = 'view-btn group w-full flex items-center justify-between px-4 py-3 rounded-xl text-sm transition-all duration-300 ease-out bg-gradient-to-r from-orange-500 to-orange-600 text-white shadow-md shadow-orange-500/20 font-bold';
                // Find all icon children and modify if needed (already handled by Lucide via CSS classes generally)
            } else {
                b.className = 'view-btn group w-full flex items-center justify-between px-4 py-3 rounded-xl text-sm transition-all duration-300 ease-out text-slate-400 hover:text-white hover:bg-slate-800/50 font-medium';
            }
        });
    }

    function toggleReject(id) { 
        const el = document.getElementById(id);
        el.classList.toggle('hidden'); 
    }

    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('mobile-overlay');
        
        if(sidebar.classList.contains('-translate-x-full')) {
            sidebar.classList.remove('-translate-x-full');
            overlay.classList.remove('hidden');
        } else {
            sidebar.classList.add('-translate-x-full');
            overlay.classList.add('hidden');
        }
    }

    // Init default view
    setView('pending');
</script>
</body>
</html>
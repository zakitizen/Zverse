<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Dashboard Pewarta — Zverse</title>
    
    {{-- Favicon untuk Tab Browser --}}
    @php $zvLogo = 'logozverse.png'; @endphp
    <link rel="icon" type="image/png" href="{{ asset($zvLogo) }}?v={{ filemtime(public_path($zvLogo)) }}">
    <link rel="apple-touch-icon" href="{{ asset($zvLogo) }}?v={{ filemtime(public_path($zvLogo)) }}">
    
    {{-- Fonts & Icons --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    {{-- Zverse Design System --}}
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'sans-serif'] },
                    colors: {
                        primary: { 500: '#0ea5e9', 600: '#0284c7' },   // Sky Blue
                        secondary: { 500: '#8b5cf6', 600: '#7c3aed' }, // Purple
                    }
                }
            }
        }
    </script>
    <style>
        body { font-family: 'Inter', sans-serif; -webkit-tap-highlight-color: transparent; }
        .line-clamp-2 { display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
        @keyframes slideDown { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }
        .touch-safe { min-height: 44px; }
        #sidebar { overscroll-behavior: contain; }
    </style>
</head>
<body class="bg-slate-50 min-h-screen flex text-slate-600 selection:bg-sky-500 selection:text-white overflow-hidden">

{{-- Mobile Sidebar Overlay --}}
<div id="mobile-overlay" class="fixed inset-0 bg-slate-900/50 z-40 hidden lg:hidden backdrop-blur-sm transition-all duration-300" onclick="toggleSidebar()"></div>

{{-- Sidebar --}}
<aside id="sidebar" class="fixed lg:static inset-y-0 left-0 z-50 w-72 bg-slate-950 border-r border-slate-800 flex flex-col shrink-0 transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-out shadow-2xl lg:shadow-none">
    <div class="p-6 border-b border-slate-800/60 flex items-center justify-between">
        <a href="{{ route('home') }}" class="flex shrink-0 items-center gap-3 group">
            {{-- Logo Zverse --}}
            @php $zvLogo = 'logozverse.png'; @endphp
            <img src="{{ asset($zvLogo) }}?v={{ filemtime(public_path($zvLogo)) }}" alt="Zverse" class="h-8 w-8 sm:h-10 sm:w-10 shrink-0 object-contain transition-transform duration-300 group-hover:scale-105" />
            <div class="leading-none">
                <p class="text-lg font-black tracking-tight text-white">Zverse</p>
                <p class="text-[10px] font-semibold uppercase tracking-[0.25em] text-slate-500">Media & Tech</p>
            </div>
        </a>
        <button class="lg:hidden text-slate-400 hover:text-white transition-colors" onclick="toggleSidebar()">
            <i data-lucide="x" class="w-5 h-5"></i>
        </button>
    </div>
    
    <nav class="flex-1 p-4 space-y-2 overflow-y-auto">
        <p class="text-xs font-semibold text-slate-500 mb-3 px-3 uppercase tracking-wider">Menu Utama</p>
        
        <a href="{{ route('pewarta.dashboard') }}" class="group w-full flex items-center gap-3 px-4 py-3 rounded-xl text-sm transition-all duration-300 ease-out bg-gradient-to-r from-sky-500 to-sky-600 text-white shadow-md shadow-sky-500/20 font-bold">
            <i data-lucide="layout-dashboard" class="w-5 h-5 group-hover:scale-110 transition-transform"></i>
            Dashboard
        </a>
        <a href="{{ route('pewarta.articles.create') }}" class="group w-full flex items-center gap-3 px-4 py-3 rounded-xl text-sm transition-all duration-300 ease-out text-slate-400 hover:text-white hover:bg-slate-800/50 font-medium border border-transparent hover:border-slate-700">
            <i data-lucide="pen-line" class="w-5 h-5 group-hover:scale-110 transition-transform"></i>
            Tulis Artikel
        </a>
    </nav>

    <div class="p-4 border-t border-slate-800/60 bg-slate-900/30">
        <div class="bg-slate-900 border border-slate-800 rounded-2xl p-4 shadow-sm">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 bg-gradient-to-br {{ $user->avatar_color }} from-slate-700 to-slate-800 rounded-full flex items-center justify-center text-white text-sm font-bold shadow-inner ring-2 ring-slate-800">
                    {{ strtoupper(substr($user->display_name, 0, 1)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-slate-200 text-sm font-semibold truncate">{{ $user->display_name }}</p>
                    <p class="text-slate-500 text-xs truncate">Pewarta Zverse</p>
                </div>
            </div>
            <form action="{{ route('pewarta.logout') }}" method="POST">
                @csrf
                <button class="w-full flex items-center justify-center gap-2 text-slate-400 hover:text-rose-400 hover:bg-rose-400/10 text-sm font-medium transition-all duration-300 px-3 py-2.5 rounded-xl border border-transparent hover:border-rose-400/20">
                    <i data-lucide="log-out" class="w-4 h-4"></i> Keluar
                </button>
            </form>
        </div>
    </div>
</aside>

{{-- Main Content --}}
<div class="flex-1 flex flex-col h-screen overflow-hidden bg-slate-50">
    
    {{-- Header --}}
    <header class="bg-white border-b border-slate-200 px-6 py-4 flex items-center justify-between shrink-0 z-10 shadow-sm shadow-slate-200/20">
        <div class="flex items-center gap-4">
            <button class="lg:hidden text-slate-500 hover:text-slate-900 bg-slate-100 p-2 rounded-lg transition-colors" onclick="toggleSidebar()">
                <i data-lucide="menu" class="w-5 h-5"></i>
            </button>
            <div>
                <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight leading-none mb-1">Dashboard Pewarta</h1>
                <p class="text-xs sm:text-sm text-slate-500 font-medium">Selamat datang kembali, {{ explode(' ', $user->display_name)[0] }}</p>
            </div>
        </div>
        
        <a href="{{ route('pewarta.articles.create') }}" class="hidden sm:flex items-center gap-2 bg-sky-500 hover:bg-sky-600 text-white text-sm font-bold px-5 py-2.5 rounded-xl transition-all duration-300 shadow-sm hover:shadow-sky-500/25 hover:-translate-y-0.5">
            <i data-lucide="plus" class="w-4 h-4"></i> Tulis Artikel
        </a>
    </header>

    {{-- Scrollable Main Area --}}
    <main class="flex-1 overflow-y-auto p-4 md:p-8">
        <div class="max-w-6xl mx-auto">
            
            {{-- Flash Messages --}}
            @if(session('success'))
            <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm px-4 py-3.5 rounded-2xl mb-8 flex items-center gap-3 shadow-sm animate-[slideDown_0.3s_ease-out]">
                <div class="bg-emerald-100 p-1.5 rounded-full"><i data-lucide="check-circle-2" class="w-4 h-4"></i></div>
                <span class="font-semibold">{{ session('success') }}</span>
            </div>
            @endif
            @if(session('error'))
            <div class="bg-rose-50 border border-rose-200 text-rose-700 text-sm px-4 py-3.5 rounded-2xl mb-8 flex items-center gap-3 shadow-sm animate-[slideDown_0.3s_ease-out]">
                <div class="bg-rose-100 p-1.5 rounded-full"><i data-lucide="alert-circle" class="w-4 h-4"></i></div>
                <span class="font-semibold">{{ session('error') }}</span>
            </div>
            @endif

            {{-- KPI Stats --}}
            @php
                $statuses = ['draft','pending','approved','rejected','published'];
                $statusIcons = [
                    'draft' => 'pen-tool', 'pending' => 'clock-3', 'approved' => 'badge-check', 
                    'rejected' => 'x-circle', 'published' => 'rocket'
                ];
                $statusColors = [
                    'draft' => 'text-slate-500 bg-slate-50', 'pending' => 'text-amber-500 bg-amber-50', 
                    'approved' => 'text-emerald-500 bg-emerald-50', 'rejected' => 'text-rose-500 bg-rose-50', 
                    'published' => 'text-sky-500 bg-sky-50'
                ];
            @endphp
            <div class="grid grid-cols-2 sm:grid-cols-5 gap-2 sm:gap-4 mb-8">
                @foreach($statuses as $s)
                @php $count = $articles->where('status',$s)->count(); @endphp
                <div class="bg-white rounded-2xl border border-slate-200 p-3 sm:p-5 shadow-sm hover:shadow-md transition-all duration-300 hover:-translate-y-1 group">
                    <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-xl flex items-center justify-center mb-2 sm:mb-3 {{ $statusColors[$s] }} group-hover:scale-110 transition-transform">
                        <i data-lucide="{{ $statusIcons[$s] }}" class="w-4 h-4 sm:w-5 sm:h-5"></i>
                    </div>
                    <p class="text-xl sm:text-3xl font-black text-slate-900 tracking-tight leading-none mb-1">{{ $count }}</p>
                    <p class="text-[10px] sm:text-xs text-slate-500 font-semibold uppercase tracking-wider">{{ \App\Models\Article::$statusLabel[$s] ?? $s }}</p>
                </div>
                @endforeach
            </div>

            {{-- Articles Table --}}
            <div class="bg-white rounded-[1.5rem] border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                    <h2 class="font-bold text-slate-900 flex items-center gap-2">
                        <i data-lucide="file-text" class="w-5 h-5 text-sky-500"></i> Artikel Saya
                        <span class="bg-slate-200 text-slate-600 text-xs px-2 py-0.5 rounded-full">{{ $articles->count() }}</span>
                    </h2>
                    <a href="{{ route('pewarta.articles.create') }}" class="sm:hidden text-sky-600 hover:text-sky-700 font-bold text-sm flex items-center gap-1">
                        <i data-lucide="plus" class="w-4 h-4"></i> Tulis
                    </a>
                </div>
                
                <div class="divide-y divide-slate-100">
                    @forelse($articles as $article)
                    @php
                        $label = \App\Models\Article::$statusLabel[$article->status] ?? $article->status;
                        $colorStr = \App\Models\Article::$statusColor[$article->status] ?? '';
                        
                        // Adaptasi Badge Color System
                        $badgeClass = 'bg-slate-50 border-slate-200 text-slate-600';
                        if(str_contains($colorStr, 'yellow') || str_contains($colorStr, 'amber')) $badgeClass = 'bg-amber-50 border-amber-200 text-amber-700';
                        if(str_contains($colorStr, 'blue') || str_contains($colorStr, 'sky')) $badgeClass = 'bg-sky-50 border-sky-200 text-sky-700';
                        if(str_contains($colorStr, 'green') || str_contains($colorStr, 'emerald')) $badgeClass = 'bg-emerald-50 border-emerald-200 text-emerald-700';
                        if(str_contains($colorStr, 'red') || str_contains($colorStr, 'rose')) $badgeClass = 'bg-rose-50 border-rose-200 text-rose-700';
                    @endphp
                    
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 px-6 py-5 hover:bg-slate-50/80 transition-colors group">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 mb-2 flex-wrap">
                                <span class="inline-flex items-center gap-1 text-[10px] uppercase tracking-wider px-2.5 py-1 rounded-full border font-bold shadow-sm {{ $badgeClass }}">
                                    <i data-lucide="{{ $statusIcons[$article->status] ?? 'circle' }}" class="w-3 h-3"></i> {{ $label }}
                                </span>
                                @if($article->review_note && in_array($article->status, ['rejected','approved']))
                                <span class="flex items-center gap-1 text-xs text-rose-500 bg-rose-50 px-2.5 py-1 rounded-lg border border-rose-100 font-medium">
                                    <i data-lucide="message-square-dashed" class="w-3.5 h-3.5"></i> Catatan Editor
                                </span>
                                @endif
                            </div>
                            
                            <p class="text-slate-900 font-bold text-base leading-snug line-clamp-2 mb-1.5 group-hover:text-sky-600 transition-colors">{{ $article->title }}</p>
                            
                            @if($article->review_note && in_array($article->status, ['rejected','approved']))
                            <p class="text-sm text-slate-500 italic border-l-2 border-rose-200 pl-3 mb-2 bg-slate-50 py-1 pr-2 rounded-r-lg line-clamp-2">"{{ $article->review_note }}"</p>
                            @endif

                            <div class="flex items-center gap-3 text-slate-400 text-xs font-semibold">
                                <span class="flex items-center gap-1"><i data-lucide="layout-grid" class="w-3.5 h-3.5"></i> {{ ucfirst($article->category) }}</span>
                                <span class="text-slate-300">•</span>
                                <span class="flex items-center gap-1"><i data-lucide="clock" class="w-3.5 h-3.5"></i> {{ $article->created_at->diffForHumans() }}</span>
                                @if($article->submitted_at) 
                                <span class="text-slate-300">•</span>
                                <span class="flex items-center gap-1"><i data-lucide="calendar-check" class="w-3.5 h-3.5"></i> Submit: {{ $article->submitted_at->format('d M Y') }}</span> 
                                @endif
                            </div>
                        </div>
                        
                        <div class="flex flex-wrap items-center gap-2 shrink-0 border-t md:border-t-0 pt-3 md:pt-0 mt-2 md:mt-0 border-slate-100">
                            @if(in_array($article->status, ['draft','rejected']))
                                <a href="{{ route('pewarta.articles.edit', $article->id) }}" class="touch-safe flex items-center gap-1.5 text-xs bg-white hover:bg-sky-50 text-slate-600 hover:text-sky-600 border border-slate-200 hover:border-sky-200 px-3 py-2 rounded-xl transition-all shadow-sm font-bold">
                                    <i data-lucide="edit-3" class="w-3.5 h-3.5"></i> Edit
                                </a>
                            @endif
                            
                            @if($article->status === 'draft')
                                <form action="{{ route('pewarta.articles.submit', $article->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" onclick="return confirm('Kirim artikel ini ke redaksi untuk direview?')" class="touch-safe flex items-center gap-1.5 text-xs bg-sky-500 hover:bg-sky-600 text-white px-4 py-2 rounded-xl transition-all shadow-sm shadow-sky-500/20 font-bold">
                                        <i data-lucide="send" class="w-3.5 h-3.5"></i> Submit
                                    </button>
                                </form>
                            @endif
                            
                            @if(in_array($article->status, ['draft','rejected']))
                                <form action="{{ route('pewarta.articles.destroy', $article->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus artikel ini secara permanen?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="touch-safe flex items-center gap-1.5 text-xs bg-white hover:bg-rose-50 text-slate-600 hover:text-rose-600 border border-slate-200 hover:border-rose-200 px-3 py-2 rounded-xl transition-all shadow-sm font-bold">
                                        <i data-lucide="trash-2" class="w-3.5 h-3.5"></i> Hapus
                                    </button>
                                </form>
                            @endif
                            
                            @if($article->status === 'published')
                                <a href="{{ route('article.show', $article->slug) }}" target="_blank" class="touch-safe flex items-center gap-1.5 text-xs bg-emerald-50 text-emerald-700 border border-emerald-200 px-4 py-2 rounded-xl font-bold hover:bg-emerald-100 transition-colors">
                                    <i data-lucide="external-link" class="w-3.5 h-3.5"></i> Lihat Web
                                </a>
                            @endif
                        </div>
                    </div>
                    @empty
                    <div class="px-4 sm:px-6 py-10 md:py-20 text-center flex flex-col items-center justify-center">
                        <div class="w-16 h-16 md:w-20 md:h-20 bg-slate-50 rounded-full flex items-center justify-center mb-4">
                            <i data-lucide="inbox" class="w-8 h-8 md:w-10 md:h-10 text-slate-300"></i>
                        </div>
                        <h3 class="text-slate-900 font-bold text-base md:text-lg mb-1">Ruang Kerja Masih Kosong</h3>
                        <p class="text-slate-500 text-xs md:text-sm mb-6">Mulai tulis artikel pertamamu dan publikasikan idemu.</p>
                        <a href="{{ route('pewarta.articles.create') }}" class="touch-safe inline-flex items-center gap-2 bg-sky-500 hover:bg-sky-600 text-white text-sm font-bold px-6 py-2.5 rounded-xl transition-all shadow-sm shadow-sky-500/25">
                            <i data-lucide="pen-tool" class="w-4 h-4"></i> Tulis Artikel Baru
                        </a>
                    </div>
                    @endforelse
                </div>
            </div>

        </div>
    </main>
</div>

<script>
    // Inisialisasi ikon Lucide
    lucide.createIcons();

    // Fungsi Toggle Sidebar Mobile
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
</script>
</body>
</html>
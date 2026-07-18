<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    {{-- Rebranding to Zverse --}}
    <title>@yield('title', 'Zverse — Portal Entertainment & Tech Modern')</title>
    
    {{-- Fonts & Icons --}}
    <!-- Tambahkan ini di bawah tag <title> -->
    @php $zvLogo = 'logozverse.png'; @endphp
    <link rel="icon" type="image/png" href="{{ asset($zvLogo) }}?v={{ filemtime(public_path($zvLogo)) }}">
    <link rel="apple-touch-icon" href="{{ asset($zvLogo) }}?v={{ filemtime(public_path($zvLogo)) }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://cdn.tailwindcss.com"></script>

    {{-- Zverse Design System Configuration --}}
    <script>
        tailwind.config = {
            darkMode: 'class', // Support untuk dark mode
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    },
                    colors: {
                        primary: { 500: '#0ea5e9', 600: '#0284c7' },   // Sky Blue (Biru Laut)
                        secondary: { 500: '#8b5cf6', 600: '#7c3aed' }, // Purple
                        accent: { 500: '#06b6d4', 600: '#0891b2' },    // Cyan
                        success: { 500: '#10b981', 600: '#059669' },
                        danger: { 500: '#f43f5e', 600: '#e11d48' },
                        warning: { 500: '#f59e0b', 600: '#d97706' },
                    }
                }
            }
        }
    </script>

    {{-- Global Premium Styling --}}
    <style>
        body { font-family: 'Inter', sans-serif; }
        .line-clamp-2 { display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
        .line-clamp-3 { display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; }
        
        /* Modern Scrollbar */
        ::-webkit-scrollbar { width: 8px; height: 8px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
        .dark ::-webkit-scrollbar-thumb { background: #334155; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
        
        /* Smooth Loading Fade */
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
        .animate-fade-in { animation: fadeIn 0.5s ease-out forwards; }
    </style>
    @stack('styles')
</head>
<body class="bg-slate-50 dark:bg-slate-950 text-slate-600 dark:text-slate-400 min-h-screen flex flex-col selection:bg-sky-500 selection:text-white transition-colors duration-300">

    {{-- Navbar Component --}}
    @include('components.navbar')

    {{-- Modern Flash Messages (SaaS Vibe) --}}
    @if(session('success'))
        <div class="fixed top-24 left-1/2 -translate-x-1/2 z-50 animate-fade-in">
            <div class="flex items-center gap-3 bg-white dark:bg-slate-900 border border-emerald-200 dark:border-emerald-500/30 px-5 py-3 rounded-2xl shadow-xl shadow-emerald-500/10">
                <div class="w-8 h-8 rounded-full bg-emerald-100 dark:bg-emerald-500/20 flex items-center justify-center shrink-0">
                    <i data-lucide="check" class="w-4 h-4 text-emerald-600 dark:text-emerald-400"></i>
                </div>
                <span class="text-slate-800 dark:text-slate-200 text-sm font-bold">{{ session('success') }}</span>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="fixed top-24 left-1/2 -translate-x-1/2 z-50 animate-fade-in">
            <div class="flex items-center gap-3 bg-white dark:bg-slate-900 border border-rose-200 dark:border-rose-500/30 px-5 py-3 rounded-2xl shadow-xl shadow-rose-500/10">
                <div class="w-8 h-8 rounded-full bg-rose-100 dark:bg-rose-500/20 flex items-center justify-center shrink-0">
                    <i data-lucide="alert-circle" class="w-4 h-4 text-rose-600 dark:text-rose-400"></i>
                </div>
                <span class="text-slate-800 dark:text-slate-200 text-sm font-bold">{{ session('error') }}</span>
            </div>
        </div>
    @endif

    {{-- Main Content Injection --}}
    <main class="flex-1 w-full animate-fade-in relative z-0">
        @yield('content')
    </main>

    {{-- Footer Component --}}
    @include('components.footer')

    {{-- Global Scripts --}}
    <script>
        // Initialize Lucide Icons globally for all views
        document.addEventListener('DOMContentLoaded', () => {
            lucide.createIcons();
        });

        // Auto-hide flash messages after 5 seconds
        setTimeout(() => {
            const alerts = document.querySelectorAll('.fixed.top-24');
            alerts.forEach(alert => {
                alert.style.opacity = '0';
                alert.style.transition = 'opacity 0.5s ease';
                setTimeout(() => alert.remove(), 500);
            });
        }, 5000);
    </script>
    @stack('scripts')
</body>
</html>
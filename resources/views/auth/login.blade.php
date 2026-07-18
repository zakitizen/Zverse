@extends('layouts.app')
@section('title', 'Masuk — Zverse')

@section('content')
<div class="min-h-[85vh] flex items-center justify-center px-4 py-12 relative overflow-hidden">
    
    {{-- Subtle Background Glow (Futuristic Touch) --}}
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] bg-sky-500/10 dark:bg-sky-500/5 blur-[100px] rounded-full pointer-events-none"></div>

    <div class="w-full max-w-md relative z-10">

        {{-- Logo & Header --}}
        <div class="text-center mb-10">
            <a href="{{ route('home') }}" class="inline-flex items-center gap-3 mb-5 group">
                {{-- Logo Zverse Baru --}}
                @php $zvLogo = 'logozverse.png'; @endphp
                <img src="{{ asset($zvLogo) }}?v={{ filemtime(public_path($zvLogo)) }}" alt="Zverse" class="h-12 w-12 shrink-0 object-contain transition-transform duration-300 group-hover:scale-105" />
                <div class="leading-none text-left">
                    <span class="block text-slate-900 dark:text-white text-3xl font-black tracking-tight">Zverse</span>
                    <span class="text-[10px] font-bold uppercase tracking-[0.25em] text-slate-500 dark:text-slate-400">Media & Tech</span>
                </div>
            </a>
            <p class="text-slate-500 dark:text-slate-400 text-sm font-medium">Portal hiburan modern dan berita terkini</p>
        </div>

        {{-- Auth Card --}}
        <div class="bg-white dark:bg-slate-900 rounded-[2rem] border border-slate-200 dark:border-slate-800 shadow-2xl shadow-sky-500/5 overflow-hidden">

            {{-- Tabs Login / Daftar --}}
            <div class="flex border-b border-slate-200 dark:border-slate-800">
                <button id="tab-login" onclick="switchTab('login')" class="flex-1 py-4 text-sm font-bold text-sky-600 dark:text-sky-400 border-b-2 border-sky-500 bg-sky-50/50 dark:bg-sky-500/10 transition-all">
                    Masuk
                </button>
                <button id="tab-register" onclick="switchTab('register')" class="flex-1 py-4 text-sm font-semibold text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 border-b-2 border-transparent transition-all">
                    Daftar
                </button>
            </div>

            {{-- Form Login --}}
            <div id="form-login" class="p-6 sm:p-10">
                @if($errors->any() && old('_form') !== 'register')
                <div class="bg-rose-50 dark:bg-rose-500/10 border border-rose-200 dark:border-rose-500/20 text-rose-700 dark:text-rose-400 text-sm px-4 py-3 rounded-xl mb-6 flex items-start gap-2">
                    <i data-lucide="alert-circle" class="w-4 h-4 mt-0.5 shrink-0"></i>
                    <span class="font-medium">{{ $errors->first() }}</span>
                </div>
                @endif

                <form action="{{ route('login.post') }}" method="POST" class="space-y-5">
                    @csrf
                    <input type="hidden" name="_form" value="login">
                    <div>
                        <label class="block text-slate-700 dark:text-slate-300 text-sm font-bold mb-2">Username</label>
                        <input type="text" name="username" value="{{ old('username') }}" required
                               class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl px-4 py-3.5 text-sm text-slate-900 dark:text-white focus:outline-none focus:ring-4 focus:ring-sky-500/10 focus:border-sky-500 transition-all"
                               placeholder="Masukkan username kamu">
                    </div>
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <label class="block text-slate-700 dark:text-slate-300 text-sm font-bold">Password</label>
                            <a href="#" class="text-xs font-semibold text-sky-500 hover:text-sky-600 dark:hover:text-sky-400 transition-colors">Lupa Password?</a>
                        </div>
                        <input type="password" name="password" required
                               class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl px-4 py-3.5 text-sm text-slate-900 dark:text-white focus:outline-none focus:ring-4 focus:ring-sky-500/10 focus:border-sky-500 transition-all"
                               placeholder="••••••••">
                    </div>
                    <div class="flex items-center gap-2 pt-1">
                        <input type="checkbox" id="remember" name="remember" class="w-4 h-4 rounded border-slate-300 text-sky-500 focus:ring-sky-500 bg-white dark:bg-slate-900 cursor-pointer">
                        <label for="remember" class="text-sm font-medium text-slate-600 dark:text-slate-400 cursor-pointer select-none">Ingat saya</label>
                    </div>
                    <button type="submit" class="w-full bg-sky-500 hover:bg-sky-600 text-white font-bold py-3.5 rounded-xl transition-all shadow-sm shadow-sky-500/25 hover:-translate-y-0.5 mt-2 flex items-center justify-center gap-2">
                        Masuk ke Zverse <i data-lucide="arrow-right" class="w-4 h-4"></i>
                    </button>
                </form>

                <p class="text-center text-slate-500 dark:text-slate-400 text-sm mt-8 font-medium">
                    Belum punya akun? <button onclick="switchTab('register')" class="text-sky-500 font-bold hover:text-sky-600 dark:hover:text-sky-400 transition-colors">Daftar sekarang</button>
                </p>
            </div>

            {{-- Form Register --}}
            <div id="form-register" class="p-6 sm:p-10 hidden">
                @if($errors->any() && old('_form') === 'register')
                <div class="bg-rose-50 dark:bg-rose-500/10 border border-rose-200 dark:border-rose-500/20 text-rose-700 dark:text-rose-400 text-sm px-4 py-3 rounded-xl mb-6 flex items-start gap-2">
                    <i data-lucide="alert-circle" class="w-4 h-4 mt-0.5 shrink-0"></i>
                    <span class="font-medium">{{ $errors->first() }}</span>
                </div>
                @endif

                <form action="{{ route('register.post') }}" method="POST" class="space-y-4">
                    @csrf
                    <input type="hidden" name="_form" value="register">
                    <div>
                        <label class="block text-slate-700 dark:text-slate-300 text-sm font-bold mb-2">Nama Tampilan</label>
                        <input type="text" name="display_name" value="{{ old('display_name') }}" required
                               class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl px-4 py-3 text-sm text-slate-900 dark:text-white focus:outline-none focus:ring-4 focus:ring-sky-500/10 focus:border-sky-500 transition-all"
                               placeholder="Nama lengkap kamu">
                    </div>
                    <div>
                        <label class="block text-slate-700 dark:text-slate-300 text-sm font-bold mb-2">Username</label>
                        <input type="text" name="username" value="{{ old('username') }}" required
                               class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl px-4 py-3 text-sm text-slate-900 dark:text-white focus:outline-none focus:ring-4 focus:ring-sky-500/10 focus:border-sky-500 transition-all"
                               placeholder="min. 3 karakter">
                    </div>
                    <div>
                        <label class="block text-slate-700 dark:text-slate-300 text-sm font-bold mb-2">Password</label>
                        <input type="password" name="password" required
                               class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl px-4 py-3 text-sm text-slate-900 dark:text-white focus:outline-none focus:ring-4 focus:ring-sky-500/10 focus:border-sky-500 transition-all"
                               placeholder="min. 6 karakter">
                    </div>
                    <div>
                        <label class="block text-slate-700 dark:text-slate-300 text-sm font-bold mb-2">Konfirmasi Password</label>
                        <input type="password" name="password_confirmation" required
                               class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl px-4 py-3 text-sm text-slate-900 dark:text-white focus:outline-none focus:ring-4 focus:ring-sky-500/10 focus:border-sky-500 transition-all"
                               placeholder="Ulangi password">
                    </div>
                    <button type="submit" class="w-full bg-slate-900 dark:bg-white text-white dark:text-slate-900 hover:bg-sky-500 dark:hover:bg-sky-500 dark:hover:text-white font-bold py-3.5 rounded-xl transition-all shadow-sm mt-4">
                        Buat Akun Zverse
                    </button>
                </form>

                <p class="text-center text-slate-500 dark:text-slate-400 text-sm mt-6 font-medium">
                    Sudah punya akun? <button onclick="switchTab('login')" class="text-sky-500 font-bold hover:text-sky-600 dark:hover:text-sky-400 transition-colors">Masuk</button>
                </p>
            </div>

        </div>
    </div>
</div>

@push('scripts')
<script>
    // Initialize Lucide Icons if used
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }

    function switchTab(tab) {
        const isLogin = tab === 'login';
        
        // Toggle Form Visibility
        document.getElementById('form-login').classList.toggle('hidden', !isLogin);
        document.getElementById('form-register').classList.toggle('hidden', isLogin);
        
        // Define Active and Inactive classes for tabs
        const activeClass = 'flex-1 py-4 text-sm font-bold text-sky-600 dark:text-sky-400 border-b-2 border-sky-500 bg-sky-50/50 dark:bg-sky-500/10 transition-all';
        const inactiveClass = 'flex-1 py-4 text-sm font-semibold text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 border-b-2 border-transparent transition-all';
        
        // Apply Classes
        document.getElementById('tab-login').className = isLogin ? activeClass : inactiveClass;
        document.getElementById('tab-register').className = !isLogin ? activeClass : inactiveClass;
    }

    // Auto-switch to register tab if there was a validation error on register form
    @if(old('_form') === 'register' && $errors->any())
        switchTab('register');
    @endif
</script>
@endpush
@endsection
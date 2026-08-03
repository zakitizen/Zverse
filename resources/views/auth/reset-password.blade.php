@extends('layouts.app')
@section('title', 'Reset Password — Zverse')

@section('content')
<div class="min-h-[85vh] flex items-center justify-center px-4 py-12 relative overflow-hidden">
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] bg-sky-500/10 dark:bg-sky-500/5 blur-[100px] rounded-full pointer-events-none"></div>

    <div class="w-full max-w-md relative z-10">
        <div class="text-center mb-10">
            <a href="{{ route('home') }}" class="inline-flex items-center gap-3 mb-5 group">
                @php $zvLogo = 'logozverse.png'; @endphp
                <img src="{{ asset($zvLogo) }}?v={{ filemtime(public_path($zvLogo)) }}" alt="Zverse" class="h-12 w-12 shrink-0 object-contain transition-transform duration-300 group-hover:scale-105" />
                <div class="leading-none text-left">
                    <span class="block text-slate-900 dark:text-white text-3xl font-black tracking-tight">Zverse</span>
                    <span class="text-[10px] font-bold uppercase tracking-[0.25em] text-slate-500 dark:text-slate-400">Media & Tech</span>
                </div>
            </a>
            <p class="text-slate-500 dark:text-slate-400 text-sm font-medium">Buat password baru</p>
        </div>

        <div class="bg-white dark:bg-slate-900 rounded-[2rem] border border-slate-200 dark:border-slate-800 shadow-2xl shadow-sky-500/5 overflow-hidden p-6 sm:p-10">
            @if($errors->any())
            <div class="bg-rose-50 dark:bg-rose-500/10 border border-rose-200 dark:border-rose-500/20 text-rose-700 dark:text-rose-400 text-sm px-4 py-3 rounded-xl mb-6 flex items-start gap-2">
                <i data-lucide="alert-circle" class="w-4 h-4 mt-0.5 shrink-0"></i>
                <span class="font-medium">{{ $errors->first() }}</span>
            </div>
            @endif

            <form action="{{ route('password.update') }}" method="POST" class="space-y-5">
                @csrf
                <div class="bg-sky-50 dark:bg-sky-500/10 border border-sky-200 dark:border-sky-500/20 text-sky-700 dark:text-sky-400 text-sm px-4 py-3 rounded-xl flex items-center gap-2">
                    <i data-lucide="user-check" class="w-4 h-4 shrink-0"></i>
                    <span class="font-medium">Reset password untuk <b>{{ $username }}</b></span>
                </div>
                <div>
                    <label class="block text-slate-700 dark:text-slate-300 text-sm font-bold mb-2">Password Baru</label>
                    <input type="password" name="password" required
                           class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl px-4 py-3.5 text-sm text-slate-900 dark:text-white focus:outline-none focus:ring-4 focus:ring-sky-500/10 focus:border-sky-500 transition-all"
                           placeholder="min. 6 karakter">
                </div>
                <div>
                    <label class="block text-slate-700 dark:text-slate-300 text-sm font-bold mb-2">Konfirmasi Password Baru</label>
                    <input type="password" name="password_confirmation" required
                           class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl px-4 py-3.5 text-sm text-slate-900 dark:text-white focus:outline-none focus:ring-4 focus:ring-sky-500/10 focus:border-sky-500 transition-all"
                           placeholder="Ulangi password baru">
                </div>
                <button type="submit" class="w-full bg-sky-500 hover:bg-sky-600 text-white font-bold py-3.5 rounded-xl transition-all shadow-sm shadow-sky-500/25 hover:-translate-y-0.5 flex items-center justify-center gap-2">
                    Reset Password <i data-lucide="lock" class="w-4 h-4"></i>
                </button>
            </form>
        </div>
    </div>
</div>
@endsection

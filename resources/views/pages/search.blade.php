@extends('layouts.app')
@section('title', $query ? "Hasil pencarian: $query — Zverse" : 'Cari di Zverse')
@section('content')
<div class="min-h-screen bg-slate-50 dark:bg-slate-950 py-16">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="mb-12 text-center">
            <h1 class="text-4xl font-black text-slate-900 dark:text-white mb-8 tracking-tight">Eksplorasi Zverse</h1>
            <form action="{{ route('search') }}" method="GET" class="relative group max-w-2xl mx-auto">
                <i data-lucide="search" class="w-6 h-6 text-slate-400 absolute left-6 top-1/2 -translate-y-1/2 group-focus-within:text-orange-500 transition-colors"></i>
                <input type="search" name="q" value="{{ $query }}" placeholder="Cari game, film, tren, atau review..."
                       class="w-full bg-white dark:bg-slate-900 border-2 border-slate-200 dark:border-slate-800 rounded-2xl py-5 pl-16 pr-6 text-lg font-medium text-slate-900 dark:text-white focus:outline-none focus:border-orange-500 focus:ring-4 focus:ring-orange-500/10 transition-all shadow-sm">
                <button type="submit" class="absolute right-3 top-1/2 -translate-y-1/2 bg-slate-900 dark:bg-white hover:bg-orange-500 dark:hover:bg-orange-500 text-white dark:text-slate-900 hover:text-white px-6 py-2.5 rounded-xl text-sm font-bold transition-colors">
                    Cari
                </button>
            </form>
        </div>

        @if($query)
        <div class="mb-8 flex items-center justify-between border-b border-slate-200 dark:border-slate-800 pb-4">
            <p class="text-slate-500 text-lg font-medium">Menemukan <span class="text-slate-900 dark:text-white font-black">{{ $results->count() }}</span> hasil untuk "<span class="text-orange-500">{{ $query }}</span>"</p>
        </div>
        
        @if($results->count())
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-2 gap-6">
            @foreach($results as $article)
            <a href="{{ route('article.show', $article->id) }}" class="group bg-white dark:bg-slate-900 rounded-3xl p-3 border border-slate-200 dark:border-slate-800 hover:shadow-xl hover:-translate-y-1 transition-all duration-300 flex items-center gap-4">
                <div class="w-28 h-28 sm:w-32 sm:h-32 rounded-xl overflow-hidden shrink-0 relative">
                    <img src="{{ $article->image }}" alt="{{ $article->title }}" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">
                </div>
                <div class="flex-1 py-2 pr-2">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-purple-500 mb-1 block">{{ $article->category }}</span>
                    <h3 class="text-slate-900 dark:text-white font-bold text-base leading-snug group-hover:text-orange-500 transition-colors line-clamp-2 mb-2">
                        {{ $article->title }}
                    </h3>
                    <div class="flex items-center gap-2 text-slate-400 text-xs font-semibold">
                        <i data-lucide="clock-3" class="w-3 h-3"></i> {{ $article->read_time }}
                    </div>
                </div>
            </a>
            @endforeach
        </div>
        @else
        <div class="text-center py-20">
            <i data-lucide="search-x" class="w-16 h-16 text-slate-300 mx-auto mb-4"></i>
            <h3 class="text-slate-900 dark:text-white text-xl font-black mb-2">Pencarian Tidak Ditemukan</h3>
            <p class="text-slate-500 mb-8 font-medium">Coba gunakan kata kunci lain yang lebih umum.</p>
            <div class="flex flex-wrap justify-center gap-3">
                @foreach(['Games','Musik','Film','Anime','Review','Teknologi'] as $sug)
                <a href="{{ route('search', ['q' => $sug]) }}" class="px-5 py-2 rounded-full border border-slate-200 dark:border-slate-800 text-slate-600 dark:text-slate-300 hover:border-orange-500 hover:text-orange-500 text-sm font-semibold transition-colors bg-white dark:bg-slate-900">{{ $sug }}</a>
                @endforeach
            </div>
        </div>
        @endif
        @else
        <div class="text-center py-10 mt-10 border-t border-slate-200 dark:border-slate-800">
            <h3 class="text-slate-400 font-bold uppercase tracking-widest text-sm mb-6">Pencarian Populer</h3>
            <div class="flex flex-wrap justify-center gap-3">
                @foreach(['GTA VI','Oppenheimer','Review Gadget','Konser 2026','Esports','Netflix','Anime Terbaru'] as $sug)
                <a href="{{ route('search', ['q' => $sug]) }}" class="px-5 py-2.5 rounded-full border border-slate-200 dark:border-slate-800 text-slate-600 dark:text-slate-300 hover:bg-orange-50 hover:border-orange-200 dark:hover:bg-orange-500/10 dark:hover:border-orange-500/30 hover:text-orange-600 dark:hover:text-orange-400 text-sm font-bold transition-all bg-white dark:bg-slate-900 shadow-sm flex items-center gap-2">
                    <i data-lucide="trending-up" class="w-4 h-4"></i> {{ $sug }}
                </a>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</div>
@push('scripts')
<script>lucide.createIcons();</script>
@endpush
@endsection
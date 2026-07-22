@extends('layouts.app')

@section('title', ($meta['label'] ?? ucfirst($category)) . ' — Zverse')

@section('content')
<div class="min-h-screen bg-slate-50 dark:bg-slate-950 pb-20">
    
    {{-- Premium Header --}}
    <div class="relative bg-slate-900 overflow-hidden mb-8 md:mb-12 py-12 md:py-20 px-4 sm:px-6 lg:px-8 border-b border-slate-800">
        <div class="absolute inset-0 opacity-20 bg-[radial-gradient(ellipse_at_top_right,var(--tw-gradient-stops))] from-orange-500 via-purple-600 to-transparent pointer-events-none"></div>
        <div class="max-w-7xl mx-auto relative z-10 flex flex-col md:flex-row md:items-end justify-between gap-4 md:gap-6">
            <div class="flex items-center gap-4 md:gap-6">
                @php $icon = match(strtolower($category)) { 'games' => 'gamepad-2', 'musik' => 'music-4', 'film' => 'film', default => 'layout-grid' }; @endphp
                <div class="w-14 h-14 md:w-20 md:h-20 rounded-xl md:rounded-2xl bg-white/10 backdrop-blur-md border border-white/20 flex items-center justify-center shrink-0">
                    <i data-lucide="{{ $icon }}" class="w-7 h-7 md:w-10 md:h-10 text-white"></i>
                </div>
                <div>
                    <h1 class="text-2xl md:text-4xl lg:text-5xl font-black text-white tracking-tight mb-1 md:mb-2">{{ $meta['label'] ?? ucfirst($category) }}</h1>
                    <p class="text-slate-400 font-medium text-sm md:text-lg max-w-xl">{{ $meta['description'] ?? 'Eksplorasi berita, ulasan, dan tren terbaru.' }}</p>
                </div>
            </div>
            <div class="bg-white/10 backdrop-blur-md border border-white/20 px-4 md:px-5 py-2 md:py-2.5 rounded-lg md:rounded-xl text-white font-bold text-xs md:text-sm flex items-center gap-2 w-fit">
                <i data-lucide="file-text" class="w-3.5 h-3.5 md:w-4 md:h-4 text-orange-400"></i> {{ $articles->count() }} Artikel
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        @if($articles->count())
        @php $first = $articles->first(); $rest = $articles->skip(1); @endphp
        
        {{-- Featured Category Article --}}
        <div class="mb-8 md:mb-10">
            <a href="{{ route('article.show', $first->id) }}" class="group block relative rounded-2xl md:rounded-4xl overflow-hidden h-56 sm:h-80 md:h-100 lg:h-125 shadow-lg md:shadow-2xl shadow-slate-200/50 dark:shadow-none border border-slate-200/50 dark:border-slate-800">
                <img src="{{ $first->image_url }}" alt="{{ $first->title }}" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105">
                <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-900/50 to-transparent"></div>
                <div class="absolute inset-0 flex flex-col justify-end p-5 sm:p-8 md:p-12">
                    @if($first->featured)
                    <span class="inline-flex w-fit items-center gap-1 md:gap-1.5 text-[10px] md:text-xs px-2.5 py-1 md:px-3 md:py-1.5 rounded-full bg-orange-500 text-white font-bold mb-3 md:mb-4 shadow-lg shadow-orange-500/30">
                        <i data-lucide="flame" class="w-3 h-3 md:w-3.5 md:h-3.5"></i> Sorotan
                    </span>
                    @endif
                    <h2 class="text-white text-xl sm:text-3xl md:text-5xl font-black leading-tight mb-2 md:mb-4 group-hover:text-orange-400 transition-colors max-w-4xl">
                        {{ $first->title }}
                    </h2>
                    <p class="text-slate-300 text-xs sm:text-sm md:text-base mb-4 md:mb-6 max-w-2xl line-clamp-1 md:line-clamp-2 font-medium">{{ $first->excerpt }}</p>
                    <div class="flex flex-wrap items-center gap-3 md:gap-4 text-slate-300 text-[11px] md:text-sm font-semibold">
                        <span class="flex items-center gap-1 md:gap-1.5"><i data-lucide="pen-line" class="w-3.5 h-3.5 md:w-4 md:h-4 text-purple-400"></i> {{ $first->author }}</span>
                        <span class="w-1 h-1 rounded-full bg-slate-500"></span>
                        <span class="flex items-center gap-1 md:gap-1.5"><i data-lucide="clock-3" class="w-3.5 h-3.5 md:w-4 md:h-4"></i> {{ $first->read_time }}</span>
                    </div>
                </div>
            </a>
        </div>
        
        {{-- Grid --}}
        <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-3 md:gap-6">
            @foreach($rest as $article)
            <a href="{{ route('article.show', $article->id) }}" class="group bg-white dark:bg-slate-900 rounded-2xl md:rounded-3xl p-2 md:p-3 border border-slate-200 dark:border-slate-800 hover:shadow-2xl hover:shadow-slate-200/50 dark:hover:shadow-slate-900/50 hover:-translate-y-1 transition-all duration-300 flex flex-col h-full">
                <div class="relative rounded-xl md:rounded-2xl overflow-hidden h-32 sm:h-40 md:h-52 mb-3 md:mb-4 shrink-0">
                    <img src="{{ $article->image_url }}" alt="{{ $article->title }}" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">
                </div>
                <div class="px-1 md:px-2 pb-1 md:pb-2 flex-1 flex flex-col">
                    <h3 class="text-slate-900 dark:text-white font-bold text-sm md:text-lg leading-snug group-hover:text-orange-500 transition-colors line-clamp-2 md:line-clamp-3 mb-2 md:mb-4">
                        {{ $article->title }}
                    </h3>
                    <div class="mt-auto flex items-center justify-between text-slate-400 text-[10px] md:text-xs font-semibold pt-2 md:pt-4 border-t border-slate-100 dark:border-slate-800">
                        <span class="flex items-center gap-1 md:gap-1.5"><i data-lucide="pen-line" class="w-3 h-3 md:w-3.5 md:h-3.5"></i> <span class="truncate max-w-16 md:max-w-24">{{ $article->author }}</span></span>
                        <span>{{ $article->read_time }}</span>
                    </div>
                </div>
            </a>
            @endforeach
        </div>
        @else
        {{-- Empty State --}}
        <div class="text-center py-16 md:py-32 max-w-md mx-auto px-4">
            <div class="w-16 h-16 md:w-24 md:h-24 bg-slate-100 dark:bg-slate-900 rounded-full flex items-center justify-center mx-auto mb-4 md:mb-6">
                <i data-lucide="ghost" class="w-8 h-8 md:w-12 md:h-12 text-slate-300 dark:text-slate-700"></i>
            </div>
            <h3 class="text-slate-900 dark:text-white text-xl md:text-2xl font-black mb-2 md:mb-3">Belum ada konten</h3>
            <p class="text-slate-500 text-sm md:text-base font-medium mb-6 md:mb-8">Tim editor Zverse sedang menyiapkan artikel terbaik untuk kategori ini. Cek kembali nanti ya!</p>
            <a href="{{ route('home') }}" class="inline-flex items-center gap-2 bg-slate-900 dark:bg-white text-white dark:text-slate-900 font-bold px-6 md:px-8 py-3 md:py-3.5 rounded-xl hover:bg-orange-500 dark:hover:bg-orange-500 dark:hover:text-white transition-colors">
                Kembali ke Beranda
            </a>
        </div>
        @endif
    </div>
</div>
@push('scripts')
<script>lucide.createIcons();</script>
@endpush
@endsection
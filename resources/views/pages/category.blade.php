@extends('layouts.app')

@section('title', ($meta['label'] ?? ucfirst($category)) . ' — Zverse')

@section('content')
<div class="min-h-screen bg-slate-50 dark:bg-slate-950 pb-20">
    
    {{-- Premium Header --}}
    <div class="relative bg-slate-900 overflow-hidden mb-12 py-20 px-4 sm:px-6 lg:px-8 border-b border-slate-800">
        <div class="absolute inset-0 opacity-20 bg-[radial-gradient(ellipse_at_top_right,var(--tw-gradient-stops))] from-orange-500 via-purple-600 to-transparent pointer-events-none"></div>
        <div class="max-w-7xl mx-auto relative z-10 flex flex-col md:flex-row md:items-end justify-between gap-6">
            <div class="flex items-center gap-6">
                @php $icon = match(strtolower($category)) { 'games' => 'gamepad-2', 'musik' => 'music-4', 'film' => 'film', default => 'layout-grid' }; @endphp
                <div class="w-20 h-20 rounded-2xl bg-white/10 backdrop-blur-md border border-white/20 flex items-center justify-center shrink-0">
                    <i data-lucide="{{ $icon }}" class="w-10 h-10 text-white"></i>
                </div>
                <div>
                    <h1 class="text-4xl sm:text-5xl font-black text-white tracking-tight mb-2">{{ $meta['label'] ?? ucfirst($category) }}</h1>
                    <p class="text-slate-400 font-medium text-lg max-w-xl">{{ $meta['description'] ?? 'Eksplorasi berita, ulasan, dan tren terbaru.' }}</p>
                </div>
            </div>
            <div class="bg-white/10 backdrop-blur-md border border-white/20 px-5 py-2.5 rounded-xl text-white font-bold text-sm flex items-center gap-2">
                <i data-lucide="file-text" class="w-4 h-4 text-orange-400"></i> {{ $articles->count() }} Artikel
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        @if($articles->count())
        @php $first = $articles->first(); $rest = $articles->skip(1); @endphp
        
        {{-- Featured Category Article --}}
        <div class="mb-10">
            <a href="{{ route('article.show', $first->id) }}" class="group block relative rounded-4xl overflow-hidden h-100 sm:h-125 shadow-2xl shadow-slate-200/50 dark:shadow-none border border-slate-200/50 dark:border-slate-800">
                <img src="{{ $first->image_url }}" alt="{{ $first->title }}" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105">
                <div class="absolute inset-0 bg-linear-to-t from-slate-950 via-slate-900/50 to-transparent"></div>
                <div class="absolute inset-0 flex flex-col justify-end p-8 sm:p-12">
                    @if($first->featured)
                    <span class="inline-flex w-fit items-center gap-1.5 text-xs px-3 py-1.5 rounded-full bg-orange-500 text-white font-bold mb-4 shadow-lg shadow-orange-500/30">
                        <i data-lucide="flame" class="w-3.5 h-3.5"></i> Sorotan
                    </span>
                    @endif
                    <h2 class="text-white text-3xl sm:text-5xl font-black leading-tight mb-4 group-hover:text-orange-400 transition-colors max-w-4xl">
                        {{ $first->title }}
                    </h2>
                    <p class="text-slate-300 text-base mb-6 max-w-2xl line-clamp-2 font-medium">{{ $first->excerpt }}</p>
                    <div class="flex flex-wrap items-center gap-4 text-slate-300 text-sm font-semibold">
                        <span class="flex items-center gap-1.5"><i data-lucide="pen-line" class="w-4 h-4 text-purple-400"></i> {{ $first->author }}</span>
                        <span class="w-1 h-1 rounded-full bg-slate-500"></span>
                        <span class="flex items-center gap-1.5"><i data-lucide="clock-3" class="w-4 h-4"></i> {{ $first->read_time }}</span>
                    </div>
                </div>
            </a>
        </div>
        
        {{-- Grid --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @foreach($rest as $article)
            <a href="{{ route('article.show', $article->id) }}" class="group bg-white dark:bg-slate-900 rounded-3xl p-3 border border-slate-200 dark:border-slate-800 hover:shadow-2xl hover:shadow-slate-200/50 dark:hover:shadow-slate-900/50 hover:-translate-y-1 transition-all duration-300 flex flex-col h-full">
                <div class="relative rounded-2xl overflow-hidden h-52 mb-4 shrink-0">
                    <img src="{{ $article->image_url }}" alt="{{ $article->title }}" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">
                </div>
                <div class="px-2 pb-2 flex-1 flex flex-col">
                    <h3 class="text-slate-900 dark:text-white font-bold text-lg leading-snug group-hover:text-orange-500 transition-colors line-clamp-3 mb-4">
                        {{ $article->title }}
                    </h3>
                    <div class="mt-auto flex items-center justify-between text-slate-400 text-xs font-semibold pt-4 border-t border-slate-100 dark:border-slate-800">
                        <span class="flex items-center gap-1.5"><i data-lucide="pen-line" class="w-3.5 h-3.5"></i> {{ $article->author }}</span>
                        <span>{{ $article->read_time }}</span>
                    </div>
                </div>
            </a>
            @endforeach
        </div>
        @else
        {{-- Empty State --}}
        <div class="text-center py-32 max-w-md mx-auto">
            <div class="w-24 h-24 bg-slate-100 dark:bg-slate-900 rounded-full flex items-center justify-center mx-auto mb-6">
                <i data-lucide="ghost" class="w-12 h-12 text-slate-300 dark:text-slate-700"></i>
            </div>
            <h3 class="text-slate-900 dark:text-white text-2xl font-black mb-3">Belum ada konten</h3>
            <p class="text-slate-500 font-medium mb-8">Tim editor Zverse sedang menyiapkan artikel terbaik untuk kategori ini. Cek kembali nanti ya!</p>
            <a href="{{ route('home') }}" class="inline-flex items-center gap-2 bg-slate-900 dark:bg-white text-white dark:text-slate-900 font-bold px-8 py-3.5 rounded-xl hover:bg-orange-500 dark:hover:bg-orange-500 dark:hover:text-white transition-colors">
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
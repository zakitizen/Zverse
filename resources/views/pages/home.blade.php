@extends('layouts.app')

@section('title', 'Zverse — Portal Entertainment & Tech Modern')

@section('content')
<div class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8 py-5 md:py-8">

    {{-- ─── Hero Carousel Premium ─────────────────────────────────────────────── --}}
    @if($featured->count())
    <div class="mb-10 md:mb-14 relative group" id="hero-carousel">
        <div class="overflow-hidden rounded-2xl md:rounded-4xl shadow-lg md:shadow-2xl shadow-slate-200/50 dark:shadow-none border border-slate-200/50 dark:border-slate-800">
            <div id="carousel-track" class="flex transition-transform duration-700 ease-out">
                @foreach($featured as $i => $article)
                @php 
                    $meta = \App\Models\Article::$categoryMeta[$article->category] ?? []; 
                    $icon = match(strtolower($article->category)) {
                        'games' => 'gamepad-2', 'musik' => 'music-4', 'film' => 'film', default => 'sparkles'
                    };
                @endphp
                <div class="min-w-full relative h-56 sm:h-96 md:h-112.5 lg:h-137.5">
                    <img src="{{ $article->image_url }}" alt="{{ $article->title }}"
                         class="w-full h-full object-cover"
                         >
                    {{-- Gradient Overlay --}}
                    <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-900/40 to-transparent"></div>
                    <div class="absolute inset-0 flex flex-col justify-end p-5 sm:p-8 md:p-14">
                        <div class="flex flex-wrap items-center gap-2 md:gap-3 mb-3 md:mb-4">
                            <span class="inline-flex items-center gap-1 md:gap-1.5 text-[10px] md:text-xs px-2.5 py-1 md:px-3 md:py-1.5 rounded-full bg-white/10 backdrop-blur-md text-white border border-white/20 font-bold uppercase tracking-wider">
                                <i data-lucide="{{ $icon }}" class="w-3 h-3 md:w-3.5 md:h-3.5"></i> {{ $meta['label'] ?? $article->category }}
                            </span>
                            @if($article->featured)
                            <span class="inline-flex items-center gap-1 md:gap-1.5 text-[10px] md:text-xs px-2.5 py-1 md:px-3 md:py-1.5 rounded-full bg-gradient-to-r from-orange-500 to-orange-400 text-white font-bold shadow-lg shadow-orange-500/30">
                                <i data-lucide="flame" class="w-3 h-3 md:w-3.5 md:h-3.5"></i> Featured
                            </span>
                            @endif
                        </div>
                        <a href="{{ route('article.show', $article->id) }}" class="block w-fit">
                            <h2 class="text-white text-xl sm:text-3xl md:text-5xl font-black tracking-tight leading-tight mb-2 md:mb-4 hover:text-orange-400 transition-colors duration-300 line-clamp-2 md:line-clamp-3">
                                {{ $article->title }}
                            </h2>
                        </a>
                        <p class="text-slate-300 text-xs sm:text-sm md:text-base mb-4 md:mb-6 line-clamp-1 md:line-clamp-2 max-w-3xl font-medium leading-relaxed">{{ $article->excerpt }}</p>
                        <div class="flex items-center gap-3 md:gap-4 text-slate-300 text-[11px] md:text-sm font-semibold">
                            <span class="flex items-center gap-1 md:gap-1.5"><i data-lucide="pen-line" class="w-3.5 h-3.5 md:w-4 md:h-4 text-orange-400"></i> {{ $article->author }}</span>
                            <span class="w-1 h-1 rounded-full bg-slate-500"></span>
                            <span class="flex items-center gap-1 md:gap-1.5"><i data-lucide="clock-3" class="w-3.5 h-3.5 md:w-4 md:h-4 text-purple-400"></i> {{ $article->read_time }}</span>
                            @if($article->rating) 
                            <span class="w-1 h-1 rounded-full bg-slate-500"></span>
                            <span class="flex items-center gap-1 md:gap-1.5 text-amber-400"><i data-lucide="star" class="w-3.5 h-3.5 md:w-4 md:h-4 fill-amber-400"></i> {{ $article->rating }}</span> 
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        {{-- Custom Dots --}}
        <div class="absolute bottom-4 md:bottom-6 left-1/2 -translate-x-1/2 flex items-center gap-1.5 md:gap-2 bg-slate-900/50 backdrop-blur-md px-3 md:px-4 py-1.5 md:py-2 rounded-full border border-white/10" id="carousel-dots">
            @foreach($featured as $i => $_)
            <button onclick="goToSlide({{ $i }})" class="carousel-dot w-1.5 h-1.5 md:w-2 md:h-2 rounded-full transition-all duration-300 {{ $i === 0 ? 'bg-orange-500 w-5 md:w-8' : 'bg-white/50 hover:bg-white' }}"></button>
            @endforeach
        </div>
        {{-- Navigation --}}
        <button onclick="prevSlide()" class="hidden md:flex absolute left-6 top-1/2 -translate-y-1/2 w-12 h-12 bg-white/10 hover:bg-white/20 backdrop-blur-md border border-white/20 text-white rounded-full items-center justify-center transition-all duration-300 opacity-0 group-hover:opacity-100 hover:scale-110"><i data-lucide="arrow-left" class="w-5 h-5"></i></button>
        <button onclick="nextSlide()" class="hidden md:flex absolute right-6 top-1/2 -translate-y-1/2 w-12 h-12 bg-white/10 hover:bg-white/20 backdrop-blur-md border border-white/20 text-white rounded-full items-center justify-center transition-all duration-300 opacity-0 group-hover:opacity-100 hover:scale-110"><i data-lucide="arrow-right" class="w-5 h-5"></i></button>
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 md:gap-10">
        <div class="lg:col-span-2 space-y-10 md:space-y-16">

            {{-- ─── Category Sections ────────────────────────────────────── --}}
            @foreach($categories as $cat)
            @php
                $meta = \App\Models\Article::$categoryMeta[$cat] ?? [];
                $items = $byCategory[$cat];
                $main = $items->first();
                $rest = $items->skip(1);
                $icon = match(strtolower($cat)) { 'games' => 'gamepad-2', 'musik' => 'music-4', 'film' => 'film', default => 'layout-grid' };
            @endphp
            @if($items->count())
            <section class="group/section">
                <div class="flex items-center justify-between mb-5 md:mb-8 pb-3 md:pb-4 border-b border-slate-200 dark:border-slate-800">
                    <div class="flex items-center gap-2.5 md:gap-3">
                        <div class="w-8 h-8 md:w-10 md:h-10 rounded-xl bg-orange-50 dark:bg-orange-500/10 flex items-center justify-center">
                            <i data-lucide="{{ $icon }}" class="w-4 h-4 md:w-5 md:h-5 text-orange-500"></i>
                        </div>
                        <h2 class="text-lg md:text-2xl font-black tracking-tight text-slate-900 dark:text-white capitalize">{{ $meta['label'] }}</h2>
                    </div>
                    <a href="{{ route('category.show', $cat) }}" class="inline-flex items-center gap-1.5 md:gap-2 text-xs md:text-sm font-bold text-slate-500 hover:text-orange-500 transition-colors">
                        Lihat semua <i data-lucide="arrow-right" class="w-3.5 h-3.5 md:w-4 md:h-4"></i>
                    </a>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 md:gap-6">
                    {{-- Main Card --}}
                    @if($main)
                    <div class="h-full">
                        <a href="{{ route('article.show', $main->id) }}" class="group block h-full bg-white dark:bg-slate-900 rounded-2xl md:rounded-3xl p-2 md:p-3 border border-slate-200 dark:border-slate-800 hover:shadow-2xl hover:shadow-orange-500/5 hover:-translate-y-1 transition-all duration-300">
                            <div class="relative rounded-xl md:rounded-2xl overflow-hidden h-48 sm:h-64 md:h-72 mb-3 md:mb-4">
                                <img src="{{ $main->image_url }}" alt="{{ $main->title }}" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105">
                                <div class="absolute top-2.5 left-2.5 md:top-3 md:left-3 bg-white/90 dark:bg-slate-900/90 backdrop-blur text-[10px] md:text-xs font-bold px-2.5 py-1 md:px-3 md:py-1.5 rounded-full text-slate-900 dark:text-white shadow-sm">{{ $main->read_time }}</div>
                            </div>
                            <div class="px-2 md:px-3 pb-2 md:pb-3">
                                <h3 class="text-base md:text-xl font-bold text-slate-900 dark:text-white leading-snug group-hover:text-orange-500 transition-colors line-clamp-3 mb-2 md:mb-3">
                                    {{ $main->title }}
                                </h3>
                                <div class="flex items-center gap-2 text-slate-500 dark:text-slate-400 text-[11px] md:text-xs font-semibold">
                                    <i data-lucide="pen-line" class="w-3 h-3 md:w-3.5 md:h-3.5"></i> <span>{{ $main->author }}</span>
                                </div>
                            </div>
                        </a>
                    </div>
                    @endif

                    {{-- List Cards --}}
                    <div class="flex flex-col gap-3 md:gap-4">
                        @foreach($rest as $art)
                        <a href="{{ route('article.show', $art->id) }}" class="group flex gap-3 md:gap-4 p-2.5 md:p-3 rounded-xl md:rounded-2xl bg-slate-50 dark:bg-slate-900/50 hover:bg-white dark:hover:bg-slate-800 border border-transparent hover:border-slate-200 dark:hover:border-slate-700 hover:shadow-xl hover:shadow-slate-200/20 dark:hover:shadow-none transition-all duration-300">
                            <div class="w-20 h-20 sm:w-24 sm:h-24 md:w-28 md:h-28 rounded-xl overflow-hidden shrink-0 relative">
                                <img src="{{ $art->image_url }}" alt="{{ $art->title }}" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">
                            </div>
                            <div class="flex flex-col justify-center min-w-0 py-1">
                                <h4 class="text-slate-900 dark:text-white font-bold text-sm md:text-base leading-snug group-hover:text-purple-600 dark:group-hover:text-purple-400 transition-colors line-clamp-2 mb-1.5 md:mb-2">
                                    {{ $art->title }}
                                </h4>
                                <div class="flex items-center gap-2 md:gap-3 text-slate-400 text-[11px] md:text-xs font-medium">
                                    <span class="flex items-center gap-1"><i data-lucide="clock-3" class="w-3 h-3 md:w-3.5 md:h-3.5"></i> {{ $art->read_time }}</span>
                                    @if($art->rating)
                                    <span class="flex items-center gap-1 text-amber-500"><i data-lucide="star" class="w-3 h-3 md:w-3.5 md:h-3.5 fill-amber-500"></i> {{ $art->rating }}</span>
                                    @endif
                                </div>
                            </div>
                        </a>
                        @endforeach
                    </div>
                </div>
            </section>
            @endif
            @endforeach

        </div>

        {{-- ─── Sidebar: Latest Articles ─────────────────────────────────── --}}
        <div class="space-y-6 md:space-y-8 lg:sticky lg:top-24 h-fit">
            
            {{-- Search Quick Widget --}}
            <div class="bg-white dark:bg-slate-900 rounded-2xl md:rounded-3xl border border-slate-200 dark:border-slate-800 shadow-sm p-1.5 md:p-2">
                <form action="{{ route('search') }}" method="GET" class="relative group">
                    <i data-lucide="search" class="w-4 h-4 text-slate-400 absolute left-4 top-1/2 -translate-y-1/2 group-focus-within:text-orange-500 transition-colors"></i>
                    <input type="search" name="q" placeholder="Cari di Zverse..." class="w-full bg-slate-50 dark:bg-slate-950 border-none rounded-xl py-3.5 pl-11 pr-4 text-sm font-medium text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-orange-500/20 transition-all">
                </form>
            </div>

            {{-- Latest Feed --}}
            <div class="bg-white dark:bg-slate-900 rounded-2xl md:rounded-3xl border border-slate-200 dark:border-slate-800 shadow-sm p-4 md:p-6">
                <div class="flex items-center gap-2.5 md:gap-3 mb-4 md:mb-6">
                    <div class="w-7 h-7 md:w-8 md:h-8 rounded-lg bg-orange-50 dark:bg-orange-500/10 flex items-center justify-center">
                        <i data-lucide="sparkles" class="w-3.5 h-3.5 md:w-4 md:h-4 text-orange-500"></i>
                    </div>
                    <span class="text-slate-900 dark:text-white font-black tracking-tight text-base md:text-lg">Terbaru</span>
                </div>
                <div class="space-y-3 md:space-y-4">
                    @foreach($latest as $i => $art)
                    <a href="{{ route('article.show', $art->id) }}" class="group flex gap-3 md:gap-4 items-start">
                        <span class="shrink-0 text-2xl md:text-3xl font-black tabular-nums tracking-tighter transition-colors duration-300 {{ $i === 0 ? 'text-orange-500' : 'text-slate-200 dark:text-slate-800 group-hover:text-slate-400' }}">
                            {{ str_pad($i+1, 2, '0', STR_PAD_LEFT) }}
                        </span>
                        <div class="min-w-0 pt-0.5 md:pt-1">
                            <h3 class="text-slate-800 dark:text-slate-200 text-sm md:text-sm font-bold leading-snug group-hover:text-orange-500 transition-colors line-clamp-2 mb-1.5">
                                {{ $art->title }}
                            </h3>
                            <div class="flex items-center gap-2 text-slate-400 text-[11px] md:text-xs font-medium">
                                <span class="capitalize">{{ $art->category }}</span>
                                <span class="w-1 h-1 rounded-full bg-slate-300 dark:bg-slate-700"></span>
                                <span>{{ $art->read_time }}</span>
                            </div>
                        </div>
                    </a>
                    @endforeach
                </div>
            </div>

        </div>
    </div>
</div>

@push('scripts')
<script>
    lucide.createIcons();
    let current = 0;
    const total = {{ $featured->count() }};
    const track = document.getElementById('carousel-track');
    const dots  = document.querySelectorAll('.carousel-dot');

    function goToSlide(n) {
        current = (n + total) % total;
        track.style.transform = `translateX(-${current * 100}%)`;
        dots.forEach((d, i) => {
            d.className = `carousel-dot w-2 h-2 rounded-full transition-all duration-300 ${i === current ? 'bg-orange-500 w-8' : 'bg-white/50 hover:bg-white'}`;
        });
    }
    function nextSlide() { goToSlide(current + 1); }
    function prevSlide() { goToSlide(current - 1); }
    setInterval(nextSlide, 6000);
</script>
@endpush
@endsection
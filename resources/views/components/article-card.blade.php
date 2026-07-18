@php
    $meta = \App\Models\Article::$categoryMeta[$article->category] ?? [];
    $isHero = $variant ?? false;
@endphp

@if($isHero === 'hero')
<a href="{{ route('article.show', $article->id) }}" class="group block">
    <div class="relative h-72 overflow-hidden rounded-[1.75rem] border border-white/10 shadow-2xl shadow-slate-900/20 sm:h-96">
        <img src="{{ $article->image_url }}" alt="{{ $article->title }}"
             class="h-full w-full object-cover transition-all duration-500 group-hover:scale-105">
        <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-950/30 to-transparent"></div>
        <div class="absolute inset-0 flex flex-col justify-end p-6 sm:p-8">
            <span class="mb-3 inline-flex w-fit items-center gap-2 rounded-full border border-white/20 bg-white/10 px-3 py-1.5 text-[11px] font-bold uppercase tracking-[0.24em] text-white backdrop-blur-md">
                <i data-lucide="sparkles" class="h-3.5 w-3.5"></i>
                {{ $meta['label'] ?? '' }}
            </span>
            <h2 class="mb-3 line-clamp-3 text-xl font-black leading-tight text-white transition-colors duration-300 group-hover:text-orange-300 sm:text-2xl">
                {{ $article->title }}
            </h2>
            <div class="flex flex-wrap items-center gap-2 text-xs font-semibold text-slate-200">
                <span class="flex items-center gap-1.5"><i data-lucide="pen-line" class="h-3.5 w-3.5 text-orange-400"></i> {{ $article->author }}</span>
                <span class="h-1 w-1 rounded-full bg-slate-500"></span>
                <span class="flex items-center gap-1.5"><i data-lucide="calendar-days" class="h-3.5 w-3.5 text-sky-400"></i> {{ \Carbon\Carbon::parse($article->created_at)->locale('id')->isoFormat('D MMM Y') }}</span>
                <span class="h-1 w-1 rounded-full bg-slate-500"></span>
                <span class="flex items-center gap-1.5"><i data-lucide="clock-3" class="h-3.5 w-3.5 text-purple-400"></i> {{ $article->read_time }}</span>
                @if($article->rating)
                    <span class="h-1 w-1 rounded-full bg-slate-500"></span>
                    <span class="flex items-center gap-1.5 text-amber-400"><i data-lucide="star" class="h-3.5 w-3.5 fill-amber-400"></i> {{ $article->rating }}</span>
                @endif
            </div>
        </div>
    </div>
</a>
@else
<a href="{{ route('article.show', $article->id) }}" class="group block overflow-hidden rounded-[1.5rem] border border-slate-200 bg-white shadow-sm transition-all duration-300 hover:-translate-y-1 hover:border-orange-200 hover:shadow-xl dark:border-slate-800 dark:bg-slate-900">
    <div class="relative h-48 overflow-hidden">
        <img src="{{ $article->image_url }}" alt="{{ $article->title }}"
             class="h-full w-full object-cover transition-all duration-500 group-hover:scale-105">
        <div class="absolute inset-0 bg-gradient-to-t from-slate-950/60 via-slate-950/10 to-transparent"></div>
        <div class="absolute left-3 top-3">
            <span class="rounded-full bg-white/90 px-2.5 py-1 text-[11px] font-bold uppercase tracking-[0.2em] text-slate-800 shadow-sm backdrop-blur dark:bg-slate-900/90 dark:text-slate-100">
                {{ $meta['label'] ?? '' }}
            </span>
        </div>
        @if($article->rating)
        <div class="absolute right-3 top-3 flex items-center gap-1 rounded-full bg-slate-950/70 px-2 py-1 text-xs font-bold text-amber-400 backdrop-blur">
            <i data-lucide="star" class="h-3 w-3 fill-amber-400"></i>
            <span>{{ $article->rating }}</span>
        </div>
        @endif
    </div>
    <div class="p-5">
        <h3 class="mb-2 line-clamp-2 text-base font-black leading-snug text-slate-900 transition-colors duration-300 group-hover:text-orange-600 dark:text-white dark:group-hover:text-orange-400">
            {{ $article->title }}
        </h3>
        <p class="mb-4 line-clamp-2 text-sm leading-relaxed text-slate-500 dark:text-slate-400">{{ $article->excerpt }}</p>
        <div class="flex flex-wrap items-center gap-2 text-xs font-semibold text-slate-500 dark:text-slate-400">
            <span class="flex items-center gap-1.5"><i data-lucide="pen-line" class="h-3.5 w-3.5 text-slate-400"></i> {{ $article->author }}</span>
            <span class="h-1 w-1 rounded-full bg-slate-300 dark:bg-slate-700"></span>
            <span class="flex items-center gap-1.5"><i data-lucide="clock-3" class="h-3.5 w-3.5 text-slate-400"></i> {{ $article->read_time }}</span>
        </div>
    </div>
</a>
@endif

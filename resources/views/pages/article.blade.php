@extends('layouts.app')

@section('title', $article->title . ' — Zverse')

@section('content')
@php $meta = \App\Models\Article::$categoryMeta[$article->category] ?? []; @endphp

{{-- Reading Progress Bar --}}
<div id="progress-bar" class="fixed top-0 left-0 h-1.5 bg-linear-to-r from-orange-500 to-purple-500 z-50 w-0 transition-all duration-150 rounded-r-full"></div>

<div class="min-h-screen bg-slate-50 dark:bg-slate-950">
    {{-- Cinematic Hero --}}
    <div class="relative w-full h-[50vh] min-h-100 max-h-150">
        @if($article->image)
            <img src="{{ $article->image_url }}" alt="{{ $article->title }}" class="w-full h-full object-cover" loading="lazy">
        @else
            <div class="w-full h-full bg-linear-to-br from-slate-900 via-slate-700 to-slate-500"></div>
        @endif
        <div class="absolute inset-0 bg-linear-to-t from-slate-50 dark:from-slate-950 via-slate-900/50 to-transparent"></div>
    </div>

    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 -mt-40 relative z-10 pb-20">
        
        {{-- Meta & Title --}}
        <div class="mb-10 text-center">
            <a href="{{ route('category.show', $article->category) }}" class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-white/10 dark:bg-black/30 backdrop-blur-md border border-white/20 text-white text-xs font-bold uppercase tracking-widest mb-6 hover:bg-white/20 transition-colors">
                <i data-lucide="layout-grid" class="w-3.5 h-3.5"></i> {{ $meta['label'] ?? $article->category }}
            </a>
            <h1 class="text-3xl sm:text-5xl font-black text-white drop-shadow-[0_2px_10px_rgba(0,0,0,0.65)] leading-tight tracking-tight mb-6">
                {{ $article->title }}
            </h1>
            <p class="text-lg sm:text-xl text-slate-100 drop-shadow-[0_1px_6px_rgba(0,0,0,0.55)] font-medium leading-relaxed mb-8">
                {{ $article->excerpt }}
            </p>
            
            <div class="flex flex-wrap items-center justify-center gap-6 text-sm font-semibold text-slate-500 dark:text-slate-400 border-y border-slate-200 dark:border-slate-800 py-4 bg-white/70 dark:bg-slate-900/50 backdrop-blur-sm rounded-2xl px-4">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-full bg-linear-to-br from-purple-500 to-orange-500 flex items-center justify-center text-white text-xs font-black shadow-sm">
                        {{ strtoupper(substr($article->author, 0, 1)) }}
                    </div>
                    <span class="text-slate-900 dark:text-white">{{ $article->author }}</span>
                </div>
                <div class="flex items-center gap-2"><i data-lucide="calendar-days" class="w-4 h-4"></i> {{ \Carbon\Carbon::parse($article->created_at)->locale('id')->isoFormat('D MMM Y') }}</div>
                <div class="flex items-center gap-2"><i data-lucide="clock-3" class="w-4 h-4"></i> {{ $article->read_time }}</div>
                @if($article->rating)
                <div class="flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-amber-50 dark:bg-amber-500/10 text-amber-600 dark:text-amber-400">
                    <i data-lucide="star" class="w-3.5 h-3.5 fill-current"></i> {{ $article->rating }}/10
                </div>
                @endif
            </div>
        </div>

        {{-- Content --}}
        <article class="prose prose-lg dark:prose-invert prose-slate max-w-none mb-12 prose-headings:font-black prose-headings:tracking-tight prose-a:text-orange-500 hover:prose-a:text-orange-600 prose-img:rounded-2xl prose-img:shadow-md">
            @php
                $contentLines = preg_split('/\r\n|\r|\n/', trim($article->content)) ?: [];
                $renderMarkdownLine = function ($line) {
                    $line = trim($line);

                    if ($line === '') {
                        return '<div class="h-4"></div>';
                    }

                    if (str_starts_with($line, '## ')) {
                        return '<h2 class="flex items-center gap-3 text-2xl mt-10 mb-4 text-slate-900 dark:text-white"><span class="w-2 h-8 bg-orange-500 rounded-full inline-block"></span>' . e(substr($line, 3)) . '</h2>';
                    }

                    if (str_starts_with($line, '### ')) {
                        return '<h3 class="text-xl mt-8 mb-3 text-slate-800 dark:text-slate-100">' . e(substr($line, 4)) . '</h3>';
                    }

                    if (str_starts_with($line, '- ')) {
                        return '<li class="ml-4 mb-2">' . e(substr($line, 2)) . '</li>';
                    }

                    if (preg_match('/^\d+\./', $line)) {
                        return '<li class="ml-4 mb-2 list-decimal">' . e(preg_replace('/^\d+\.\s*/', '', $line)) . '</li>';
                    }

                    $html = e($line);
                    $html = preg_replace('/!\[([^\]]*)\]\(([^)]+)\)/', '<img src="$2" alt="$1" class="rounded-2xl shadow-md my-6 w-full h-auto object-cover" loading="lazy">', $html);
                    $html = preg_replace('/\*\*(.*?)\*\*/', '<strong class="text-slate-900 dark:text-white">$1</strong>', $html);
                    $html = preg_replace('/\*(.*?)\*/', '<em>$1</em>', $html);

                    if (preg_match('/^https?:\/\//i', trim($line))) {
                        return '<img src="' . e(trim($line)) . '" alt="Gambar artikel" class="rounded-2xl shadow-md my-6 w-full h-auto object-cover" loading="lazy">';
                    }

                    return '<p class="text-slate-600 dark:text-slate-300 leading-loose mb-4">' . $html . '</p>';
                };
            @endphp
            @foreach($contentLines as $line)
                {!! $renderMarkdownLine($line) !!}
            @endforeach
        </article>

        {{-- Tags --}}
        @if($article->tags)
        <div class="flex flex-wrap items-center gap-2 mb-12 pb-12 border-b border-slate-200 dark:border-slate-800">
            <i data-lucide="tags" class="w-5 h-5 text-slate-400 mr-2"></i>
            @foreach($article->tags as $tag)
            <span class="px-4 py-1.5 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 text-sm font-semibold hover:bg-orange-100 hover:text-orange-600 dark:hover:bg-orange-500/20 dark:hover:text-orange-400 transition-colors cursor-pointer">
                {{ $tag }}
            </span>
            @endforeach
        </div>
        @endif

        {{-- Author Premium Card --}}
        <div class="bg-linear-to-br from-slate-900 to-slate-800 rounded-3xl p-8 mb-16 shadow-xl shadow-slate-900/10 flex flex-col sm:flex-row items-center sm:items-start gap-6 text-center sm:text-left border border-slate-700">
            <div class="w-20 h-20 rounded-full bg-linear-to-tr from-orange-500 to-purple-600 p-1 shrink-0">
                <div class="w-full h-full rounded-full bg-slate-900 flex items-center justify-center text-white text-2xl font-black">
                    {{ strtoupper(substr($article->author, 0, 1)) }}
                </div>
            </div>
            <div>
                <h3 class="text-white text-xl font-black mb-1">{{ $article->author }}</h3>
                <p class="text-orange-400 text-sm font-bold uppercase tracking-wider mb-3">Senior Editor Zverse</p>
                <p class="text-slate-300 text-sm leading-relaxed">
                    Menyajikan analisa mendalam dan opini tajam seputar industri {{ strtolower($meta['label'] ?? 'hiburan') }}. Ikuti tulisan terbarunya hanya di Zverse.
                </p>
            </div>
        </div>

        {{-- Like & Share Actions --}}
        <div class="flex flex-wrap items-center justify-center gap-3 mb-16">
            <button id="like-btn" data-id="{{ $article->id }}" class="inline-flex items-center gap-2 rounded-full border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-600 shadow-sm transition-all hover:-translate-y-0.5 hover:border-orange-500 hover:text-orange-500">
                <i data-lucide="heart" class="w-4 h-4"></i>
                <span>Suka</span>
                <span id="like-count" class="font-bold">{{ $article->likes }}</span>
            </button>
            <button onclick="navigator.share ? navigator.share({title:'{{ $article->title }}',url:window.location.href}) : null" class="inline-flex items-center gap-2 rounded-full border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-600 shadow-sm transition-all hover:-translate-y-0.5 hover:border-sky-500 hover:text-sky-500">
                <i data-lucide="share-2" class="w-4 h-4"></i>
                <span>Bagikan</span>
            </button>
        </div>

        {{-- Discussion Section --}}
        <div class="mb-16">
            <div class="flex items-center justify-between mb-8">
                <h3 class="text-2xl font-black text-slate-900 dark:text-white flex items-center gap-3">
                    <i data-lucide="message-circle" class="w-6 h-6 text-orange-500"></i> Diskusi ({{ $comments->count() }})
                </h3>
            </div>

            @auth
            <form action="{{ route('article.comment', $article->id) }}" method="POST" class="mb-10 bg-white dark:bg-slate-900 p-2 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm focus-within:border-orange-500 focus-within:ring-4 focus-within:ring-orange-500/10 transition-all">
                @csrf
                <input type="hidden" name="parent_id" id="parent_id" value="">
                <div class="flex gap-4 p-2">
                    <div class="w-10 h-10 rounded-full bg-linear-to-br {{ auth()->user()->avatar_color }} flex items-center justify-center text-white font-bold shrink-0 shadow-inner">
                        {{ strtoupper(substr(auth()->user()->display_name, 0, 1)) }}
                    </div>
                    <div class="flex-1">
                        <div id="reply-indicator" class="hidden mb-2 rounded-lg border border-orange-200 bg-orange-50 px-3 py-2 text-sm text-orange-700">
                            Membalas komentar <span id="reply-target" class="font-semibold"></span>
                            <button type="button" onclick="clearReply()" class="ml-2 text-xs font-bold underline">Batal</button>
                        </div>
                        <textarea name="body" rows="2" placeholder="Bagikan pendapatmu..." class="w-full bg-transparent border-none focus:ring-0 text-slate-700 dark:text-slate-200 text-sm resize-none mt-2" required></textarea>
                    </div>
                </div>
                <div class="flex justify-end p-2 border-t border-slate-100 dark:border-slate-800 mt-2">
                    <button type="submit" class="bg-slate-900 dark:bg-white text-white dark:text-slate-900 hover:bg-orange-500 dark:hover:bg-orange-500 hover:text-white px-6 py-2 rounded-xl text-sm font-bold transition-colors">Kirim</button>
                </div>
            </form>
            @else
            <div class="bg-slate-100 dark:bg-slate-800/50 rounded-2xl p-8 text-center mb-10 border border-slate-200 dark:border-slate-700">
                <i data-lucide="message-square-dashed" class="w-10 h-10 text-slate-400 mx-auto mb-3"></i>
                <h4 class="text-slate-900 dark:text-white font-bold mb-2">Ikut Berdiskusi</h4>
                <p class="text-slate-500 text-sm mb-4">Masuk ke akun Zverse kamu untuk memberikan komentar.</p>
                <a href="{{ route('login') }}" class="inline-flex items-center gap-2 bg-slate-900 dark:bg-white text-white dark:text-slate-900 font-bold px-6 py-2.5 rounded-xl hover:bg-orange-500 dark:hover:bg-orange-500 dark:hover:text-white transition-colors">
                    Masuk <i data-lucide="arrow-right" class="w-4 h-4"></i>
                </a>
            </div>
            @endauth

            <div class="space-y-6">
                @forelse($comments as $comment)
                <div class="flex gap-4">
                    <div class="w-12 h-12 rounded-full bg-linear-to-br {{ $comment->avatar_color }} flex items-center justify-center text-white font-bold shrink-0 shadow-sm">
                        {{ strtoupper(substr($comment->author_name, 0, 1)) }}
                    </div>
                    <div class="flex-1 bg-white dark:bg-slate-900 rounded-2xl rounded-tl-none p-5 border border-slate-200 dark:border-slate-800 shadow-sm">
                        <div class="flex items-center justify-between mb-2">
                            <span class="font-bold text-slate-900 dark:text-white">{{ $comment->author_name }}</span>
                            <span class="text-xs font-semibold text-slate-400">{{ $comment->created_at->diffForHumans() }}</span>
                        </div>
                        <p class="text-slate-600 dark:text-slate-300 text-sm leading-relaxed">{{ $comment->body }}</p>
                        @auth
                        <button type="button" onclick="setReply('{{ $comment->id }}', '{{ addslashes($comment->author_name) }}')" class="mt-3 text-xs font-semibold text-orange-500 hover:text-orange-600">Balas</button>
                        @endauth

                        @if($comment->replies->isNotEmpty())
                        <div class="mt-4 space-y-3 pl-4 border-l-2 border-slate-200 dark:border-slate-800">
                            @foreach($comment->replies as $reply)
                            <div class="rounded-xl bg-slate-50 dark:bg-slate-800/60 p-3">
                                <div class="flex items-center justify-between mb-1">
                                    <span class="font-semibold text-slate-900 dark:text-white text-sm">{{ $reply->author_name }}</span>
                                    <span class="text-xs font-semibold text-slate-400">{{ $reply->created_at->diffForHumans() }}</span>
                                </div>
                                <p class="text-slate-600 dark:text-slate-300 text-sm leading-relaxed">{{ $reply->body }}</p>
                            </div>
                            @endforeach
                        </div>
                        @endif
                    </div>
                </div>
                @empty
                <p class="text-center text-slate-500 text-sm italic py-8">Belum ada komentar. Jadilah yang pertama memberikan pendapat!</p>
                @endforelse
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
lucide.createIcons();

// Progress Bar Logic
window.addEventListener('scroll', () => {
    const winScroll = document.body.scrollTop || document.documentElement.scrollTop;
    const height = document.documentElement.scrollHeight - document.documentElement.clientHeight;
    const scrolled = (winScroll / height) * 100;
    document.getElementById('progress-bar').style.width = scrolled + '%';
});

// Like Logic
document.getElementById('like-btn')?.addEventListener('click', async function() {
    const id = this.dataset.id;
    const res = await fetch(`/article/${id}/like`, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
    });
    if (res.ok) {
        const data = await res.json();
        document.getElementById('like-count').textContent = data.likes;
        this.classList.add('border-orange-500', 'text-orange-500', 'bg-orange-50', 'dark:bg-orange-500/10');
        this.querySelector('i').classList.add('fill-orange-500');
        this.disabled = true;
    }
});

function setReply(commentId, authorName) {
    const input = document.getElementById('parent_id');
    const indicator = document.getElementById('reply-indicator');
    const target = document.getElementById('reply-target');
    if (input && indicator && target) {
        input.value = commentId;
        target.textContent = authorName;
        indicator.classList.remove('hidden');
        document.querySelector('textarea[name="body"]').focus();
    }
}

function clearReply() {
    const input = document.getElementById('parent_id');
    const indicator = document.getElementById('reply-indicator');
    if (input && indicator) {
        input.value = '';
        indicator.classList.add('hidden');
    }
}
</script>
<style>
    .sticky-actions { position: sticky; top: 120px; z-index: 40; height: fit-content; }
</style>
@endpush
@endsection
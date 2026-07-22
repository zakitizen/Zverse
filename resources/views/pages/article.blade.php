@extends('layouts.app')

@section('title', $article->title . ' — Zverse')

@section('content')
@php $meta = \App\Models\Article::$categoryMeta[$article->category] ?? []; @endphp

{{-- Reading Progress Bar --}}
<div id="progress-bar" class="fixed top-0 left-0 h-1.5 bg-gradient-to-r from-orange-500 to-purple-500 z-50 w-0 transition-all duration-150 rounded-r-full"></div>

<div class="min-h-screen bg-slate-50 dark:bg-slate-950">
    {{-- Cinematic Hero --}}
    <div class="relative w-full h-[40vh] sm:h-[50vh] min-h-64 sm:min-h-100 max-h-150">
        @if($article->image)
            <img src="{{ $article->image_url }}" alt="{{ $article->title }}" class="w-full h-full object-cover" loading="lazy">
        @else
            <div class="w-full h-full bg-gradient-to-br from-slate-900 via-slate-700 to-slate-500"></div>
        @endif
        <div class="absolute inset-0 bg-gradient-to-t from-slate-50 dark:from-slate-950 via-slate-900/50 to-transparent"></div>
    </div>

    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 -mt-28 sm:-mt-40 relative z-10 pb-16 md:pb-20">
        
        {{-- Meta & Title --}}
        <div class="mb-8 md:mb-10 text-center">
            <a href="{{ route('category.show', $article->category) }}" class="inline-flex items-center gap-1.5 md:gap-2 px-3 md:px-4 py-1 rounded-full bg-white/10 dark:bg-black/30 backdrop-blur-md border border-white/20 text-white text-[10px] md:text-xs font-bold uppercase tracking-widest mb-4 md:mb-6 hover:bg-white/20 transition-colors">
                <i data-lucide="layout-grid" class="w-3 h-3 md:w-3.5 md:h-3.5"></i> {{ $meta['label'] ?? $article->category }}
            </a>
            <h1 class="text-2xl sm:text-4xl md:text-5xl font-black text-white drop-shadow-[0_2px_10px_rgba(0,0,0,0.65)] leading-tight tracking-tight mb-3 md:mb-6">
                {{ $article->title }}
            </h1>
            <p class="text-sm sm:text-lg md:text-xl text-slate-100 drop-shadow-[0_1px_6px_rgba(0,0,0,0.55)] font-medium leading-relaxed mb-6 md:mb-8">
                {{ $article->excerpt }}
            </p>
            
            <div class="flex flex-wrap items-center justify-center gap-4 md:gap-6 text-xs md:text-sm font-semibold text-slate-500 dark:text-slate-400 border-y border-slate-200 dark:border-slate-800 py-3 md:py-4 bg-white/70 dark:bg-slate-900/50 backdrop-blur-sm rounded-xl md:rounded-2xl px-3 md:px-4">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-full bg-gradient-to-br from-purple-500 to-orange-500 flex items-center justify-center text-white text-xs font-black shadow-sm">
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
                        $url = e(trim($line));
                        return '<a href="' . $url . '" target="_blank" rel="noopener" class="text-orange-500 hover:text-orange-600 underline break-all">' . $url . '</a>';
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
        <div class="flex flex-wrap items-center gap-1.5 md:gap-2 mb-8 md:mb-12 pb-8 md:pb-12 border-b border-slate-200 dark:border-slate-800">
            <i data-lucide="tags" class="w-4 h-4 md:w-5 md:h-5 text-slate-400 mr-1 md:mr-2"></i>
            @foreach($article->tags as $tag)
            <span class="px-3 md:px-4 py-1 md:py-1.5 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 text-[11px] md:text-sm font-semibold hover:bg-orange-100 hover:text-orange-600 dark:hover:bg-orange-500/20 dark:hover:text-orange-400 transition-colors cursor-pointer">
                {{ $tag }}
            </span>
            @endforeach
        </div>
        @endif

        @if($article->reviewed_by)
        {{-- Editor Approval Card --}}
        <div class="relative mb-12 md:mb-16 max-w-2xl mx-auto">
            <div class="absolute top-0 left-0 right-0 h-0.75 rounded-t-5xl opacity-70" style="background: linear-gradient(to right, #4F46E5, #3B82F6, #FF7A1A);"></div>

            <div class="bg-white border border-slate-200 dark:bg-slate-900 dark:border-slate-800 rounded-3xl md:rounded-5xl p-5 md:p-8 shadow-lg shadow-slate-900/8 hover:shadow-xl hover:shadow-slate-900/12 transition-all duration-250 hover:-translate-y-1">
                <div class="flex flex-col sm:flex-row items-center sm:items-center gap-4 md:gap-6">
                    <div class="relative shrink-0">
                        <div class="absolute inset-0 w-16 h-16 md:w-20 md:h-20 rounded-full bg-blue-400 opacity-20 blur-lg"></div>
                        <div class="relative w-16 h-16 md:w-20 md:h-20 rounded-full bg-gradient-to-br from-slate-200 to-slate-300 dark:from-slate-700 dark:to-slate-800 flex items-center justify-center text-xl md:text-2xl font-black text-slate-900 dark:text-white shadow-md">
                            {{ strtoupper(substr($article->reviewed_by, 0, 1)) }}
                        </div>
                    </div>

                    <div class="flex-1 text-center sm:text-left">
                        <div class="flex flex-col sm:flex-row sm:items-center gap-1.5 md:gap-2 mb-2">
                            <h3 class="text-xl md:text-2xl font-black text-slate-900 dark:text-white">{{ $article->reviewed_by }}</h3>
                            <div class="inline-flex items-center gap-1 px-2.5 md:px-3 py-0.5 md:py-1 bg-blue-50 dark:bg-blue-500/10 rounded-full w-fit mx-auto sm:mx-0">
                                <i data-lucide="check-circle" class="w-3.5 h-3.5 md:w-4 md:h-4 text-blue-600 dark:text-blue-400"></i>
                                <span class="text-[10px] md:text-xs font-bold text-blue-600 dark:text-blue-400">Verified Editor</span>
                            </div>
                        </div>

                        <p class="text-[11px] md:text-sm font-bold uppercase tracking-widest text-orange-500 dark:text-orange-400 mb-2 md:mb-3">
                            Pemimpin Redaksi • Zverse
                        </p>

                        <div class="flex flex-col sm:flex-row gap-2 md:gap-3 text-[11px] md:text-xs font-semibold text-slate-500 dark:text-slate-400">
                            <div class="inline-flex items-center gap-1.5 md:gap-2 px-2.5 md:px-3 py-1 md:py-1.5 rounded-lg bg-slate-50 dark:bg-slate-800 w-fit mx-auto sm:mx-0">
                                <i data-lucide="check-circle-2" class="w-3.5 h-3.5 md:w-4 md:h-4 text-emerald-500"></i>
                                <span>Approved for Publication</span>
                            </div>
                            <div class="inline-flex items-center gap-1.5 md:gap-2 px-2.5 md:px-3 py-1 md:py-1.5 rounded-lg bg-slate-50 dark:bg-slate-800 w-fit mx-auto sm:mx-0">
                                <i data-lucide="shield-check" class="w-3.5 h-3.5 md:w-4 md:h-4 text-blue-500"></i>
                                <span>Verified by Editorial Team</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        {{-- Share Action --}}
        <div class="mb-10 md:mb-16 flex justify-center">
            <button onclick="navigator.share ? navigator.share({title:'{{ $article->title }}',url:window.location.href}) : null" class="inline-flex items-center gap-2 rounded-full border border-slate-200 bg-white px-4 md:px-4 py-2 text-xs md:text-sm font-semibold text-slate-600 shadow-sm transition-all hover:-translate-y-0.5 hover:border-sky-500 hover:text-sky-500">
                <i data-lucide="share-2" class="w-3.5 h-3.5 md:w-4 md:h-4"></i>
                <span>Bagikan</span>
            </button>
        </div>

        {{-- Discussion Section --}}
        <div class="mb-12 md:mb-16">
            <div class="flex items-center justify-between mb-6 md:mb-8">
                <h3 class="text-lg md:text-2xl font-black text-slate-900 dark:text-white flex items-center gap-2 md:gap-3">
                    <i data-lucide="message-circle" class="w-5 h-5 md:w-6 md:h-6 text-orange-500"></i> Diskusi <span id="comment-count" class="text-base md:text-xl">({{ $comments->count() }})</span>
                </h3>
            </div>

            @auth
            <form id="comment-form" data-article-id="{{ $article->id }}" class="mb-10">
                @csrf
                <input type="hidden" name="parent_id" id="parent_id" value="">
                <input type="hidden" name="reply_to_user_id" id="reply_to_user_id" value="">
                <input type="hidden" name="comment_id" id="comment_id" value="">
                <input type="hidden" name="mode" id="comment-mode" value="create">
                <div class="flex gap-3">
                    <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-gradient-to-br {{ auth()->user()->avatar_color }} text-xs font-black text-white shadow-sm">
                        {{ strtoupper(substr(auth()->user()->display_name, 0, 1)) }}
                    </div>
                    <div class="flex-1">
                        <div id="reply-indicator" class="mb-2 hidden text-xs text-orange-600">
                            Membalas <span id="reply-target" class="font-semibold"></span>
                            <button type="button" onclick="clearReply()" class="ml-1 font-bold underline">Batal</button>
                        </div>
                        <textarea id="comment-textarea" name="content" rows="1" placeholder="Bagikan pendapatmu..." class="w-full resize-none border-0 border-b border-slate-200 bg-transparent px-0 pb-2 text-sm text-slate-700 placeholder:text-slate-400 focus:border-orange-500 focus:ring-0 dark:border-slate-700 dark:text-slate-200 dark:placeholder:text-slate-500" required></textarea>
                        <div class="mt-2 flex justify-end">
                            <button type="submit" class="text-xs font-bold text-orange-500 hover:text-orange-600">Kirim</button>
                        </div>
                    </div>
                </div>
            </form>
            @else
            <div class="mb-10 rounded-2xl border border-slate-200 bg-slate-100 p-8 text-center dark:border-slate-700 dark:bg-slate-800/50">
                <i data-lucide="message-square-dashed" class="mx-auto mb-3 h-10 w-10 text-slate-400"></i>
                <h4 class="mb-2 font-bold text-slate-900 dark:text-white">Ikut Berdiskusi</h4>
                <p class="mb-4 text-sm text-slate-500">Masuk ke akun Zverse kamu untuk memberikan komentar.</p>
                <a href="{{ route('login') }}" class="inline-flex items-center gap-2 rounded-xl bg-slate-900 px-6 py-2.5 font-bold text-white transition-colors hover:bg-orange-500 dark:bg-white dark:text-slate-900 dark:hover:bg-orange-500 dark:hover:text-white">
                    Masuk <i data-lucide="arrow-right" class="h-4 w-4"></i>
                </a>
            </div>
            @endauth

            <div id="comments-list" class="space-y-5">
                @forelse($comments as $comment)
                    @include('partials.comment-card', ['comment' => $comment])
                @empty
                    <p class="py-8 text-center text-sm italic text-slate-500">Belum ada komentar. Jadilah yang pertama memberikan pendapat!</p>
                @endforelse
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
lucide.createIcons();

window.addEventListener('scroll', () => {
    const winScroll = document.body.scrollTop || document.documentElement.scrollTop;
    const height = document.documentElement.scrollHeight - document.documentElement.clientHeight;
    const scrolled = (winScroll / height) * 100;
    document.getElementById('progress-bar').style.width = scrolled + '%';
});

const form = document.getElementById('comment-form');
const textarea = document.getElementById('comment-textarea');
const parentInput = document.getElementById('parent_id');
const replyToUserInput = document.getElementById('reply_to_user_id');
const modeInput = document.getElementById('comment-mode');
const commentIdInput = document.getElementById('comment_id');
const replyIndicator = document.getElementById('reply-indicator');
const replyTarget = document.getElementById('reply-target');
const commentsList = document.getElementById('comments-list');
const commentCount = document.getElementById('comment-count');
const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
const articleId = form?.dataset.articleId;

function resetComposer() {
    if (!form) return;
    form.reset();
    parentInput.value = '';
    replyToUserInput.value = '';
    commentIdInput.value = '';
    modeInput.value = 'create';
    replyIndicator.classList.add('hidden');
    textarea.value = '';
}

function setReply(commentId, authorName, userId) {
    if (!parentInput || !replyIndicator || !replyTarget || !textarea) return;
    parentInput.value = commentId;
    replyToUserInput.value = userId || '';
    replyTarget.textContent = '@' + authorName;
    replyIndicator.classList.remove('hidden');
    modeInput.value = 'create';
    commentIdInput.value = '';
    textarea.value = '@' + authorName + ' ';
    textarea.focus();
}

function clearReply() {
    if (!parentInput || !replyIndicator || !replyTarget) return;
    parentInput.value = '';
    replyToUserInput.value = '';
    replyTarget.textContent = '';
    replyIndicator.classList.add('hidden');
    modeInput.value = 'create';
    commentIdInput.value = '';
}

form?.addEventListener('submit', async function (event) {
    event.preventDefault();
    if (!textarea) return;

    const content = textarea.value.trim();
    if (!content) return;

    const isEdit = modeInput.value === 'edit';
    const route = isEdit ? `/article/${articleId}/comments/${commentIdInput.value}` : `/article/${articleId}/comments`;

    const payload = new FormData(form);
    const normalizedContent = content.replace(/^@[^\s]+\s*/, '');
    payload.set('content', normalizedContent);
    if (isEdit) {
        payload.set('_method', 'PATCH');
    }

    const response = await fetch(route, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
        },
        body: payload,
    });

    const data = await response.json();
    if (!response.ok) {
        window.alert(data.message || 'Komentar gagal dikirim.');
        return;
    }

    if (isEdit) {
        const currentCard = document.querySelector(`.comment-card[data-comment-id="${commentIdInput.value}"]`);
        if (currentCard) {
            currentCard.outerHTML = data.html;
        }
    } else {
        const html = data.html;
        if (parentInput.value) {
            const parentCard = document.querySelector(`.comment-card[data-comment-id="${parentInput.value}"]`);
            if (parentCard) {
                const contentArea = parentCard.querySelector('.flex-1');
                if (contentArea) {
                    const repliesContainer = parentCard.querySelector('.comment-replies');
                    if (repliesContainer) {
                        repliesContainer.insertAdjacentHTML('beforeend', html);
                    } else {
                        contentArea.insertAdjacentHTML('beforeend', `<div class="comment-replies mt-3 space-y-3 border-l border-slate-200 pl-4 dark:border-slate-700">${html}</div>`);
                    }
                }
            }
        } else {
            commentsList.insertAdjacentHTML('afterbegin', html);
        }
    }

    resetComposer();
    if (commentCount) {
        const currentCount = parseInt(commentCount.textContent.replace(/\D/g, ''), 10) || 0;
        commentCount.textContent = `(${currentCount + (isEdit ? 0 : 1)})`;
    }
});

commentsList?.addEventListener('click', async function (event) {
    const button = event.target.closest('button');
    if (!button) return;

    const commentId = button.dataset.commentId;
    if (!commentId) return;

    if (button.classList.contains('reply-comment')) {
        const author = button.dataset.author || 'Pengguna';
        const userId = button.dataset.userId || '';
        setReply(commentId, author, userId);
        return;
    }

    if (button.classList.contains('edit-comment')) {
        const card = document.querySelector(`.comment-card[data-comment-id="${commentId}"]`);
        const content = card?.querySelector('.comment-content')?.textContent?.trim() || '';
        modeInput.value = 'edit';
        commentIdInput.value = commentId;
        textarea.value = content;
        textarea.focus();
        return;
    }

    if (button.classList.contains('delete-comment')) {
        if (!window.confirm('Hapus komentar ini?')) return;
        const response = await fetch(`/article/${articleId}/comments/${commentId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
        });

        const data = await response.json();
        if (!response.ok) {
            window.alert(data.message || 'Komentar gagal dihapus.');
            return;
        }

        const card = document.querySelector(`.comment-card[data-comment-id="${commentId}"]`);
        if (card) {
            card.outerHTML = data.html;
        }
        if (commentCount) {
            const currentCount = parseInt(commentCount.textContent.replace(/\D/g, ''), 10) || 0;
            commentCount.textContent = `(${Math.max(currentCount - 1, 0)})`;
        }
    }
});
</script>
<style>
    .sticky-actions { position: sticky; top: 120px; z-index: 40; height: fit-content; }
    .h-0\.75 { height: 3px; }
    .rounded-5xl { border-radius: 20px; }
</style>
@endpush
@endsection
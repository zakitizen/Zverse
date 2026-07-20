@php
$canManage = auth()->check() && !$comment->trashed() && $comment->user_id === auth()->id();
@endphp

<div class="comment-card flex gap-3" data-comment-id="{{ $comment->id }}">
    <div class="w-8 h-8 mt-0.5 rounded-full bg-gradient-to-br {{ $comment->avatar_color ?? ($comment->user?->avatar_color ?? 'from-orange-500 to-amber-400') }} flex items-center justify-center text-white text-xs font-black shrink-0 shadow-sm">
        {{ strtoupper(substr($comment->user?->display_name ?? $comment->author_name ?? 'U', 0, 1)) }}
    </div>

    <div class="flex-1 min-w-0">
        <div class="flex items-baseline gap-2 flex-wrap">
            <span class="font-semibold text-sm text-slate-900 dark:text-white">
                {{ $comment->user?->display_name ?? $comment->author_name ?? 'Pengguna' }}
            </span>
            @if($comment->replyUser)
                <span class="text-xs font-medium text-orange-500">@<span>{{ $comment->replyUser->username }}</span></span>
            @endif
            <span class="text-xs text-slate-400">&middot;</span>
            <span class="text-xs text-slate-400">{{ $comment->created_at?->diffForHumans() }}</span>
        </div>

        @if($comment->trashed())
            <p class="text-sm italic text-slate-400 mt-1">Komentar telah dihapus.</p>
        @else
            <p class="comment-content text-sm leading-relaxed text-slate-700 dark:text-slate-300 mt-1">{{ $comment->content }}</p>
        @endif

        @auth
            @if(!$comment->trashed())
                <div class="flex items-center gap-4 mt-2">
                    @if($canManage)
                        <button type="button" class="edit-comment text-xs font-semibold text-slate-500 hover:text-orange-500" data-comment-id="{{ $comment->id }}">Edit</button>
                        <button type="button" class="delete-comment text-xs font-semibold text-slate-500 hover:text-rose-500" data-comment-id="{{ $comment->id }}">Hapus</button>
                    @endif
                    <button type="button" class="reply-comment text-xs font-semibold text-slate-500 hover:text-orange-500" data-comment-id="{{ $comment->id }}" data-author="{{ $comment->user?->display_name ?? $comment->author_name ?? 'Pengguna' }}" data-user-id="{{ $comment->user_id }}">Balas</button>
                </div>
            @endif
        @endauth

        @if($comment->replies->isNotEmpty())
            <div class="comment-replies mt-3 space-y-3 border-l border-slate-200 pl-4 dark:border-slate-700">
                @foreach($comment->replies as $reply)
                    @include('partials.comment-card', ['comment' => $reply])
                @endforeach
            </div>
        @endif
    </div>
</div>

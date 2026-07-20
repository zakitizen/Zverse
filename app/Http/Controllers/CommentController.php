<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    public function store(Request $request, Article $article)
    {
        if (!Auth::check()) {
            return $this->respondUnauthorized();
        }

        $data = $this->validateComment($request, $article);
        $comment = $this->createComment($request, $article, $data);

        return $this->respondWithComment($comment, 'Komentar berhasil dikirim.');
    }

    public function reply(Request $request, Article $article, Comment $comment)
    {
        if (!Auth::check()) {
            return $this->respondUnauthorized();
        }

        $data = $this->validateComment($request, $article, $comment);
        $reply = $this->createComment($request, $article, $data, $comment);

        return $this->respondWithComment($reply, 'Balasan berhasil dikirim.');
    }

    public function update(Request $request, Article $article, Comment $comment)
    {
        if (!Auth::check()) {
            return $this->respondUnauthorized();
        }

        if ($comment->user_id !== Auth::id()) {
            return $this->respondForbidden();
        }

        $validated = $this->validateComment($request, $article);
        $content = $this->normalizeContent($validated['content'], $validated['reply_to_user_id'] ?? null);

        $comment->forceFill([
            'content' => $content,
            'body' => $content,
        ])->save();

        $comment->load(['user', 'replyUser', 'replies' => function ($query) {
            $query->with(['user', 'replyUser']);
        }]);

        return $this->respondWithComment($comment, 'Komentar berhasil diperbarui.');
    }

    public function destroy(Request $request, Article $article, Comment $comment)
    {
        if (!Auth::check()) {
            return $this->respondUnauthorized();
        }

        if ($comment->user_id !== Auth::id()) {
            return $this->respondForbidden();
        }

        $comment->delete();

        $comment->load(['user', 'replyUser', 'replies' => function ($query) {
            $query->with(['user', 'replyUser']);
        }]);

        return $this->respondWithComment($comment, 'Komentar berhasil dihapus.', true);
    }

    public function loadReplies(Request $request, Article $article, Comment $comment)
    {
        $replies = $comment->replies()->with(['user', 'replyUser'])->get();

        return response()->json([
            'html' => view('partials.comment-card', ['comment' => $comment->load(['user', 'replyUser'])])->render(),
            'replies' => $replies->map(fn (Comment $reply) => [
                'id' => $reply->id,
                'content' => $reply->content,
                'created_at' => $reply->created_at->toIso8601String(),
            ])->values(),
        ]);
    }

    private function validateComment(Request $request, Article $article, ?Comment $parent = null): array
    {
        $content = (string) ($request->input('content', $request->input('body', '')));
        $content = trim($content);

        if ($content === '') {
            throw new \InvalidArgumentException('Komentar tidak boleh kosong.');
        }

        if (mb_strlen($content) > 1000) {
            throw new \InvalidArgumentException('Komentar terlalu panjang.');
        }

        if ($request->filled('parent_id')) {
            $parentComment = Comment::where('article_id', $article->id)->find($request->input('parent_id'));
            if (!$parentComment) {
                throw new \InvalidArgumentException('Komentar yang ingin dibalas tidak ditemukan.');
            }
        }

        return [
            'content' => $content,
            'reply_to_user_id' => $request->input('reply_to_user_id'),
            'parent_id' => $request->input('parent_id'),
        ];
    }

    private function createComment(Request $request, Article $article, array $data, ?Comment $parent = null): Comment
    {
        $user = Auth::user();
        $content = $this->normalizeContent($data['content'], $data['reply_to_user_id'] ?? null);

        $comment = Comment::create([
            'article_id' => $article->id,
            'user_id' => $user->id,
            'parent_id' => $parent?->id ?? ($data['parent_id'] ? (int) $data['parent_id'] : null),
            'reply_to_user_id' => $this->resolveReplyTargetUserId($request, $parent, $data),
            'author_name' => $user->display_name ?? $user->username ?? 'Anonim',
            'avatar_color' => $user->avatar_color ?? 'from-orange-500 to-amber-400',
            'content' => $content,
            'body' => $content,
        ]);

        return $comment->load(['user', 'replyUser', 'replies' => function ($query) {
            $query->with(['user', 'replyUser']);
        }]);
    }

    private function resolveReplyTargetUserId(Request $request, ?Comment $parent, array $data): ?int
    {
        if ($request->filled('reply_to_user_id')) {
            return (int) $request->input('reply_to_user_id');
        }

        if ($parent) {
            return $parent->user_id;
        }

        if (!empty($data['parent_id'])) {
            $parentComment = Comment::find((int) $data['parent_id']);
            return $parentComment?->user_id;
        }

        return null;
    }

    private function normalizeContent(string $content, ?int $replyToUserId = null): string
    {
        $trimmed = trim($content);
        if ($replyToUserId) {
            $trimmed = preg_replace('/^@[^\s]+\s*/', '', $trimmed) ?? $trimmed;
        }

        return trim($trimmed);
    }

    private function respondWithComment(Comment $comment, string $message, bool $deleted = false)
    {
        if (request()->expectsJson()) {
            return response()->json([
                'message' => $message,
                'html' => view('partials.comment-card', ['comment' => $comment])->render(),
                'deleted' => $deleted,
            ]);
        }

        return back()->with('success', $message);
    }

    private function respondUnauthorized()
    {
        if (request()->expectsJson()) {
            return response()->json(['message' => 'Silakan login terlebih dahulu.'], 401);
        }

        return redirect()->route('login')->withErrors(['comment' => 'Silakan login terlebih dahulu.']);
    }

    private function respondForbidden()
    {
        if (request()->expectsJson()) {
            return response()->json(['message' => 'Anda tidak memiliki akses untuk melakukan aksi ini.'], 403);
        }

        return back()->withErrors(['comment' => 'Anda tidak memiliki akses untuk melakukan aksi ini.']);
    }
}

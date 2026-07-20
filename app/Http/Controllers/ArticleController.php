<?php

namespace App\Http\Controllers;

use App\Models\Article;

class ArticleController extends Controller
{
    public function show(string $id)
    {
        $query = Article::query()->whereIn('status', ['published', 'approved']);

        $article = is_numeric($id)
            ? $query->where('id', $id)->firstOrFail()
            : $query->where('slug', $id)->firstOrFail();

        $comments = $article->comments()
            ->with(['user', 'replyUser', 'replies' => function ($query) {
                $query->with(['user', 'replyUser']);
            }])
            ->get();

        // Eager load deeper nesting to avoid N+1
        foreach ($comments as $comment) {
            if ($comment->replies->isNotEmpty()) {
                $comment->loadMissing('replies.replies.user', 'replies.replies.replyUser');
            }
        }

        return view('pages.article', compact('article', 'comments'));
    }
}

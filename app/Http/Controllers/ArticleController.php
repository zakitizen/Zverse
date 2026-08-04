<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\View\View;

/**
 * Controller untuk halaman detail artikel.
 */
class ArticleController extends Controller
{
    /**
     * Menampilkan halaman satu artikel beserta komentarnya.
     *
     * Artikel dapat diakses via ID (angka) atau slug (teks). Hanya artikel
     * berstatus `published` yang bisa dilihat publik — selain itu 404.
     *
     * Komentar di-eager-load beserta user, replyUser, dan balasan (termasuk
     * balasan bersarang 2 level) untuk menghindari masalah N+1 query.
     *
     * @param string $id ID atau slug artikel.
     *
     * @return View
     */
    public function show(string $id)
    {
        $query = Article::query()->where('status', 'published');

        $article = is_numeric($id)
            ? $query->where('id', $id)->firstOrFail()
            : $query->where('slug', $id)->firstOrFail();

        $comments = $article->comments()
            ->with(['user', 'replyUser', 'replies' => function ($query) {
                $query->with(['user', 'replyUser']);
            }])
            ->get();

        // Eager load balasan yang lebih dalam agar tidak terjadi N+1 query.
        foreach ($comments as $comment) {
            if ($comment->replies->isNotEmpty()) {
                $comment->loadMissing('replies.replies.user', 'replies.replies.replyUser');
            }
        }

        return view('pages.article', compact('article', 'comments'));
    }
}

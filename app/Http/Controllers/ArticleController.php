<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ArticleController extends Controller
{
    public function show(string $id)
    {
        // Support both numeric ID and slug
        $article = is_numeric($id)
            ? Article::where('id', $id)->where('status', 'published')->firstOrFail()
            : Article::where('slug', $id)->where('status', 'published')->firstOrFail();

        $related = Article::where('category', $article->category)
            ->where('id', '!=', $article->id)
            ->where('status', 'published')
            ->latest()
            ->take(3)
            ->get();

        $comments = $article->comments()->whereNull('parent_id')->with('replies')->get();

        return view('pages.article', compact('article', 'related', 'comments'));
    }

    public function like(Request $request, string $id)
    {
        $article = Article::findOrFail($id);
        $article->increment('likes');
        return response()->json(['likes' => $article->likes]);
    }

    public function comment(Request $request, string $id)
    {
        $body = trim((string) $request->input('body', ''));
        $parentId = $request->input('parent_id');

        $article = Article::findOrFail($id);
        $user = Auth::user();

        if (!$user) {
            return back()->withErrors(['body' => 'Anda harus login terlebih dahulu untuk memberi komentar.']);
        }

        if ($body === '') {
            return back()->withErrors(['body' => 'Komentar tidak boleh kosong.']);
        }

        if ($body !== strip_tags($body) && strlen($body) > 1000) {
            return back()->withErrors(['body' => 'Komentar terlalu panjang.']);
        }

        $parentComment = null;
        if (!empty($parentId)) {
            $parentComment = Comment::where('article_id', $article->id)->find($parentId);
            if (!$parentComment) {
                return back()->withErrors(['body' => 'Komentar yang ingin dibalas tidak ditemukan.']);
            }
        }

        Comment::create([
            'article_id'  => $article->id,
            'user_id'     => $user->id,
            'parent_id'   => $parentComment?->id,
            'author_name' => $user->display_name ?? 'Anonim',
            'avatar_color' => $user->avatar_color ?? 'from-orange-500 to-amber-400',
            'body'        => $body,
        ]);

        return back()->with('success', 'Komentar berhasil dikirim!');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Article;

class CategoryController extends Controller
{
    public function show(string $category)
    {
        $valid = ['games', 'musik', 'film', 'entertainment'];
        if (!in_array($category, $valid)) {
            abort(404, 'Kategori tidak ditemukan');
        }

        $articles = Article::where('category', $category)->where('status', 'published')->latest()->get();
        $meta     = Article::$categoryMeta[$category] ?? [];

        return view('pages.category', compact('category', 'articles', 'meta'));
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\View\View;

/**
 * Controller untuk halaman artikel per kategori.
 */
class CategoryController extends Controller
{
    /**
     * Menampilkan daftar artikel dalam satu kategori.
     *
     * Hanya artikel berstatus `published` yang ditampilkan, diurutkan dari
     * yang terbaru. Kategori yang tidak dikenal akan mengembalikan 404.
     *
     * @param string $category Slug kategori ('games', 'musik', 'film', 'entertainment').
     *
     * @return View
     */
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

<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

/**
 * Controller untuk halaman utama portal.
 */
class HomeController extends Controller
{
    /**
     * Menampilkan halaman beranda.
     *
     * Menyiapkan data untuk tampilan:
     *  - `featured`   : maksimal 6 artikel unggulan (`featured = true`).
     *  - `latest`     : 10 artikel terbaru.
     *  - `byCategory` : maksimal 3 artikel per kategori (games, musik, film, entertainment).
     *
     * Semua kueri hanya mengambil artikel dengan status `published`.
     * Pengecekan `Schema::hasTable('articles')` mencegah error saat aplikasi
     * dibuka sebelum migrasi dijalankan.
     *
     * @return View
     */
    public function index()
    {
        $categories = ['games', 'musik', 'film', 'entertainment'];
        $featured   = collect();
        $latest     = collect();
        $byCategory = [];

        if (Schema::hasTable('articles')) {
            $visibleArticles = Article::where('status', 'published')->latest();

            $featured = (clone $visibleArticles)->where('featured', true)->take(6)->get();
            $latest   = (clone $visibleArticles)->take(10)->get();

            foreach ($categories as $cat) {
                $byCategory[$cat] = (clone $visibleArticles)->where('category', $cat)->take(3)->get();
            }
        } else {
            foreach ($categories as $cat) {
                $byCategory[$cat] = collect();
            }
        }

        return view('pages.home', compact('featured', 'latest', 'byCategory', 'categories'));
    }
}

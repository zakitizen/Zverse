<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Controller untuk pencarian artikel di portal.
 */
class SearchController extends Controller
{
    /**
     * Menampilkan hasil pencarian artikel.
     *
     * Kata kunci dicocokkan pada judul, ringkasan (excerpt), atau tag artikel.
     * Hanya artikel berstatus `published` yang ikut hasil pencarian. Jika
     * kata kunci kosong, hasilnya adalah koleksi kosong.
     *
     * @param Request $request Memuat parameter query `q`.
     *
     * @return View
     */
    public function index(Request $request)
    {
        $query   = $request->get('q', '');
        $results = collect();

        if (trim($query)) {
            $results = Article::where('status', 'published')
                ->where(function ($q) use ($query) {
                    $q->where('title', 'like', "%{$query}%")
                      ->orWhere('excerpt', 'like', "%{$query}%")
                      ->orWhereJsonContains('tags', $query);
                })
                ->latest()
                ->get();
        }

        return view('pages.search', compact('query', 'results'));
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Support\Facades\Schema;

class HomeController extends Controller
{
    public function index()
    {
        $categories = ['games', 'musik', 'film', 'entertainment'];
        $featured = collect();
        $latest = collect();
        $byCategory = [];

        if (Schema::hasTable('articles')) {
            $visibleArticles = Article::whereIn('status', ['published'])->latest();

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

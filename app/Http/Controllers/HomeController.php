<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $visibleArticles = Article::whereIn('status', ['published'])->latest();

        $featured = (clone $visibleArticles)->where('featured', true)->take(6)->get();
        $latest   = (clone $visibleArticles)->take(10)->get();

        $categories = ['games', 'musik', 'film', 'entertainment'];
        $byCategory = [];
        foreach ($categories as $cat) {
            $byCategory[$cat] = (clone $visibleArticles)->where('category', $cat)->take(3)->get();
        }

        return view('pages.home', compact('featured', 'latest', 'byCategory', 'categories'));
    }
}

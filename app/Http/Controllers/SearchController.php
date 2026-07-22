<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $query   = $request->get('q', '');
        $results = collect();

        if (trim($query)) {
            $results = Article::where('status', 'published')->where(function ($q) use ($query) {
                $q->where('title', 'like', "%{$query}%")
                  ->orWhere('excerpt', 'like', "%{$query}%")
                  ->orWhereJsonContains('tags', $query);
            })->latest()->get();
        }

        return view('pages.search', compact('query', 'results'));
    }
}

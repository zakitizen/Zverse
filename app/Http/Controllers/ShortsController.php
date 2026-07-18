<?php

namespace App\Http\Controllers;

use App\Models\Short;
use Illuminate\Http\Request;

class ShortsController extends Controller
{
    public function index()
    {
        $shorts = Short::latest()->get();
        return view('pages.shorts', compact('shorts'));
    }
}

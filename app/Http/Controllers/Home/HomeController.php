<?php

namespace App\Http\Controllers\Home;

use App\Http\Controllers\Controller;
use App\Models\Article;

class HomeController extends Controller
{
    public function index()
    {
        $articles = Article::published()->with(['user', 'category'])->latest('published_at')->take(3)->get();

        return view('pages.home.index', compact('articles'));
    }
}

<?php

namespace App\Http\Controllers\Home;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $portfolios = \App\Models\Portfolio::where('is_active', true)->latest()->take(6)->get();
        $articles = \App\Models\Article::published()->with(['user', 'category'])->latest('published_at')->take(3)->get();
        return view('pages.home.index', compact('portfolios', 'articles'));
    }
}

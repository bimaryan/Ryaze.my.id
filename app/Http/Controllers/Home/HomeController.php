<?php

namespace App\Http\Controllers\Home;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $portfolios = \App\Models\Portfolio::where('is_active', true)->latest()->take(6)->get();
        return view('pages.home.index', compact('portfolios'));
    }
}

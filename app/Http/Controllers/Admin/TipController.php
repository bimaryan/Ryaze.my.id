<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tip;
use Illuminate\Http\Request;

class TipController extends Controller
{
    public function index(Request $request)
    {
        $query = Tip::latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $tips = $query->paginate(15)->withQueryString();
        $totalCompleted = Tip::where('status', 'completed')->sum('amount');

        return view('pages.admin.tips.index', compact('tips', 'totalCompleted'));
    }
}

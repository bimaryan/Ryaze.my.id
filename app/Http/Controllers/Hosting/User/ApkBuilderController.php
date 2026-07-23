<?php

namespace App\Http\Controllers\Hosting\User;

use App\Http\Controllers\Controller;
use App\Models\ApkBuild;
use App\Jobs\BuildApkJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ApkBuilderController extends Controller
{
    public function index()
    {
        $builds = ApkBuild::where('user_id', Auth::id())->latest()->paginate(10);
        return view('pages.hosting.user.apk_builder.index', compact('builds'));
    }

    public function create()
    {
        return view('pages.hosting.user.apk_builder.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'app_name' => 'required|string|max:50',
            'app_url' => 'required|url',
            'package_name' => 'required|string|regex:/^[a-z][a-z0-9_]*(\.[a-z0-9_]+)+[0-9a-z_]$/i',
            'icon' => 'nullable|image|mimes:png,jpg,jpeg|max:2048'
        ]);

        $iconPath = null;
        if ($request->hasFile('icon')) {
            $iconPath = $request->file('icon')->store('apk_icons', 'public');
        }

        $build = ApkBuild::create([
            'user_id' => Auth::id(),
            'app_name' => $request->app_name,
            'app_url' => $request->app_url,
            'package_name' => strtolower($request->package_name),
            'icon_path' => $iconPath,
            'status' => 'pending'
        ]);

        BuildApkJob::dispatch($build);

        return redirect()->route('user_hosting.apk.index')->with('success', 'Pesanan kompilasi APK berhasil dibuat dan sedang diproses di background.');
    }

    public function download(ApkBuild $build)
    {
        if ($build->user_id !== Auth::id()) {
            abort(403);
        }

        if ($build->status !== 'success' || !$build->apk_path) {
            return back()->with('error', 'File APK belum tersedia atau proses build gagal.');
        }

        return Storage::disk('local')->download($build->apk_path, Str::slug($build->app_name) . '.apk');
    }

    public function log(ApkBuild $build)
    {
        if ($build->user_id !== Auth::id()) {
            abort(403);
        }

        return response()->json([
            'status' => $build->status,
            'log' => $build->log_output
        ]);
    }

    public function destroy(ApkBuild $build)
    {
        if ($build->user_id !== Auth::id()) {
            abort(403);
        }

        // Hapus file APK jika ada
        if ($build->apk_path && Storage::disk('local')->exists($build->apk_path)) {
            Storage::disk('local')->delete($build->apk_path);
        }

        // Hapus file ikon jika ada
        if ($build->icon_path && Storage::disk('public')->exists($build->icon_path)) {
            Storage::disk('public')->delete($build->icon_path);
        }

        $build->delete();

        return redirect()->route('user_hosting.apk.index')->with('success', 'Aplikasi berhasil dihapus.');
    }
}

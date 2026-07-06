<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::all()->pluck('value', 'key')->toArray();
        return view('pages.admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $data = $request->except(['_token', '_method']);
        
        // Handle file uploads
        if ($request->hasFile('site_logo')) {
            $data['site_logo'] = $request->file('site_logo')->store('settings', 'public');
        }
        
        if ($request->hasFile('site_favicon')) {
            $data['site_favicon'] = $request->file('site_favicon')->store('settings', 'public');
        }

        foreach ($data as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }

        return redirect()->back()->with('success', 'Pengaturan berhasil diperbarui!');
    }
}

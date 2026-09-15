<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ArticleCategory;
use App\Models\HostingBilling;
use App\Models\HostingProject;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::all()->pluck('value', 'key')->toArray();
        $articleCategories = ArticleCategory::orderBy('name')->get();

        return inertia('Admin/Settings', ['settings' => $settings, 'articleCategories' => $articleCategories]);
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
            Cache::forget('setting_'.$key);
        }

        // Suspend projects for inactive plans
        $inactivePlans = [];
        $plans = ['free', 'starter', 'pro', 'business'];
        foreach ($plans as $plan) {
            $key = "plan_{$plan}_active";
            if (isset($data[$key]) && $data[$key] == '0') {
                $inactivePlans[] = $plan;
            }
        }

        if (! empty($inactivePlans)) {
            $billingsToSuspend = HostingBilling::whereIn('plan', $inactivePlans)
                ->where('status', 'active')
                ->get();

            foreach ($billingsToSuspend as $billing) {
                $projects = HostingProject::where('user_id', $billing->user_id)
                    ->whereIn('status', ['active', 'building'])
                    ->get();

                foreach ($projects as $project) {
                    $project->status = 'suspended';
                    $project->save();

                    $subdomain = explode('.', $project->ryaze_domain)[0];
                    $projectDir = hosting_clients_dir()."/{$subdomain}";
                    $suspendFile = "{$projectDir}/.suspended";

                    if (is_dir($projectDir)) {
                        touch($suspendFile);
                        @chmod($suspendFile, 0660);
                    }

                    // Stop PM2 process untuk framework Node-based
                    if (in_array($project->framework, ['react', 'nextjs', 'vue', 'node'])) {
                        $pm2Name = "prod_{$project->id}";
                        exec("pm2 delete \"{$pm2Name}\" 2>/dev/null || true");

                        if (! empty($project->dev_pid)) {
                            exec("pm2 delete \"{$project->dev_pid}\" 2>/dev/null || true");
                        }

                        $project->update(['dev_pid' => null]);
                    }

                    $project->deployments()->create([
                        'status' => 'failed',
                        'build_logs' => '> SISTEM: Hosting disuspend otomatis karena paket langganan saat ini telah dinonaktifkan oleh administrator. Silakan upgrade ke paket yang tersedia.',
                    ]);
                }
            }
        }

        return redirect()->back()->with('success', 'Pengaturan berhasil diperbarui!');
    }
}

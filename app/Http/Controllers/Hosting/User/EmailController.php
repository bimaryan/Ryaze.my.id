<?php

namespace App\Http\Controllers\Hosting\User;

use App\Http\Controllers\Controller;
use App\Models\HostingEmail;
use App\Models\HostingProject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Vinkla\Hashids\Facades\Hashids;

class EmailController extends Controller
{
    public function index()
    {
        $emails = HostingEmail::where('user_id', Auth::id())->latest()->get();
        $projects = HostingProject::where('user_id', Auth::id())->get();
        
        return view('pages.hosting.user.email.index', compact('emails', 'projects'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'prefix' => 'required|string|regex:/^[a-zA-Z0-9_\.-]+$/|max:50',
            'domain' => 'required|string|max:100',
            'password' => 'required|string|min:8',
        ]);

        $fullEmail = strtolower($request->prefix . '@' . $request->domain);

        if (HostingEmail::where('email_address', $fullEmail)->exists()) {
            return back()->with('error', 'Alamat email ini sudah terdaftar.');
        }

        // Logic for actually creating the mailbox on the mail server goes here (e.g. 1Panel API / Docker exec)
        // For now, we will store it in the Ryaze database.
        
        $email = HostingEmail::create([
            'user_id' => Auth::id(),
            'email_address' => $fullEmail,
            'domain' => $request->domain,
            'password' => Crypt::encryptString($request->password),
            'quota_mb' => 100, // Default 100MB
            'status' => 'active',
        ]);

        $this->syncToMailServer($email, $request->password, 'create');

        return back()->with('success', "Akun email {$fullEmail} berhasil dibuat!");
    }

    /**
     * Sync mailbox to actual Mail Server (Poste.io API)
     */
    private function syncToMailServer(HostingEmail $email, string $rawPassword, string $action)
    {
        $url = env('POSTE_IO_URL');
        $user = env('POSTE_IO_USER');
        $pass = env('POSTE_IO_PASSWORD');

        if (!$url || !$user || !$pass) {
            \Log::warning("[MailServer Sync] Missing Poste.io credentials in .env");
            return;
        }

        $apiUrl = rtrim($url, '/') . '/admin/api/v1';

        try {
            if ($action === 'create') {
                // Pastikan domainnya sudah ada di Poste.io
                \Illuminate\Support\Facades\Http::withoutVerifying()
                    ->withBasicAuth($user, $pass)
                    ->post("{$apiUrl}/domains", [
                        'name' => $email->domain,
                    ]);

                // Buat mailbox-nya
                $response = \Illuminate\Support\Facades\Http::withoutVerifying()
                    ->withBasicAuth($user, $pass)
                    ->post("{$apiUrl}/boxes", [
                        'name' => explode('@', $email->email_address)[0],
                        'email' => $email->email_address,
                        'passwordPlaintext' => $rawPassword,
                    ]);

                if (!$response->successful()) {
                    \Log::error("[MailServer Sync Create] Failed: " . $response->body());
                }
            } elseif ($action === 'delete') {
                $response = \Illuminate\Support\Facades\Http::withoutVerifying()
                    ->withBasicAuth($user, $pass)
                    ->delete("{$apiUrl}/boxes/" . urlencode($email->email_address));

                if (!$response->successful()) {
                    \Log::error("[MailServer Sync Delete] Failed: " . $response->body());
                }
            }
        } catch (\Exception $e) {
            \Log::error("[MailServer Sync] Error: " . $e->getMessage());
        }
    }

    public function destroy($hashid)
    {
        $decoded = Hashids::decode($hashid);
        if (empty($decoded)) {
            abort(404);
        }

        $email = HostingEmail::where('user_id', Auth::id())->findOrFail($decoded[0]);
        
        $this->syncToMailServer($email, '', 'delete');
        
        $email->delete();

        return back()->with('success', 'Akun email berhasil dihapus.');
    }
}

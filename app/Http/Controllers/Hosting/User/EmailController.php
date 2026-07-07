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
        
        HostingEmail::create([
            'user_id' => Auth::id(),
            'email_address' => $fullEmail,
            'domain' => $request->domain,
            'password' => Crypt::encryptString($request->password),
            'quota_mb' => 100, // Default 100MB
            'status' => 'active',
        ]);

        return back()->with('success', "Akun email {$fullEmail} berhasil dibuat!");
    }

    public function destroy($hashid)
    {
        $decoded = Hashids::decode($hashid);
        if (empty($decoded)) {
            abort(404);
        }

        $email = HostingEmail::where('user_id', Auth::id())->findOrFail($decoded[0]);
        
        // Delete logic on mail server goes here
        
        $email->delete();

        return back()->with('success', 'Akun email berhasil dihapus.');
    }
}

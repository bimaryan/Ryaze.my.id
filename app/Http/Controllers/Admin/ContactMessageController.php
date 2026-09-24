<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use Illuminate\Http\Request;
use Vinkla\Hashids\Facades\Hashids;

class ContactMessageController extends Controller
{
    public function index(Request $request)
    {
        $query = ContactMessage::latest();

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%')
                  ->orWhere('subject', 'like', '%' . $search . '%')
                  ->orWhere('message', 'like', '%' . $search . '%');
            });
        }

        if ($request->filled('status') && $request->status === 'unread') {
            $query->where('is_read', false);
        }

        $messages = $query->paginate(10)->withQueryString();
        return view('pages.admin.contact-messages.index', compact('messages'));
    }

    public function show($hashid)
    {
        $decoded = Hashids::decode($hashid);
        if (empty($decoded)) abort(404);

        $message = ContactMessage::findOrFail($decoded[0]);

        if (!$message->is_read) {
            $message->update(['is_read' => true]);
        }

        return view('pages.admin.contact-messages.show', compact('message'));
    }

    public function destroy($hashid)
    {
        $decoded = Hashids::decode($hashid);
        if (empty($decoded)) abort(404);

        $message = ContactMessage::findOrFail($decoded[0]);
        $message->delete();

        return redirect()->route('superadmin.contact_messages.index')->with('success', 'Pesan berhasil dihapus.');
    }
}

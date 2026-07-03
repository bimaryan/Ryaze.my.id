<?php

namespace App\Http\Controllers\Hosting\Admin;

use App\Http\Controllers\Controller;
use App\Events\TicketReplyCreated;
use App\Models\Ticket;
use App\Models\TicketReply;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TicketController extends Controller
{
    public function index()
    {
        $tickets = Ticket::with('user')
            ->orderBy('status', 'asc') // Open, Answered, Closed
            ->orderBy('updated_at', 'desc')
            ->paginate(15);
            
        return view('pages.tickets.admin.index', compact('tickets'));
    }

    public function show($hashid)
    {
        $ticket = Ticket::with(['replies.user', 'user'])->findByHashidOrFail($hashid);
        
        return view('pages.tickets.admin.show', compact('ticket'));
    }

    public function reply(Request $request, $hashid)
    {
        $request->validate([
            'message' => 'nullable|string',
            'attachment' => 'nullable|image|max:2048',
        ]);

        if (!$request->message && !$request->hasFile('attachment')) {
            return back()->withErrors(['message' => 'Pesan atau gambar harus diisi.']);
        }

        $ticket = Ticket::findByHashidOrFail($hashid);

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('tickets/attachments', 'public');
        }

        $reply = TicketReply::create([
            'ticket_id' => $ticket->id,
            'user_id' => Auth::id(),
            'message' => $request->message ?? '[Gambar dilampirkan]',
            'attachment_path' => $attachmentPath,
        ]);

        // Update status to answered
        $ticket->update(['status' => 'answered']);
        
        broadcast(new TicketReplyCreated($reply));

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true]);
        }
        
        return back()->with('success', 'Balasan berhasil dikirim.');
    }

    public function close($hashid)
    {
        $ticket = Ticket::findByHashidOrFail($hashid);
        $ticket->update(['status' => 'closed']);
        
        return redirect()->route('admin_hosting.tickets.index')
            ->with('success', 'Tiket berhasil ditutup.');
    }
}

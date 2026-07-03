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
            'message' => 'required|string',
        ]);

        $ticket = Ticket::findByHashidOrFail($hashid);

        $reply = TicketReply::create([
            'ticket_id' => $ticket->id,
            'user_id' => Auth::id(),
            'message' => $request->message,
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

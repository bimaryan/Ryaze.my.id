<?php

namespace App\Http\Controllers\User;

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
        $tickets = Ticket::where('user_id', Auth::id())
            ->orderBy('updated_at', 'desc')
            ->paginate(10);
            
        return view('pages.tickets.user.index', compact('tickets'));
    }

    public function create()
    {
        return view('pages.tickets.user.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'department' => 'required|string|in:Hosting,Joki,Billing,Teknis',
            'priority' => 'required|string|in:low,medium,high',
            'message' => 'required|string',
        ]);

        $ticket = Ticket::create([
            'user_id' => Auth::id(),
            'subject' => $request->subject,
            'department' => $request->department,
            'priority' => $request->priority,
            'status' => 'open',
        ]);

        TicketReply::create([
            'ticket_id' => $ticket->id,
            'user_id' => Auth::id(),
            'message' => $request->message,
        ]);

        return redirect()->route('user_hosting.tickets.show', $ticket->hashid)
            ->with('success', 'Tiket bantuan berhasil dibuat.');
    }

    public function show($hashid)
    {
        $ticket = Ticket::with(['replies.user'])->where('user_id', Auth::id())->findByHashidOrFail($hashid);
        
        return view('pages.tickets.user.show', compact('ticket'));
    }

    public function reply(Request $request, $hashid)
    {
        $request->validate([
            'message' => 'required|string',
        ]);

        $ticket = Ticket::where('user_id', Auth::id())->findByHashidOrFail($hashid);

        $reply = TicketReply::create([
            'ticket_id' => $ticket->id,
            'user_id' => Auth::id(),
            'message' => $request->message,
        ]);

        // Ubah status jadi open lagi jika sebelumnya answered
        if ($ticket->status == 'answered') {
            $ticket->update(['status' => 'open']);
        }
        
        // Update updated_at
        $ticket->touch();

        broadcast(new TicketReplyCreated($reply));

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'Balasan berhasil dikirim.');
    }
}

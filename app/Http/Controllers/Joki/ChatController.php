<?php

namespace App\Http\Controllers\Joki;

use App\Events\MessageSent;
use App\Http\Controllers\Controller;
use App\Models\JokiOrder;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    /**
     * Fetch messages for a specific Joki Order
     */
    public function fetchMessages($hashid)
    {
        $decoded = \Vinkla\Hashids\Facades\Hashids::decode($hashid);
        if (empty($decoded)) return response()->json(['error' => 'Not found'], 404);
        $id = $decoded[0];

        $order = JokiOrder::findOrFail($id);

        // Authorization: Must be client or admin/worker
        $user = Auth::user();
        $isAdmin = in_array($user->role, ['admin_joki', 'superadmin']);
        if ($order->client_id !== $user->id && !$isAdmin && $order->worker_id !== $user->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $messages = Message::with('sender:id,name,role')->where('joki_order_id', $id)->orderBy('created_at', 'asc')->get();

        return response()->json($messages);
    }

    /**
     * Store a new message and broadcast
     */
    public function sendMessage(Request $request, $hashid)
    {
        $decoded = \Vinkla\Hashids\Facades\Hashids::decode($hashid);
        if (empty($decoded)) return response()->json(['error' => 'Not found'], 404);
        $id = $decoded[0];

        $order = JokiOrder::findOrFail($id);
        $user = Auth::user();

        // Authorization
        $isAdmin = in_array($user->role, ['admin_joki', 'superadmin']);
        if ($order->client_id !== $user->id && !$isAdmin && $order->worker_id !== $user->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $request->validate([
            'message' => 'required|string|max:1000'
        ]);

        // Determine receiver
        $receiverId = null;
        if ($user->id === $order->client_id) {
            $receiverId = $order->worker_id; // Could be null if not picked up yet
        } else {
            $receiverId = $order->client_id;
        }

        $message = Message::create([
            'joki_order_id' => $id,
            'sender_id' => $user->id,
            'receiver_id' => $receiverId,
            'message' => $request->message,
        ]);

        $message->load('sender');

        // Broadcast Event
        broadcast(new MessageSent($message))->toOthers();

        return response()->json([
            'status' => 'success',
            'message' => $message
        ]);
    }
}

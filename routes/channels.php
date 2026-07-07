<?php

use Illuminate\Support\Facades\Broadcast;
use App\Models\Ticket;
use App\Models\JokiOrder;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('ticket.{hashid}', function ($user, $hashid) {
    $ticket = Ticket::findByHashidOrFail($hashid);

    // Admin can access all tickets
    if (in_array($user->role, ['admin_hosting', 'superadmin'])) {
        return true;
    }

    // Normal user can only access their own ticket
    return (int) $user->id === (int) $ticket->user_id;
});

Broadcast::channel('chat.joki_order.{id}', function ($user, $id) {
    $order = JokiOrder::find($id);
    if (!$order) return false;

    if (in_array($user->role, ['admin_joki', 'superadmin'])) {
        return true;
    }

    return (int) $user->id === (int) $order->client_id || (int) $user->id === (int) $order->worker_id;
});

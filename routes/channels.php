<?php

use Illuminate\Support\Facades\Broadcast;
use App\Models\Ticket;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('ticket.{hashid}', function ($user, $hashid) {
    $ticket = Ticket::findByHashid($hashid);
    if (!$ticket) return false;

    // Admin can access all tickets
    if (in_array($user->role, ['admin_hosting', 'superadmin'])) {
        return true;
    }

    // Normal user can only access their own ticket
    return (int) $user->id === (int) $ticket->user_id;
});

<?php

namespace App\Events;

use App\Models\TicketReply;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TicketReplyCreated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $reply;

    /**
     * Create a new event instance.
     */
    public function __construct(TicketReply $reply)
    {
        $this->reply = $reply->load('user');
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('ticket.' . $this->reply->ticket->hashid),
        ];
    }



    /**
     * Get the data to broadcast.
     *
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        $isAdmin = in_array($this->reply->user->role, ['admin_hosting', 'superadmin']);
        
        return [
            'id' => $this->reply->id,
            'ticket_id' => $this->reply->ticket_id,
            'user_id' => $this->reply->user_id,
            'user_name' => $this->reply->user->name,
            'user_role' => $this->reply->user->role,
            'is_admin' => $isAdmin,
            'message' => $this->reply->message,
            'attachment_url' => $this->reply->attachment_path ? asset('storage/' . $this->reply->attachment_path) : null,
            'created_at' => $this->reply->created_at->format('d M Y, H:i'),
        ];
    }
}

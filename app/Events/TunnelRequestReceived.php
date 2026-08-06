<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TunnelRequestReceived implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public string $subdomain;
    public string $requestId;
    public string $method;

    /**
     * Create a new event instance.
     *
     * Catatan keamanan: request di-broadcast hanya berisi metadata (subdomain, requestId, method).
     * Header/body request disimpan di cache dan diambil client via endpoint /api/tunnel/fetch
     * yang dilindungi secret per-tunnel, sehingga pengintai channel WebSocket publik
     * tidak bisa membaca cookie, token, atau body request.
     */
    public function __construct(string $subdomain, string $requestId, string $method)
    {
        $this->subdomain = $subdomain;
        $this->requestId = $requestId;
        $this->method = $method;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, Channel>
     */
    public function broadcastOn(): array
    {
        // For simplicity, we can use a public channel first, or private channel if auth is setup.
        // Public channel is easier to test without authentication setup first.
        return [
            new Channel('tunnel.' . $this->subdomain),
        ];
    }
}

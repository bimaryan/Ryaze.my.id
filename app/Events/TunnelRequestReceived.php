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
    public string $path;
    public array $headers;
    public $body;

    /**
     * Create a new event instance.
     */
    public function __construct(string $subdomain, string $requestId, string $method, string $path, array $headers, $body)
    {
        $this->subdomain = $subdomain;
        $this->requestId = $requestId;
        $this->method = $method;
        $this->path = $path;
        $this->headers = $headers;
        $this->body = $body;
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

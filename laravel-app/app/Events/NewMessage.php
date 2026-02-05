<?php

namespace App\Events;

use App\Models\Message; // <-- Update this
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewMessage implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;

    // Accept Message model instead of Chat
    public function __construct(Message $message)
    {
        $this->message = $message;
    }

    public function broadcastOn()
    {
        // Broadcast on private channel for the receiver
        return new PrivateChannel('chat.' . $this->message->receiver_id);
    }

    public function broadcastWith()
    {
        return [
            'sender_id' => $this->message->sender_id,
            'receiver_id' => $this->message->receiver_id,
            'message' => $this->message->message,
            'created_at' => $this->message->created_at->toDateTimeString(),
        ];
    }
}

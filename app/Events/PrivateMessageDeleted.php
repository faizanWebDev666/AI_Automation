<?php

namespace App\Events;

use App\Models\Message;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PrivateMessageDeleted implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public int $id;
    public int $sender_id;
    public int $receiver_id;
    public string $timestamp;

    public function __construct(Message $msg)
    {
        $this->id = $msg->id;
        $this->sender_id = $msg->sender_id;
        $this->receiver_id = $msg->receiver_id;
        $this->timestamp = $msg->created_at->toDateTimeString();
    }

    public function broadcastOn(): PrivateChannel
    {
        // Notify the other party only.
        return new PrivateChannel('chat.' . $this->receiver_id);
    }

    public function broadcastAs(): string
    {
        return 'message.deleted';
    }
}


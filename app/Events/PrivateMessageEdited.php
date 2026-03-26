<?php

namespace App\Events;

use App\Models\Message;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PrivateMessageEdited implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public int $id;
    public int $sender_id;
    public string $sender_name;
    public int $receiver_id;
    public string $message;
    public string $type;
    public ?string $file_url;
    public ?string $edited_at;
    public string $timestamp;

    public function __construct(Message $msg)
    {
        $this->id = $msg->id;
        $this->sender_id = $msg->sender_id;
        $this->sender_name = $msg->sender?->name ?? '';
        $this->receiver_id = $msg->receiver_id;
        $this->message = $msg->message;
        $this->type = $msg->type ?? 'text';
        $this->file_url = $msg->file_path ? asset('storage/' . $msg->file_path) : null;
        $this->edited_at = $msg->edited_at?->toDateTimeString();
        $this->timestamp = $msg->created_at->toDateTimeString();
    }

    public function broadcastOn(): PrivateChannel
    {
        // Notify the other party only.
        return new PrivateChannel('chat.' . $this->receiver_id);
    }

    public function broadcastAs(): string
    {
        return 'message.edited';
    }
}


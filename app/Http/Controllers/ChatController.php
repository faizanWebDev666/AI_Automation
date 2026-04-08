<?php

namespace App\Http\Controllers;

use App\Events\PrivateMessageSent;
use App\Events\PrivateMessageDeleted;
use App\Events\PrivateMessageEdited;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ChatController extends Controller
{
    public function index(Request $request)
    {
        $property = null;
        if ($request->has('property')) {
            $property = \App\Models\Property::with('images')->find($request->property);
        }

        return view('chat', [
            'authUser' => Auth::user(),
            'targetUserId' => $request->query('user'),
            'property' => $property,
            'propertyContext' => $property ? [
                'id' => $property->id,
                'title' => $property->title,
                'price' => number_format($property->price),
                'location' => "{$property->area_name}, {$property->city}",
                'type' => ucfirst($property->property_type),
                'specs' => ($property->bedrooms ? "{$property->bedrooms} Bed " : '') . ($property->bathrooms ? "• {$property->bathrooms} Bath " : '') . "• {$property->area_marla} Marla",
                'url' => route('properties.show', $property->id)
            ] : null
        ]);
    }

    public function contacts()
    {
        $me = Auth::id();
        $users = User::where('id', '!=', $me)->orderBy('name')->get();

        $contacts = $users->map(function ($user) use ($me) {
            $lastMessage = Message::where(function ($q) use ($me, $user) {
                $q->where('sender_id', $me)->where('receiver_id', $user->id);
            })->orWhere(function ($q) use ($me, $user) {
                $q->where('sender_id', $user->id)->where('receiver_id', $me);
            })->orderByDesc('created_at')->first();

            $unread = Message::where('sender_id', $user->id)
                ->where('receiver_id', $me)
                ->where('is_read', false)
                ->count();

            $preview = $lastMessage
                ? ($lastMessage->type === 'image' ? '📷 Photo' : $lastMessage->message)
                : null;

            return [
                'id'           => $user->id,
                'name'         => $user->name,
                'email'        => $user->email,
                'last_message' => $preview,
                'last_time'    => $lastMessage?->created_at?->toDateTimeString(),
                'unread'       => $unread,
            ];
        });

        $sorted = $contacts->sortByDesc(fn($c) => $c['last_time'] ?? '0000-00-00')->values();

        return response()->json($sorted);
    }

    public function messages(User $user)
    {
        $me = Auth::id();

        Message::where('sender_id', $user->id)
            ->where('receiver_id', $me)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        $messages = Message::where(function ($q) use ($me, $user) {
            $q->where('sender_id', $me)->where('receiver_id', $user->id);
        })->orWhere(function ($q) use ($me, $user) {
            $q->where('sender_id', $user->id)->where('receiver_id', $me);
        })->orderBy('created_at')->get();

        $latestPropertyContext = null;
        $latestPropertyMsg = $messages->whereNotNull('property_id')->last();
        if ($latestPropertyMsg) {
            $prop = \App\Models\Property::with('images')->find($latestPropertyMsg->property_id);
            if ($prop) {
                $latestPropertyContext = [
                    'id' => $prop->id,
                    'title' => $prop->title,
                    'price' => number_format($prop->price),
                    'location' => "{$prop->area_name}, {$prop->city}",
                    'type' => ucfirst($prop->property_type),
                    'specs' => ($prop->bedrooms ? "{$prop->bedrooms} Bed " : '') . ($prop->bathrooms ? "• {$prop->bathrooms} Bath " : '') . "• {$prop->area_marla} Marla",
                    'url' => route('properties.show', $prop->id),
                    'image' => $prop->images->first() ? asset('storage/' . $prop->images->first()->image_path) : 'https://images.unsplash.com/photo-1560518883-ce09059eeffa?ixlib=rb-4.0.3&auto=format&fit=crop&w=100&q=80'
                ];
            }
        }

        return response()->json([
            'messages' => $messages->map(function ($m) {
                $editedAt = $m->edited_at ? $m->edited_at->toDateTimeString() : null;
                return [
                    'id'          => $m->id,
                    'sender_id'   => $m->sender_id,
                    'receiver_id' => $m->receiver_id,
                    'property_id' => $m->property_id,
                    'message'     => $m->message,
                    'type'        => $m->type ?? 'text',
                    'file_url'    => $m->file_path ? asset('storage/' . $m->file_path) : null,
                    'is_read'     => $m->is_read,
                    'timestamp'   => $m->created_at->toDateTimeString(),
                    'edited_at'   => $editedAt,
                    'reply_to_message_id' => $m->reply_to_message_id,
                    'reply_to_message'    => $m->reply_to_message,
                    'forwarded_from_message_id' => $m->forwarded_from_message_id,
                ];
            }),
            'latest_property_context' => $latestPropertyContext
        ]);
    }

    private function validateConversationMessagePair(int $meId, int $otherUserId, Message $msg): void
    {
        $ok = ($msg->sender_id === $meId && $msg->receiver_id === $otherUserId)
            || ($msg->sender_id === $otherUserId && $msg->receiver_id === $meId);

        if (! $ok) abort(403, 'Invalid conversation message reference.');
    }

    private function buildReplyPreview(Message $msg): string
    {
        return ($msg->type === 'image') ? '📷 Photo' : (string) $msg->message;
    }

    /**
     * Send a text message.
     */
    public function send(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'message'     => 'required|string|max:2000',
            'reply_to_message_id' => 'nullable|exists:messages,id',
            'property_id' => 'nullable|exists:properties,id',
        ]);

        $meId = (int) Auth::id();
        $receiverId = (int) $request->receiver_id;

        $replyToMessageId = null;
        $replyToMessage = null;

        if ($request->filled('reply_to_message_id')) {
            $replyMsg = Message::findOrFail($request->reply_to_message_id);
            $this->validateConversationMessagePair($meId, $receiverId, $replyMsg);

            $replyToMessageId = $replyMsg->id;
            $replyToMessage = $this->buildReplyPreview($replyMsg);
        }

        $msg = Message::create([
            'sender_id'   => $meId,
            'receiver_id' => $receiverId,
            'property_id' => $request->property_id,
            'message'     => $request->message,
            'type'        => 'text',
            'reply_to_message_id' => $replyToMessageId,
            'reply_to_message'    => $replyToMessage,
        ]);

        try {
            $context = null;
            if ($msg->property_id) {
                $prop = \App\Models\Property::with('images')->find($msg->property_id);
                if ($prop) {
                    $context = [
                        'id' => $prop->id,
                        'title' => $prop->title,
                        'price' => number_format($prop->price),
                        'location' => "{$prop->area_name}, {$prop->city}",
                        'type' => ucfirst($prop->property_type),
                        'specs' => ($prop->bedrooms ? "{$prop->bedrooms} Bed " : '') . ($prop->bathrooms ? "• {$prop->bathrooms} Bath " : '') . "• {$prop->area_marla} Marla",
                        'url' => route('properties.show', $prop->id),
                        'image' => $prop->images->first() ? asset('storage/' . $prop->images->first()->image_path) : 'https://images.unsplash.com/photo-1560518883-ce09059eeffa?ixlib=rb-4.0.3&auto=format&fit=crop&w=100&q=80'
                    ];
                }
            }
            broadcast(new PrivateMessageSent($msg, Auth::user()->name, $context))->toOthers();
        } catch (\Exception $e) {
            // Broadcasting disabled or failed, continue anyway
            \Log::info('Broadcasting failed silently: ' . $e->getMessage());
        }

        return response()->json([
            'id'          => $msg->id,
            'sender_id'   => $msg->sender_id,
            'receiver_id' => $msg->receiver_id,
            'message'     => $msg->message,
            'type'        => 'text',
            'file_url'    => null,
            'timestamp'   => $msg->created_at->toDateTimeString(),
            'edited_at'   => null,
            'reply_to_message_id' => $msg->reply_to_message_id,
            'reply_to_message'    => $msg->reply_to_message,
            'forwarded_from_message_id' => $msg->forwarded_from_message_id,
        ]);
    }

    /**
     * Send an image message.
     */
    public function sendImage(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'image'       => 'required|image|mimes:jpg,jpeg,png,gif,webp|max:5120',
            'reply_to_message_id' => 'nullable|exists:messages,id',
        ]);

        $meId = (int) Auth::id();
        $receiverId = (int) $request->receiver_id;

        $replyToMessageId = null;
        $replyToMessage = null;
        if ($request->filled('reply_to_message_id')) {
            $replyMsg = Message::findOrFail($request->reply_to_message_id);
            $this->validateConversationMessagePair($meId, $receiverId, $replyMsg);
            $replyToMessageId = $replyMsg->id;
            $replyToMessage = $this->buildReplyPreview($replyMsg);
        }

        $path = $request->file('image')->store('chat-images', 'public');

        $msg = Message::create([
            'sender_id'   => $meId,
            'receiver_id' => $receiverId,
            'message'     => '📷 Photo',
            'type'        => 'image',
            'file_path'   => $path,
            'reply_to_message_id' => $replyToMessageId,
            'reply_to_message'    => $replyToMessage,
        ]);

        try {
            broadcast(new PrivateMessageSent($msg, Auth::user()->name))->toOthers();
        } catch (\Exception $e) {
            \Log::info('Broadcasting failed silently: ' . $e->getMessage());
        }

        return response()->json([
            'id'          => $msg->id,
            'sender_id'   => $msg->sender_id,
            'receiver_id' => $msg->receiver_id,
            'message'     => $msg->message,
            'type'        => 'image',
            'file_url'    => asset('storage/' . $path),
            'timestamp'   => $msg->created_at->toDateTimeString(),
            'edited_at'   => null,
            'reply_to_message_id' => $msg->reply_to_message_id,
            'reply_to_message'    => $msg->reply_to_message,
            'forwarded_from_message_id' => $msg->forwarded_from_message_id,
        ]);
    }

    /**
     * Edit an existing message (text messages only).
     */
    public function edit(Request $request)
    {
        $request->validate([
            'message_id' => 'required|exists:messages,id',
            'message'    => 'required|string|max:2000',
        ]);

        $meId = (int) Auth::id();
        $msg = Message::where('id', $request->message_id)
            ->where('sender_id', $meId)
            ->firstOrFail();

        if (($msg->type ?? 'text') !== 'text') {
            return response()->json(['message' => 'Only text messages can be edited.'], 422);
        }

        $msg->message = $request->message;
        $msg->edited_at = now();
        $msg->save();

        try {
            broadcast(new PrivateMessageEdited($msg))->toOthers();
        } catch (\Exception $e) {
            \Log::info('Broadcasting failed silently: ' . $e->getMessage());
        }

        return response()->json([
            'id'          => $msg->id,
            'sender_id'   => $msg->sender_id,
            'receiver_id' => $msg->receiver_id,
            'message'     => $msg->message,
            'type'        => 'text',
            'file_url'    => null,
            'timestamp'   => $msg->created_at->toDateTimeString(),
            'edited_at'   => $msg->edited_at?->toDateTimeString(),
            'reply_to_message_id' => $msg->reply_to_message_id,
            'reply_to_message'    => $msg->reply_to_message,
            'forwarded_from_message_id' => $msg->forwarded_from_message_id,
        ]);
    }

    /**
     * Delete an existing message (hard delete).
     */
    public function delete(Message $message)
    {
        $meId = (int) Auth::id();
        if ((int) $message->sender_id !== $meId) abort(403, 'You can only delete your own messages.');

        // Broadcast before deletion so event payload can access message fields.
        try {
            broadcast(new PrivateMessageDeleted($message))->toOthers();
        } catch (\Exception $e) {
            \Log::info('Broadcasting failed silently: ' . $e->getMessage());
        }

        if (($message->type ?? 'text') === 'image' && $message->file_path) {
            try {
                Storage::disk('public')->delete($message->file_path);
            } catch (\Throwable $e) {
                // If file deletion fails, don't block message deletion.
            }
        }

        $message->delete();

        return response()->json(['deleted' => true, 'id' => $message->id]);
    }

    /**
     * Forward an existing message to a different user.
     */
    public function forward(Request $request)
    {
        $request->validate([
            'message_id'  => 'required|exists:messages,id',
            'receiver_id' => 'required|exists:users,id',
        ]);

        $meId = (int) Auth::id();
        $destId = (int) $request->receiver_id;

        $orig = Message::findOrFail($request->message_id);

        // Only allow forwarding messages that the current user can access.
        if (!($orig->sender_id === $meId || $orig->receiver_id === $meId)) {
            abort(403, 'You can only forward messages from your conversations.');
        }

        $forward = Message::create([
            'sender_id' => $meId,
            'receiver_id' => $destId,
            'message' => $orig->message,
            'type' => $orig->type ?? 'text',
            'file_path' => $orig->file_path,
            'forwarded_from_message_id' => $orig->id,
            'reply_to_message_id' => $orig->reply_to_message_id,
            'reply_to_message' => $orig->reply_to_message,
        ]);

        try {
            broadcast(new PrivateMessageSent($forward, Auth::user()->name))->toOthers();
        } catch (\Exception $e) {
            \Log::info('Broadcasting failed silently: ' . $e->getMessage());
        }

        return response()->json([
            'id' => $forward->id,
            'sender_id' => $forward->sender_id,
            'receiver_id' => $forward->receiver_id,
            'message' => $forward->message,
            'type' => $forward->type ?? 'text',
            'file_url' => $forward->file_path ? asset('storage/' . $forward->file_path) : null,
            'timestamp' => $forward->created_at->toDateTimeString(),
            'edited_at' => null,
            'reply_to_message_id' => $forward->reply_to_message_id,
            'reply_to_message' => $forward->reply_to_message,
            'forwarded_from_message_id' => $forward->forwarded_from_message_id,
        ]);
    }
}

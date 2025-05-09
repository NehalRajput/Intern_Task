<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
use App\Models\Message;
use App\Models\User;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    public function index()
    {
        $users = $this->getAvailableUsers();
        return view('chat.index', compact('users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'content' => 'required|string',
        ]);

        try {
            $message = Message::create([
                'content' => $request->content,
                'sender_type' => get_class(Auth::user()),
                'sender_id' => Auth::id(),
                'receiver_type' => User::class,
                'receiver_id' => $request->receiver_id,
            ]);

            try {
                broadcast(new MessageSent($message))->toOthers();
            } catch (\Exception $e) {
                \Log::error('Broadcasting failed: ' . $e->getMessage());
                // Return success with message even if broadcasting fails
                return response()->json([
                    'message' => $message,
                    'warning' => 'Message saved but real-time update failed'
                ]);
            }

            return response()->json(['message' => $message]);
        } catch (\Exception $e) {
            \Log::error('Message creation failed: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to save message'], 500);
        }
    }

    public function getMessages(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $messages = Message::where(function($query) use ($request) {
            $query->where([
                'sender_id' => Auth::id(),
                'receiver_id' => $request->user_id,
            ])->orWhere([
                'sender_id' => $request->user_id,
                'receiver_id' => Auth::id(),
            ]);
        })
        ->orderBy('created_at', 'asc')
        ->get();

        return response()->json(['messages' => $messages]);
    }

    public function markAsRead(Message $message)
    {
        if ($message->receiver_id === Auth::id()) {
            $message->update(['read_at' => now()]);
        }

        return response()->json(['success' => true]);
    }

    public function markAllAsRead($userId)
    {
        Message::where([
            'sender_id' => $userId,
            'receiver_id' => Auth::id(),
            'read_at' => null,
        ])->update(['read_at' => now()]);

        return response()->json(['success' => true]);
    }

    public function getUsers()
    {
        $users = $this->getAvailableUsers();
        return response()->json(['users' => $users]);
    }

    private function getAvailableUsers()
    {
        if (Auth::user() instanceof Admin) {
            // If admin, get all interns (users)
            return User::withCount(['receivedMessages' => function($query) {
                $query->whereNull('read_at');
            }])
            ->orderBy('name')
            ->get();
        } else {
            // If intern, get all admins
            return Admin::withCount(['receivedMessages' => function($query) {
                $query->whereNull('read_at');
            }])
            ->orderBy('name')
            ->get();
        }
    }
} 
<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $messages = Message::where('receiver_id', $user->id)
            ->orWhere('sender_id', $user->id)
            ->whereNull('parent_id')
            ->with(['sender', 'receiver', 'replies.sender'])
            ->latest()
            ->get();

        $interns = [];
        if ($user->role === 'admin') {
            $interns = User::where('role', 'intern')->get();
        }

        return view('messages.index', compact('messages', 'interns'));
    }

    public function show(Message $message)
    {
        $message->load(['sender', 'receiver', 'replies.sender']);
        return view('messages.show', compact('message'));
    }

    public function create()
    {
        $interns = User::where('role', 'intern')->get();
        return view('messages.create', compact('interns'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'content' => 'required|string',
            'receiver_id' => 'required|exists:users,id',
            'parent_id' => 'nullable|exists:messages,id'
        ]);

        $message = Message::create([
            'content' => $request->content,
            'sender_id' => Auth::id(),
            'receiver_id' => $request->receiver_id,
            'parent_id' => $request->parent_id
        ]);

        return redirect()->route('messages.index')
            ->with('success', 'Message sent successfully');
    }

    public function update(Request $request, Message $message)
    {
        // Only allow the sender to edit their message
        if ($message->sender_id !== Auth::id()) {
            return redirect()->back()->with('error', 'You can only edit your own messages');
        }

        $request->validate([
            'content' => 'required|string'
        ]);

        $message->update([
            'content' => $request->content
        ]);

        return redirect()->back()->with('success', 'Message updated successfully');
    }

    public function destroy(Message $message)
    {
        // Only allow the sender to delete their message
        if ($message->sender_id !== Auth::id()) {
            return redirect()->back()->with('error', 'You can only delete your own messages');
        }

        // Delete all replies to this message
        $message->replies()->delete();
        
        // Delete the message
        $message->delete();

        return redirect()->route('messages.index')
            ->with('success', 'Message deleted successfully');
    }

    public function markAsRead(Message $message)
    {
        $message->update(['is_read' => true]);
        return response()->json(['success' => true]);
    }
} 
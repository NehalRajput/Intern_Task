<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    public function store(Request $request, Task $task)
    {
        $request->validate([
            'content' => 'required|string',
            'is_query' => 'boolean'
        ]);

        $task->comments()->create([
            'content' => $request->content,
            'user_id' => Auth::id(),
            'is_query' => $request->is_query ?? false
        ]);

        return redirect()->back()->with('success', 'Comment added successfully');
    }

    public function index(Task $task)
    {
        $comments = $task->comments()->with('user')->latest()->get();
        return response()->json($comments);
    }

    public function update(Request $request, Comment $comment)
    {
        if (Auth::user()->role !== 'admin') {
            return redirect()->back()->with('error', 'Unauthorized');
        }

        $request->validate([
            'content' => 'required|string'
        ]);

        $comment->update([
            'content' => $request->content
        ]);

        return redirect()->back()->with('success', 'Comment updated successfully');
    }

    public function destroy(Comment $comment)
    {
        if (Auth::user()->role !== 'admin') {
            return redirect()->back()->with('error', 'Unauthorized');
        }

        $comment->delete();
        return redirect()->back()->with('success', 'Comment deleted successfully');
    }
}

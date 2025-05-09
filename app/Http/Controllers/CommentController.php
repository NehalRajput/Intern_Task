<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class CommentController extends Controller
{
    public function store(Request $request, Task $task)
    {
        $request->validate([
            'content' => 'required|string',
            'is_query' => 'boolean'
        ]);

        $comment = new Comment([
            'content' => $request->content,
            'is_query' => $request->boolean('is_query', false),
            'user_id' => Auth::id()
        ]);

        $task->comments()->save($comment);

        return back()->with('success', 'Comment added successfully');
    }

    public function index(Task $task)
    {
        try {
            $comments = $task->comments()->with('user')->latest()->get();
            return response()->json($comments);
        } catch (\Exception $e) {
            Log::error('Failed to fetch comments', [
                'error' => $e->getMessage(),
                'task_id' => $task->id
            ]);
            return response()->json(['error' => 'Failed to fetch comments.'], 500);
        }
    }

    public function update(Request $request, Comment $comment)
    {
        if (!Auth::guard('admin')->check()) {
            return redirect()->back()->with('error', 'Unauthorized');
        }

        try {
            $request->validate([
                'content' => 'required|string'
            ]);

            $comment->update([
                'content' => $request->content
            ]);

            return redirect()->back()->with('success', 'Comment updated successfully');
        } catch (\Exception $e) {
            Log::error('Failed to update comment', [
                'error' => $e->getMessage(),
                'comment_id' => $comment->id
            ]);
            return redirect()->back()->with('error', 'Failed to update comment. Please try again.');
        }
    }

    public function destroy(Comment $comment)
    {
        if (!Auth::guard('admin')->check()) {
            return redirect()->back()->with('error', 'Unauthorized');
        }

        try {
            $comment->delete();
            return redirect()->back()->with('success', 'Comment deleted successfully');
        } catch (\Exception $e) {
            Log::error('Failed to delete comment', [
                'error' => $e->getMessage(),
                'comment_id' => $comment->id
            ]);
            return redirect()->back()->with('error', 'Failed to delete comment. Please try again.');
        }
    }
}

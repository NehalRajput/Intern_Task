<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Task;
use Illuminate\Support\Facades\Auth;

class InternController extends Controller
{
    public function tasks()
    {
        $tasks = Auth::user()->tasks;
        return view('intern.tasks', compact('tasks'));
    }

    public function updateTaskStatus(Request $request, Task $task)
    {
        $request->validate([
            'status' => 'required|in:pending,todo,completed'
        ]);

        // Check if the task is assigned to the current intern
        if (!$task->interns->contains(Auth::id())) {
            return redirect()->back()->with('error', 'Unauthorized to update this task');
        }

        $task->update([
            'status' => $request->status
        ]);

        return redirect()->back()->with('success', 'Task status updated successfully');
    }
}
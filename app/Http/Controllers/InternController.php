<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Task;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class InternController extends Controller
{
    public function tasks()
    {
        try {
            $tasks = Auth::user()->tasks;
            return view('intern.tasks', compact('tasks'));
        } catch (\Exception $e) {
            Log::error('Failed to fetch tasks for intern', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);
            return redirect()->back()->with('error', 'Failed to load tasks. Please try again later.');
        }
    }

    public function updateTaskStatus(Request $request, Task $task)
    {
        try {
            $request->validate([
                'status' => 'required|in:pending,todo,completed'
            ]);

            if (!$task->interns->contains(Auth::id())) {
                return redirect()->back()->with('error', 'Unauthorized to update this task');
            }

            $task->update([
                'status' => $request->status
            ]);

            return redirect()->back()->with('success', 'Task status updated successfully');
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::warning('Validation failed when updating task status', [
                'errors' => $e->errors(),
                'user_id' => Auth::id()
            ]);
            throw $e;
        } catch (\Exception $e) {
            Log::error('Failed to update task status', [
                'error' => $e->getMessage(),
                'task_id' => $task->id,
                'user_id' => Auth::id()
            ]);
            return redirect()->back()->with('error', 'Failed to update task status. Please try again later.');
        }
    }
}

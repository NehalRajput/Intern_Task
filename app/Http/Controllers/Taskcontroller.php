<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    public function index()
    {
        $tasks = Auth::user()->assignedTasks()
            ->with(['comments.user'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('intern.tasks', compact('tasks'));
    }

    public function create()
    {
        try {
            $interns = User::all();
            return view('Admin.Tasks.create', compact('interns'));
        } catch (\Exception $e) {
            Log::error('Failed to load create task page', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Failed to load task creation page.');
        }
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'title' => 'required',
                'description' => 'required',
                'due_date' => 'nullable|date',
                'interns' => 'array'
            ]);

            $task = Task::create([
                'title' => $request->title,
                'description' => $request->description,
                'due_date' => $request->due_date,
                'created_by' => auth()->id()
            ]);

            $task->interns()->attach($request->interns);

            return redirect()->route('admin.tasks.index')->with('success', 'Task created!');
        } catch (\Exception $e) {
            Log::error('Failed to create task', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Failed to create task.')->withInput();
        }
    }

    public function edit(Task $task)
    {
        try {
            $interns = User::all();
            $assigned = $task->interns->pluck('id')->toArray();
            return view('Admin.Tasks.edit', compact('task', 'interns', 'assigned'));
        } catch (\Exception $e) {
            Log::error('Failed to load task edit page', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Failed to load edit page.');
        }
    }

    public function update(Request $request, Task $task)
    {
        try {
            $request->validate([
                'title' => 'required',
                'description' => 'required',
                'due_date' => 'nullable|date',
                'interns' => 'array'
            ]);

            $task->update($request->only('title', 'description', 'due_date'));
            $task->interns()->sync($request->interns);

            return redirect()->route('admin.tasks.index')->with('success', 'Task updated!');
        } catch (\Exception $e) {
            Log::error('Failed to update task', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Failed to update task.')->withInput();
        }
    }

    public function assignIntern(Request $request, Task $task)
    {
        try {
            $validated = $request->validate([
                'intern_id' => 'required|exists:users,id'
            ]);

            $task->interns()->attach($validated['intern_id']);

            return redirect()->back()->with('success', 'Intern assigned successfully');
        } catch (\Exception $e) {
            Log::error('Failed to assign intern to task', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Failed to assign intern.');
        }
    }

    public function detachIntern(Request $request, Task $task, User $intern)
    {
        try {
            $task->interns()->detach($intern->id);
            return redirect()->back()->with('success', 'Intern removed from task successfully');
        } catch (\Exception $e) {
            Log::error('Failed to detach intern from task', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Failed to remove intern.');
        }
    }

    public function destroy(Task $task)
    {
        try {
            $task->interns()->detach();
            $task->delete();

            return redirect()->route('admin.tasks.index')
                ->with('success', 'Task deleted successfully');
        } catch (\Exception $e) {
            Log::error('Failed to delete task', ['error' => $e->getMessage()]);
            return redirect()->route('admin.tasks.index')
                ->with('error', 'Error deleting task');
        }
    }

    public function updateStatus(Request $request, Task $task)
    {
        $request->validate([
            'status' => 'required|in:pending,todo,completed'
        ]);

        $task->update(['status' => $request->status]);

        return back()->with('success', 'Task status updated successfully');
    }
}

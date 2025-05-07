<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\User;

class TaskController extends Controller
{
   
    public function index()
    {
        $tasks = Task::with('interns')->get();
        $interns = User::where('role', 'intern')->get();
        return view('Admin.Tasks.index', compact('tasks', 'interns'));
    }
    


    public function create()
    {
        $interns = User::where('role', 'intern')->get();
        return view('Admin.Tasks.create', compact('interns'));
    }

    public function store(Request $request)
    {
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
    
        return redirect()->route('tasks.index')->with('success', 'Task created!');
    }

    public function edit(Task $task)
    {
        $interns = User::where('role', 'intern')->get();
        $assigned = $task->interns->pluck('id')->toArray();
        return view('Admin.Tasks.edit', compact('task', 'interns', 'assigned'));
    }

    public function update(Request $request, Task $task)
    {
        $request->validate([
            'title' => 'required',
            'description' => 'required',
            'due_date' => 'nullable|date',
            'interns' => 'array'
        ]);

        $task->update($request->only('title', 'description', 'due_date'));
        $task->interns()->sync($request->interns);

        return redirect()->route('tasks.index')->with('success', 'Task updated!'); // Fixed route name
    }

   
    public function assignIntern(Request $request, Task $task)
    {
        $validated = $request->validate([
            'intern_id' => 'required|exists:users,id'
        ]);

        $task->interns()->attach($validated['intern_id']);

        return redirect()->back()->with('success', 'Intern assigned successfully');
    }

    public function detachIntern(Request $request, Task $task, User $intern)
    {
        $task->interns()->detach($intern->id);
        return redirect()->back()->with('success', 'Intern removed from task successfully');
    }

    public function destroy(Task $task)
    {
        try {
            // Delete associated records in the pivot table
            $task->interns()->detach();
            
            // Delete the task
            $task->delete();
            
            return redirect()->route('tasks.index')
                ->with('success', 'Task deleted successfully');
        } catch (\Exception $e) {
            return redirect()->route('tasks.index')
                ->with('error', 'Error deleting task');
        }
    }
}

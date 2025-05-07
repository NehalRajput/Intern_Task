<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Task;

class InternController extends Controller
{
    public function tasks()
    {
        $tasks = auth()->user()->tasks;
        return view('intern.tasks', compact('tasks'));
    }
}
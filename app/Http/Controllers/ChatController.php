<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ChatController extends Controller
{
    public function index()
    {
        $isAdmin = Auth::user()->role === 'admin';
        
        // If admin, show all interns, if intern, show admin
        $users = $isAdmin
            ? User::where('role', 'intern')->get()
            : User::where('role', 'admin')->get();

        return Inertia::render('Chat/Index', [
            'users' => $users
        ]);
    }
} 
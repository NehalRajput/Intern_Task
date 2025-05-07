<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    public function deleteUser(User $user)
    {
        // Check if the user is an intern
        if ($user->role !== 'intern') {
            return redirect()->back()->with('error', 'Only intern accounts can be deleted');
        }

        // Delete the user
        $user->delete();

        return redirect()->back()->with('success', 'Intern account deleted successfully');
    }
} 
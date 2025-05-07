<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserType;
use Illuminate\Http\Request;

class SuperAdminController extends Controller
{
    public function dashboard()
    {
        $admins = User::whereHas('userType', function($query) {
            $query->where('name', 'admin');
        })->get();
        
        $interns = User::whereHas('userType', function($query) {
            $query->where('name', 'intern');
        })->get();

        return view('super_admin.dashboard', compact('admins', 'interns'));
    }

    public function manageUsers()
    {
        $users = User::with('userType')->get();
        $userTypes = UserType::all();
        return view('super_admin.manage_users', compact('users', 'userTypes'));
    }

    public function updateUserType(Request $request, User $user)
    {
        $request->validate([
            'user_type_id' => 'required|exists:user_types,id'
        ]);

        $user->update([
            'user_type_id' => $request->user_type_id
        ]);

        return back()->with('success', 'User type updated successfully');
    }
}
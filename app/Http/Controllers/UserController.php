<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with('role')->where('role_id', Role::where('name', 'intern')->first()->id)->get();
        return view('Admin.interns.index', compact('users'));
    }

    public function create()
    {
        return view('Admin.interns.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed'
        ]);

        $internRole = Role::where('name', 'intern')->first();

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => $internRole->id
        ]);

        return redirect()->route('admin.interns.index')
            ->with('success', 'Intern created successfully');
    }

    public function edit(User $user)
    {
        if (!$user->isIntern()) {
            abort(403, 'Only intern accounts can be edited here');
        }
        return view('Admin.interns.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        if (!$user->isIntern()) {
            abort(403, 'Only intern accounts can be edited here');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|min:8|confirmed'
        ]);

        $data = $request->except('password');
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('admin.interns.index')
            ->with('success', 'Intern updated successfully');
    }

    public function destroy(User $user)
    {
        if (!$user->isIntern()) {
            abort(403, 'Only intern accounts can be deleted');
        }

        $user->delete();

        return redirect()->route('admin.interns.index')
            ->with('success', 'Intern deleted successfully');
    }

    public function dashboard()
    {
        // Add intern dashboard logic here
        return view('dashboard');
    }
} 
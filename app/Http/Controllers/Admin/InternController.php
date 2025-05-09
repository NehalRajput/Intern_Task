<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\InternRegisterRequest;
use App\Models\User;
use Illuminate\Http\Request;

class InternController extends Controller
{
    public function index()
    {
        $interns = User::where('role_id', 3)->get();
        return view('Admin.interns.index', compact('interns'));
    }

    public function create()
    {
        return view('Admin.interns.create');
    }

    public function store(InternRegisterRequest $request)
    {
        $validated = $request->validated();
        $validated['role_id'] = 3;

        $intern = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => bcrypt($validated['password']),
            'role_id' => $validated['role_id'],
        ]);

        return redirect()->route('admin.interns.index')
            ->with('success', 'Intern created successfully.');
    }

    

    public function edit(User $intern)
    {
        return view('Admin.interns.edit', compact('intern'));
    }

    public function update(Request $request, User $intern)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $intern->id,
            'password' => 'nullable|min:8|confirmed',
        ]);

        $intern->name = $request->name;
        $intern->email = $request->email;
        
        if ($request->filled('password')) {
            $intern->password = bcrypt($request->password);
        }

        $intern->save();

        return redirect()->route('admin.interns.index')
            ->with('success', 'Intern updated successfully.');
    }

    public function destroy(User $intern)
    {
        $intern->delete();

        return response()->json([
            'success' => true,
            'message' => 'Intern deleted successfully'
        ]);
    }
} 
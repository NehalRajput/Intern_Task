<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    // Show Register Page
    public function showRegister()
    {
        return view('Auth.Register');
    }

    // Handle Register
    public function register(RegisterRequest $request)
    {
        try {
            $validated = $request->validated();
            $validated['role_id'] = 3;

            $user = User::create($validated);

            Auth::guard('web')->login($user);

            return redirect('dashboard');
        } catch (\Exception $e) {
            Log::error('User registration failed', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Registration failed. Please try again.');
        }
    }

    // Show Login Page
    public function showLogin()
    {
        return view('Auth.Login');
    }

    // Handle Login
    public function login(LoginRequest $request)
    {
        try {
            $validated = $request->validated();

            if (Auth::guard('user')->attempt($validated)) {
                $request->session()->regenerate();
                return redirect('dashboard');
            }

            return back()->withErrors([
                'email' => 'Invalid credentials.',
            ]);
        } catch (\Exception $e) {
            Log::error('User login failed', ['error' => $e->getMessage()]);
            return back()->with('error', 'Login failed. Please try again.');
        }
    }

    // Show Admin Login Page
    public function showAdminLogin()
    {
        return view('Admin.login');
    }

    // Handle Admin Login
    public function adminLogin(LoginRequest $request)
    {
        try {
            $validated = $request->validated();

            if (Auth::guard('admin')->attempt($validated)) {
                $request->session()->regenerate();
                return redirect('admin.dashboard');
            }

            return back()->withErrors([
                'email' => 'Invalid credentials.',
            ]);
        } catch (\Exception $e) {
            Log::error('Admin login failed', ['error' => $e->getMessage()]);
            return back()->with('error', 'Login failed. Please try again.');
        }
    }

    // Handle User Logout
    public function logout(Request $request)
    {
        try {
            Auth::guard('user')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect('intern.login');
        } catch (\Exception $e) {
            Log::error('User logout failed', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Logout failed. Please try again.');
        }
    }

    // Handle Admin Logout
    public function adminLogout(Request $request)
    {
        try {
            Auth::guard('admin')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect('admin.login');
        } catch (\Exception $e) {
            Log::error('Admin logout failed', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Logout failed. Please try again.');
        }
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Role;
use App\Models\Task;
use App\Models\Permission;
use App\Models\RolePermission;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class AdminController extends Controller
{
    public function index()
    {
        $admins = Admin::all();
        return view('Admin.index', compact('admins'));
    }

    public function create()
    {
        $permissions = Permission::all();
        return view('Admin.create', compact('permissions'));
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:admins',
                'password' => 'required|min:6',
                'permissions' => 'required|array|min:1',
                'permissions.*' => 'exists:permissions,id'
            ]);

            Log::info('Admin validation passed', ['email' => $validated['email']]);

            $validated['password'] = Hash::make($validated['password']);
            $validated['role_id'] = 2; // Admin role ID

            DB::beginTransaction();
            try {
                $admin = Admin::create([
                    'name' => $validated['name'],
                    'email' => $validated['email'],
                    'password' => $validated['password'],
                    'role_id' => $validated['role_id']
                ]);

                Log::info('Admin created successfully', ['admin_id' => $admin->id]);

                if ($request->has('permissions')) {
                    foreach ($request->permissions as $permissionId) {
                        RolePermission::create([
                            'admin_id' => $admin->id,
                            'permission_id' => $permissionId
                        ]);
                    }

                    Log::info('Permissions assigned to admin', [
                        'admin_id' => $admin->id,
                        'permissions' => $request->permissions
                    ]);
                }

                DB::commit();
                return redirect()->route('admin.admins.index')
                    ->with('success', 'Admin created successfully.');
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Failed to create admin record', [
                    'error' => $e->getMessage(),
                    'email' => $validated['email']
                ]);
                return redirect()->back()
                    ->with('error', 'Failed to create admin. Please try again.')
                    ->withInput();
            }

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::warning('Admin validation failed', [
                'errors' => $e->errors(),
                'input' => $request->except('password')
            ]);
            throw $e;
        } catch (\Exception $e) {
            Log::error('Unexpected error while creating admin', [
                'error' => $e->getMessage(),
                'input' => $request->except('password')
            ]);
            return redirect()->back()
                ->with('error', 'An unexpected error occurred. Please try again.')
                ->withInput();
        }
    }

    public function edit(Admin $admin)
    {
        try {
            $permissions = Permission::all();
            $adminPermissions = $admin->permissions->pluck('id')->toArray();
            return view('Admin.edit', compact('admin', 'permissions', 'adminPermissions'));
        } catch (\Exception $e) {
            Log::error('Error loading admin edit form', ['error' => $e->getMessage()]);
            return redirect()->route('admin.admins.index')->with('error', 'Failed to load admin edit page.');
        }
    }

    public function update(Request $request, Admin $admin)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:admins,email,' . $admin->id,
                'password' => 'nullable|min:6',
                'permissions' => 'required|array|min:1',
                'permissions.*' => 'exists:permissions,id'
            ]);

            if (isset($validated['password'])) {
                $validated['password'] = Hash::make($validated['password']);
            } else {
                unset($validated['password']);
            }

            $admin->update([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => $validated['password'] ?? $admin->password
            ]);

            RolePermission::where('admin_id', $admin->id)->delete();

            if ($request->has('permissions')) {
                foreach ($request->permissions as $permissionId) {
                    RolePermission::create([
                        'admin_id' => $admin->id,
                        'permission_id' => $permissionId
                    ]);
                }
            }

            return redirect()->route('admin.admins.index')
                ->with('success', 'Admin updated successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::warning('Validation failed during admin update', [
                'errors' => $e->errors()
            ]);
            throw $e;
        } catch (\Exception $e) {
            Log::error('Error updating admin', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Failed to update admin. Please try again.');
        }
    }

    public function destroy(Admin $admin)
    {
        try {
            RolePermission::where('admin_id', $admin->id)->delete();
            $admin->delete();

            return redirect()->route('admin.admins.index')
                ->with('success', 'Admin deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Error deleting admin', ['error' => $e->getMessage()]);
            return redirect()->route('admin.admins.index')
                ->with('error', 'Failed to delete admin. Please try again.');
        }
    }

    public function dashboard()
    {
        try {
            $tasks = Task::with('interns')->get();
            $interns = User::all();
            return view('Admin.Dashboard', [
                'tasks' => $tasks,
                'interns' => $interns
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to load dashboard', ['error' => $e->getMessage()]);
            return redirect()->route('admin.admins.index')->with('error', 'Failed to load dashboard.');
        }
    }

    public function deleteUser(User $user)
    {
        try {
            $user->delete();
            return redirect()->back()->with('success', 'Intern account deleted successfully');
        } catch (ModelNotFoundException $e) {
            Log::warning('User not found for deletion', ['user_id' => $user->id ?? 'unknown']);
            return redirect()->back()->with('error', 'User not found.');
        } catch (\Exception $e) {
            Log::error('Error deleting user', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Failed to delete user. Please try again.');
        }
    }
}

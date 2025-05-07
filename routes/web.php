<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\SuperAdminController;
use App\Http\Controllers\InternController;
use App\Http\Controllers\CommentController;


// Guest Routes (User)
Route::middleware(['guest'])->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

// Authenticated User Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::post('/logout', function () {
        auth()->logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return redirect('/login');
    })->name('logout');
});

// Admin Routes
Route::prefix('admin')->group(function () {
    // Admin Guest Routes
    Route::middleware(['guest:admin'])->group(function () {
        Route::get('/login', [AuthController::class, 'showAdminLogin'])->name('admin.login');
        Route::post('/login', [AuthController::class, 'adminLogin'])->name('admin.authenticate');
    });

    // Admin Authenticated Routes
    Route::middleware(['auth:admin'])->group(function () {
        Route::get('/dashboard', function () {
            $tasks = App\Models\Task::with('interns')->get();
            $interns = App\Models\User::where('role', 'intern')->get();
            return view('Admin.dashboard', compact('tasks', 'interns'));
        })->name('admin.dashboard');

        // Task Management Routes
        Route::get('/tasks', [TaskController::class, 'index'])->name('tasks.index');
        Route::get('/tasks/create', [TaskController::class, 'create'])->name('tasks.create');
        Route::post('/tasks', [TaskController::class, 'store'])->name('tasks.store');
        Route::get('/tasks/{task}/edit', [TaskController::class, 'edit'])->name('tasks.edit');
        Route::put('/tasks/{task}', [TaskController::class, 'update'])->name('tasks.update');
        Route::delete('/tasks/{task}', [TaskController::class, 'destroy'])->name('tasks.destroy');
        Route::post('/tasks/{task}/assign-intern', [TaskController::class, 'assignIntern'])->name('tasks.assign-intern');
        Route::delete('/tasks/{task}/interns/{intern}', [TaskController::class, 'detachIntern'])->name('tasks.detach-intern');

        // Comment routes
        Route::get('/tasks/{task}/comments', [CommentController::class, 'index'])->name('comments.index');
        Route::post('/tasks/{task}/comments', [CommentController::class, 'store'])->name('comments.store');
        Route::put('/comments/{comment}', [CommentController::class, 'update'])->name('comments.update');
        Route::delete('/comments/{comment}', [CommentController::class, 'destroy'])->name('comments.destroy');

        Route::post('/logout', function () {
            auth('admin')->logout();
            request()->session()->invalidate();
            request()->session()->regenerateToken();
            return redirect()->route('admin.login');
        })->name('admin.logout');
    });
});

// Super Admin Routes
Route::prefix('super-admin')->middleware(['auth', 'super_admin'])->group(function () {
    Route::get('/dashboard', [SuperAdminController::class, 'dashboard'])->name('super_admin.dashboard');
    Route::get('/manage-users', [SuperAdminController::class, 'manageUsers'])->name('super_admin.manage_users');
    Route::put('/users/{user}/update-type', [SuperAdminController::class, 'updateUserType'])->name('super_admin.update_user_type');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/intern/tasks', [InternController::class, 'tasks'])->name('intern.tasks');
});
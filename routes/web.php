<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\SuperAdminController;
use App\Http\Controllers\InternController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\MessageController;

// Guest Routes (Unauthenticated Users)
Route::middleware('guest')->group(function () {
    // User authentication
    Route::controller(AuthController::class)->group(function () {
        Route::get('/login', 'showLogin')->name('login');
        Route::post('/login', 'login');
        Route::get('/register', 'showRegister')->name('register');
        Route::post('/register', 'register');
        
        // Admin authentication
        Route::prefix('admin')->group(function () {
            Route::get('/login', 'showAdminLogin')->name('admin.login');
            Route::post('/login', 'adminLogin')->name('admin.authenticate');
        });
    });
});

// Authenticated User Routes
Route::middleware('auth')->group(function () {
    // Common logout for all auth types
    Route::post('/logout', function () {
        auth()->logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return redirect('/login');
    })->name('logout');

    // Dashboard for regular users
    Route::get('/dashboard', fn() => view('dashboard'))->name('dashboard');

    // Intern-specific routes
    Route::prefix('intern')->group(function () {
        Route::get('/tasks', [InternController::class, 'tasks'])->name('intern.tasks');
        Route::put('/tasks/{task}/status', [InternController::class, 'updateTaskStatus'])
            ->name('intern.update-task-status');
    });

    // Super Admin routes
    Route::prefix('super-admin')->middleware('super_admin')->group(function () {
        Route::controller(SuperAdminController::class)->group(function () {
            Route::get('/dashboard', 'dashboard')->name('super_admin.dashboard');
            Route::get('/manage-users', 'manageUsers')->name('super_admin.manage_users');
            Route::put('/users/{user}/update-type', 'updateUserType')
                ->name('super_admin.update_user_type');
        });
    });

    // Message routes
    Route::get('/messages', [MessageController::class, 'index'])->name('messages.index');
    Route::get('/messages/{user}', [MessageController::class, 'show'])->name('messages.show');
    Route::post('/messages', [MessageController::class, 'store'])->name('messages.store');
    Route::get('/messages/unread/count', [MessageController::class, 'getUnreadCount'])->name('messages.unread.count');
});

// Admin Routes
Route::prefix('admin')->middleware('auth:admin')->group(function () {
    // Admin dashboard
    Route::get('/dashboard', function () {
        return view('Admin.dashboard', [
            'tasks' => App\Models\Task::with('interns')->get(),
            'interns' => App\Models\User::where('role', 'intern')->get()
        ]);
    })->name('admin.dashboard');

    // Task management
    Route::controller(TaskController::class)->group(function () {
        Route::get('/tasks', 'index')->name('tasks.index');
        Route::get('/tasks/create', 'create')->name('tasks.create');
        Route::post('/tasks', 'store')->name('tasks.store');
        Route::get('/tasks/{task}/edit', 'edit')->name('tasks.edit');
        Route::put('/tasks/{task}', 'update')->name('tasks.update');
        Route::delete('/tasks/{task}', 'destroy')->name('tasks.destroy');
        Route::post('/tasks/{task}/assign-intern', 'assignIntern')->name('tasks.assign-intern');
        Route::delete('/tasks/{task}/interns/{intern}', 'detachIntern')->name('tasks.detach-intern');
    });

    // User management
    Route::delete('/users/{user}', [AdminController::class, 'deleteUser'])->name('admin.delete-user');

    // Comments
    Route::controller(CommentController::class)->group(function () {
        Route::get('/tasks/{task}/comments', 'index')->name('comments.index');
        Route::post('/tasks/{task}/comments', 'store')->name('comments.store');
        Route::put('/comments/{comment}', 'update')->name('comments.update');
        Route::delete('/comments/{comment}', 'destroy')->name('comments.destroy');
    });

    // Admin logout
    Route::post('/logout', function () {
        auth('admin')->logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return redirect()->route('admin.login');
    })->name('admin.logout');
});
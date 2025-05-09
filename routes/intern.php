<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\CommentController;

Route::middleware(['guest:user'])->name('intern.')->group(function () {
    Route::controller(AuthController::class)->group(function () {
        Route::get('/login', 'showLogin')->name('login');
        Route::post('/login', 'login');
        Route::get('/register', 'showRegister')->name('register');
        Route::post('/register', 'register');
    });
});

/*
|--------------------------------------------------------------------------
| Authenticated User (Intern) Routes
|--------------------------------------------------------------------------
*/
Route::middleware("auth:user")->group(function () {
    // Intern Dashboard
    Route::get('/dashboard', [UserController::class, 'dashboard'])->name('dashboard');
    
    // Tasks Routes
    Route::controller(TaskController::class)->prefix('tasks')->name('intern.tasks.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::put('/{task}/status', 'updateStatus')->name('update-task-status');
    });

    // Comments Routes
    Route::controller(CommentController::class)->prefix('comments')->name('intern.comments.')->group(function () {
        Route::post('/tasks/{task}', 'store')->name('store');
    });

    // Chat System
    /*Route::controller(ChatController::class)->prefix('chat')->name('chat.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/send', 'sendMessage')->name('send');
        Route::get('/messages/{admin}', 'getMessages')->name('messages');
        Route::post('/mark-read', 'markAsRead')->name('mark-read');
    });
*/
    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

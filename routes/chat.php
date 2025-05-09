<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MessageController;

// Routes accessible by both admins and interns
Route::middleware(['auth:admin,user'])->group(function () {
    // Chat interface
    Route::get('/chat', [MessageController::class, 'index'])->name('chat');
    
    // Message handling routes
    Route::post('/messages', [MessageController::class, 'store'])->name('messages.store');
    Route::get('/messages', [MessageController::class, 'getMessages'])->name('messages.get');
    Route::post('/messages/{message}/read', [MessageController::class, 'markAsRead'])->name('messages.read');
    
    // Get users for chat
    Route::get('/chat/users', [MessageController::class, 'getUsers'])->name('chat.users');
    
    // Mark all messages as read for a conversation
    Route::post('/messages/read-all/{userId}', [MessageController::class, 'markAllAsRead'])->name('messages.readAll');
}); 
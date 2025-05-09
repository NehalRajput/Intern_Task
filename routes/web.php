<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\MessageController;

/*
|--------------------------------------------------------------------------
| Guest Routes
|--------------------------------------------------------------------------
*/

// Include admin and intern routes
require __DIR__.'/admin.php';
require __DIR__.'/intern.php';
require __DIR__.'/chat.php';  // Include chat routes



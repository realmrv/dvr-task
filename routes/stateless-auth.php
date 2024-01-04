<?php

declare(strict_types=1);

use App\Http\Controllers\Auth\Stateless\AuthenticatedUserController;
use App\Http\Controllers\Auth\Stateless\RegisteredUserController;
use Illuminate\Support\Facades\Route;

Route::post('/register', [RegisteredUserController::class, 'store'])
    ->name('register');

Route::post('/login', [AuthenticatedUserController::class, 'store'])
    ->name('login');

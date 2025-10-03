<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WelcomeController;
use App\Http\Controllers\AuthController;

// Landing page
Route::get('/', [WelcomeController::class, 'index'])->name('welcome');

// Halaman login
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');

// Dashboard Master (sementara statis dulu)
Route::get('/dashboardmaster', function () {
    return view('dashboardmaster');
})->name('dashboardmaster');

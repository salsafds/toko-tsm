<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WelcomeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Master\SatuanController;
use App\Http\Controllers\Master\RoleController;

// Landing page
Route::get('/', [WelcomeController::class, 'index'])->name('welcome');

// Halaman login
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');

//Halaman logout
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Dashboard Master (sementara statis dulu)
Route::get('/dashboard-master', function () {
    return view('master/dashboard-master');
})->name('dashboard-master');

Route::middleware(['auth'])->name('master.')->group(function () {
    Route::resource('data-satuan', SatuanController::class);
});

Route::middleware(['auth'])->name('master.')->group(function () {
    Route::resource('data-role', RoleController::class);
});


<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WelcomeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Master\SatuanController;

// Landing page
Route::get('/', [WelcomeController::class, 'index'])->name('welcome');

// Halaman login
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');

//Halaman logout
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Dashboard Master (sementara statis dulu)
Route::get('/dashboard', function () {
    return view('master/dashboard');
})->name('dashboard');

Route::middleware(['auth'])->prefix('master')->name('master.')->group(function () {
    Route::resource('dataSatuan', SatuanController::class);
});

Route::get('/dataSatuan', function () {
    return view('master/dataSatuan/index');
})->name('master.dataSatuan.index');

Route::get('/dataSatuan.create', function () {
    return view('master/dataSatuan/create');
})->name('master.dataSatuan.create');



// Route::middleware(['auth'])->prefix('master')->name('master.')->group(function () {
//     Route::resource('dataSatuan', SatuanController::class);
// });
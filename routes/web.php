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

Route::get('/dashboard-admin', function () {
    return view('admin/dashboard-admin');
})->name('dashboard-admin');

Route::middleware(['auth'])->name('master.')->group(function () {
    Route::resource('data-bahasa', SatuanController::class);
});

Route::middleware(['auth'])->name('master.')->group(function () {
    Route::resource('data-barang', RoleController::class);
});

Route::middleware(['auth'])->name('master.')->group(function () {
    Route::resource('data-jabatan', RoleController::class);
});

Route::middleware(['auth'])->name('master.')->group(function () {
    Route::resource('data-karyawan', RoleController::class);
});

Route::middleware(['auth'])->name('master.')->group(function () {
    Route::resource('data-kota', RoleController::class);
});

Route::middleware(['auth'])->name('master.')->group(function () {
    Route::resource('data-negara', RoleController::class);
});

Route::middleware(['auth'])->name('master.')->group(function () {
    Route::resource('data-pendidikan', RoleController::class);
});

Route::middleware(['auth'])->name('master.')->group(function () {
    Route::resource('data-provinsi', RoleController::class);
});

Route::middleware(['auth'])->name('master.')->group(function () {
    Route::resource('data-role', RoleController::class);
});

Route::middleware(['auth'])->name('master.')->group(function () {
    Route::resource('data-satuan', RoleController::class);
});

Route::middleware(['auth'])->name('master.')->group(function () {
    Route::resource('data-supplier', RoleController::class);
});
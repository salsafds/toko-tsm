<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WelcomeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Master\BahasaController;
use App\Http\Controllers\Master\BarangController;
use App\Http\Controllers\Master\JabatanController;
use App\Http\Controllers\Master\KaryawanController;
use App\Http\Controllers\Master\KotaController;
use App\Http\Controllers\Master\NegaraController;
use App\Http\Controllers\Master\PendidikanController;
use App\Http\Controllers\Master\ProvinsiController;
use App\Http\Controllers\Master\RoleController;
use App\Http\Controllers\Master\SatuanController;
use App\Http\Controllers\Master\SupplierController;

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
    Route::resource('data-bahasa', BahasaController::class);
});

Route::middleware(['auth'])->name('master.')->group(function () {
    Route::resource('data-barang', BarangController::class);
});

Route::middleware(['auth'])->name('master.')->group(function () {
    Route::resource('data-jabatan', JabatanController::class);
});

Route::middleware(['auth'])->name('master.')->group(function () {
    Route::resource('data-karyawan', KaryawanController::class);
});

Route::middleware(['auth'])->name('master.')->group(function () {
    Route::resource('data-kota', KotaController::class);
});

Route::middleware(['auth'])->name('master.')->group(function () {
    Route::resource('data-negara', NegaraController::class);
});

Route::middleware(['auth'])->name('master.')->group(function () {
    Route::resource('data-pendidikan', PendidikanController::class);
});

Route::middleware(['auth'])->name('master.')->group(function () {
    Route::resource('data-provinsi', ProvinsiController::class);
});

Route::middleware(['auth'])->name('master.')->group(function () {
    Route::resource('data-role', RoleController::class);
});

Route::middleware(['auth'])->name('master.')->group(function () {
    Route::resource('data-satuan', SatuanController::class);
});

Route::middleware(['auth'])->name('master.')->group(function () {
    Route::resource('data-supplier', SupplierController::class);
});
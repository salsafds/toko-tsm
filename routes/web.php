
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WelcomeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Master\BahasaController;
use App\Http\Controllers\Master\BarangController;
use App\Http\Controllers\Master\JabatanController;
use App\Http\Controllers\Master\KotaController;
use App\Http\Controllers\Master\NegaraController;
use App\Http\Controllers\Master\PendidikanController;
use App\Http\Controllers\Master\ProvinsiController;
use App\Http\Controllers\Master\RoleController;
use App\Http\Controllers\Master\SatuanController;
use App\Http\Controllers\Master\SupplierController;
use App\Http\Controllers\Master\KategoriBarangController;
use App\Http\Controllers\Master\AgenEkspedisiController;     
use App\Http\Controllers\Master\PelangganController;
use App\Http\Controllers\Master\UserController;
use App\Http\Controllers\PrivacyPolicyController;
use App\Http\Controllers\ProfileController;


// Landing page
Route::get('/', [WelcomeController::class, 'index'])->name('welcome');

Route::middleware(['auth'])->name('layouts.')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/check-username', [ProfileController::class, 'checkUsername'])->name('profile.check-username');
    Route::post('/profile/verify-password', [ProfileController::class, 'verifyOldPassword'])->name('profile.verify-password');
});

 Route::get('/privacy-policy', [PrivacyPolicyController::class, 'index'])->name('privacy-policy');
 
// Halaman login
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');

//Halaman logout
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

//DASHBOARD
Route::get('/dashboard-master', function () {
    return view('master.dashboard-master');
})->middleware(['auth', 'role:R01'])->name('dashboard-master');

Route::get('/dashboard-admin', function () {
    return view('admin.dashboard-admin');
})->middleware(['auth', 'role:R02'])->name('dashboard-admin');

// ADMIN MASTER
Route::middleware(['auth', 'role:R01'])->name('master.')->group(function () {
    //data user(karyawan)
    Route::resource('data-user', UserController::class);
    Route::post('data-user/check-username', [UserController::class, 'checkUsername'])
     ->name('data-user.check-username');
    
    //data supplier
    Route::resource('data-supplier', SupplierController::class);
    Route::get('data-supplier/provinsis/{id_negara}', [SupplierController::class, 'getProvinsiByNegara'])->name('data-supplier.provinsis');
    Route::get('data-supplier/kotas/{id_provinsi}', [SupplierController::class, 'getKotaByProvinsi'])->name('data-supplier.kotas');
    
    //data kategori barang
    Route::resource('data-kategori-barang', KategoriBarangController::class);
    
    //data agen ekspedisi
    Route::resource('data-agen-ekspedisi', AgenEkspedisiController::class);
    Route::get('data-agen-ekspedisi/provinsis/{id_negara}', [AgenEkspedisiController::class, 'getProvinsiByNegara'])->name('data-agen-ekspedisi.provinsis');
    Route::get('data-agen-ekspedisi/kotas/{id_provinsi}', [AgenEkspedisiController::class, 'getKotaByProvinsi'])->name('data-agen-ekspedisi.kotas');
    
    //data pelanggan
    Route::resource('data-pelanggan', PelangganController::class);
    Route::get('data-pelanggan/provinsis/{id_negara}', [PelangganController::class, 'getProvinsiByNegara'])->name('data-pelanggan.provinsis');
    Route::get('data-pelanggan/kotas/{id_provinsi}', [PelangganController::class, 'getKotaByProvinsi'])->name('data-pelanggan.kotas');
    
    //data negara
    Route::resource('data-negara', NegaraController::class);
    
    //data provinsi
    Route::resource('data-provinsi', ProvinsiController::class);
    
    //data kota
    Route::resource('data-kota', KotaController::class);
    Route::get('data-kota/provinsis/{id_negara}', [KotaController::class, 'getProvinsiByNegara'])->name('data-kota.provinsis');
    
    //data pendidikan
    Route::resource('data-pendidikan', PendidikanController::class);
    
    //data satuan
    Route::resource('data-satuan', SatuanController::class);
    
    //data jabatan
    Route::resource('data-jabatan', JabatanController::class);
    
    //data role
    Route::resource('data-role', RoleController::class);
    
    //data bahasa
    Route::resource('data-bahasa', BahasaController::class);
    
    //data barang
    Route::resource('data-barang', BarangController::class);
    
});

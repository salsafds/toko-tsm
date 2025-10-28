
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
use App\Http\Controllers\Master\KategoriBarangController;
use App\Http\Controllers\Master\AgenEkspedisiController;     
use App\Http\Controllers\Master\PelangganController;
use App\Http\Controllers\Master\UserController;
use App\Http\Controllers\PrivacyPolicyController;

// Landing page
Route::get('/', [WelcomeController::class, 'index'])->name('welcome');

//Privacy Policy
Route::middleware(['auth'])->get('/privacy-policy', [PrivacyPolicyController::class, 'index'])->name('privacy-policy');

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
    Route::resource('data-pelanggan', PelangganController::class);
    Route::get('data-pelanggan/provinsis/{id_negara}', [PelangganController::class, 'getProvinsiByNegara'])->name('data-pelanggan.provinsis');
    Route::get('data-pelanggan/kotas/{id_provinsi}', [PelangganController::class, 'getKotaByProvinsi'])->name('data-pelanggan.kotas');
});

Route::middleware(['auth'])->name('master.')->group(function () {
    Route::resource('data-jabatan', JabatanController::class);
});

 Route::middleware(['auth'])->name('master.')->group(function () {
  Route::resource('data-karyawan', KaryawanController::class);
 });

Route::middleware(['auth'])->name('master.')->group(function () {
    Route::resource('data-kota', KotaController::class);
    Route::get('data-kota/provinsis/{id_negara}', [KotaController::class, 'getProvinsiByNegara'])->name('data-kota.provinsis');
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
    Route::get('data-supplier/provinsis/{id_negara}', [SupplierController::class, 'getProvinsiByNegara'])->name('data-supplier.provinsis');
    Route::get('data-supplier/kotas/{id_provinsi}', [SupplierController::class, 'getKotaByProvinsi'])->name('data-supplier.kotas');
});

Route::middleware(['auth'])->name('master.')->group(function () {
    Route::resource('data-kategori-barang', KategoriBarangController::class);
});

Route::middleware(['auth'])->name('master.')->group(function () {
    Route::resource('data-agen-ekspedisi', AgenEkspedisiController::class);
    Route::get('data-agen-ekspedisi/provinsis/{id_negara}', [AgenEkspedisiController::class, 'getProvinsiByNegara'])->name('data-agen-ekspedisi.provinsis');
    Route::get('data-agen-ekspedisi/kotas/{id_provinsi}', [AgenEkspedisiController::class, 'getKotaByProvinsi'])->name('data-agen-ekspedisi.kotas');
});

Route::middleware(['auth'])->name('master.')->group(function () {
    Route::resource('data-user', UserController::class);
    Route::post('data-user/check-username', [UserController::class, 'checkUsername'])
     ->name('data-user.check-username');
});
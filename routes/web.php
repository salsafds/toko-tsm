<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WelcomeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PrivacyPolicyController;
use App\Http\Controllers\ProfileController;

// Master Controllers
use App\Http\Controllers\Master\BulananController;
use App\Http\Controllers\Master\MutasiController;
use App\Http\Controllers\Master\AgenEkspedisiController; 
use App\Http\Controllers\Master\BahasaController;
use App\Http\Controllers\Master\JabatanController;
use App\Http\Controllers\Master\KategoriBarangController;
use App\Http\Controllers\Master\NegaraController;
use App\Http\Controllers\Master\KotaController;
use App\Http\Controllers\Master\ProvinsiController;
use App\Http\Controllers\Master\PendidikanController;
use App\Http\Controllers\Master\RoleController;
use App\Http\Controllers\Master\SatuanController;
use App\Http\Controllers\Master\SupplierController;
use App\Http\Controllers\Master\PelangganController;
use App\Http\Controllers\Master\UserController;
use App\Http\Controllers\Master\DashboardMasterController;

// Admin Controllers
use App\Http\Controllers\Admin\PembelianController;
use App\Http\Controllers\Admin\BarangController;
use App\Http\Controllers\Admin\PenjualanController;
use App\Http\Controllers\Admin\DashboardAdminController;


// Landing page
Route::get('/', [WelcomeController::class, 'index'])->name('welcome');

Route::middleware(['auth'])->name('profile.')->group(function () {
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
Route::get('/dashboard-master', [DashboardMasterController::class, 'index'])
    ->middleware(['auth', 'role:R01'])
    ->name('dashboard-master');

Route::get('/dashboard-admin', [DashboardAdminController::class, 'index'])
    ->middleware(['auth', 'role:R02'])
    ->name('dashboard-admin');

// ADMIN MASTER
Route::middleware(['auth', 'role:R01'])->name('master.')->group(function () {

    // Laporan Bulanan
    Route::get('laporan/bulanan', [BulananController::class, 'index'])
     ->name('laporan.bulanan');

    // Barang Terlaris
    Route::get('laporan/terlaris', [BulananController::class, 'terlaris'])
        ->name('laporan.terlaris');

    // Export Barang Terlaris (PDF & Excel)
    Route::get('laporan/terlaris/export', [BulananController::class, 'exportTerlaris'])
        ->name('laporan.terlaris.export');

    // Laporan Mutasi Barang
    Route::get('/mutasi-barang', [MutasiController::class, 'index'])
        ->name('mutasi.index');

    Route::get('/mutasi-barang/export', [MutasiController::class, 'export'])
        ->name('mutasi.export');

    // Print Daftar Barang
    Route::get('/stok/pdf', [BulananController::class, 'pdfStok'])
        ->name('stok.pdf');

    // Print Laporan Bulanan    
    Route::get('/laporan/bulanan/export', [BulananController::class, 'export'])
    ->name('laporan.bulanan.export');
    
    // Data User (Karyawan)
    Route::resource('data-user', UserController::class);
    Route::post('data-user/check-username', [UserController::class, 'checkUsername'])
     ->name('data-user.check-username');
    
    // Data Supplier
    Route::resource('data-supplier', SupplierController::class);
    Route::get('data-supplier/provinsis/{id_negara}', [SupplierController::class, 'getProvinsiByNegara'])->name('data-supplier.provinsis');
    Route::get('data-supplier/kotas/{id_provinsi}', [SupplierController::class, 'getKotaByProvinsi'])->name('data-supplier.kotas');
    
    // Data Kategori Barang
    Route::resource('data-kategori-barang', KategoriBarangController::class);
    
    // Data Agen Ekspedisi
    Route::resource('data-agen-ekspedisi', AgenEkspedisiController::class);
    Route::get('data-agen-ekspedisi/provinsis/{id_negara}', [AgenEkspedisiController::class, 'getProvinsiByNegara'])->name('data-agen-ekspedisi.provinsis');
    Route::get('data-agen-ekspedisi/kotas/{id_provinsi}', [AgenEkspedisiController::class, 'getKotaByProvinsi'])->name('data-agen-ekspedisi.kotas');
    
    // Data Pelanggan
    Route::resource('data-pelanggan', PelangganController::class);
    Route::get('data-pelanggan/provinsis/{id_negara}', [PelangganController::class, 'getProvinsiByNegara'])->name('data-pelanggan.provinsis');
    Route::get('data-pelanggan/kotas/{id_provinsi}', [PelangganController::class, 'getKotaByProvinsi'])->name('data-pelanggan.kotas');
    
    // Data Negara
    Route::resource('data-negara', NegaraController::class);
    
    // Data Provinsi
    Route::resource('data-provinsi', ProvinsiController::class);
    
    // Data Kota
    Route::resource('data-kota', KotaController::class);
    Route::get('data-kota/provinsis/{id_negara}', [KotaController::class, 'getProvinsiByNegara'])->name('data-kota.provinsis');
    
    // Data Pendidikan
    Route::resource('data-pendidikan', PendidikanController::class);
    
    // Data Satuan
    Route::resource('data-satuan', SatuanController::class);
    
    // Data Jabatan
    Route::resource('data-jabatan', JabatanController::class);
    
    // Data Role
    Route::resource('data-role', RoleController::class);
    
    // Data Bahasa
    Route::resource('data-bahasa', BahasaController::class);
    
});

Route::middleware(['auth', 'role:R02'])->name('admin.')->group(function () {
    // Data Penjualan
    Route::resource('penjualan', PenjualanController::class);
    Route::get('penjualan/{id}/print', [PenjualanController::class, 'print'])->name('penjualan.print');
    Route::patch('penjualan/{id_penjualan}/selesai', [PenjualanController::class, 'selesai'])->name('penjualan.selesai');

    // Data Pembelian
    Route::resource('pembelian', PembelianController::class);
    Route::patch('pembelian/{id_pembelian}/selesai', [PembelianController::class, 'selesai'])->name('pembelian.selesai');
    Route::post('pembelian/store-barang', [PembelianController::class, 'storeBarang'])->name('pembelian.storeBarang');
    Route::get('/admin/pembelian/{id}/view', [PembelianController::class, 'show'])->name('admin.pembelian.show');

    // Data Barang
    Route::resource('data-barang', BarangController::class);
});
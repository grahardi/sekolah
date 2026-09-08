<?php

use App\Http\Controllers\SarprasAssetController;
use App\Http\Controllers\SarprasCategoryController;
use Illuminate\Support\Facades\Route;

// Modul Sarpras (Sarana & Prasarana / Inventaris). Sama pola dgn modul lain:
// login pakai Breeze yg sudah ada, multi-sekolah otomatis lewat global scope
// BelongsToSekolah di semua model Sarpras*.
Route::middleware(['web', 'auth', 'not_guru'])->prefix('sarpras')->name('sarpras.')->group(function () {

    Route::get('/', function () { return redirect()->route('sarpras.assets.index'); })->name('index');

    // Kategori
    Route::get('/kategori', [SarprasCategoryController::class, 'index'])->name('categories.index');
    Route::post('/kategori', [SarprasCategoryController::class, 'store'])->name('categories.store');
    Route::put('/kategori/{category}', [SarprasCategoryController::class, 'update'])->name('categories.update');
    Route::delete('/kategori/{category}', [SarprasCategoryController::class, 'destroy'])->name('categories.destroy');

    // Master Data: Lokasi & Sumber Dana
    Route::get('/master-data', [\App\Http\Controllers\SarprasMasterDataController::class, 'index'])->name('master-data.index');
    Route::post('/master-data/lokasi', [\App\Http\Controllers\SarprasMasterDataController::class, 'storeLocation'])->name('master-data.lokasi.store');
    Route::put('/master-data/lokasi/{location}', [\App\Http\Controllers\SarprasMasterDataController::class, 'updateLocation'])->name('master-data.lokasi.update');
    Route::delete('/master-data/lokasi/{location}', [\App\Http\Controllers\SarprasMasterDataController::class, 'destroyLocation'])->name('master-data.lokasi.destroy');
    Route::post('/master-data/dana', [\App\Http\Controllers\SarprasMasterDataController::class, 'storeFundingSource'])->name('master-data.dana.store');
    Route::put('/master-data/dana/{fundingSource}', [\App\Http\Controllers\SarprasMasterDataController::class, 'updateFundingSource'])->name('master-data.dana.update');
    Route::delete('/master-data/dana/{fundingSource}', [\App\Http\Controllers\SarprasMasterDataController::class, 'destroyFundingSource'])->name('master-data.dana.destroy');

    // Barang (Aset) - /barang/create HARUS sebelum /barang/{asset} biar
    // "create" tidak dikira ID (bug yg sudah pernah kejadian di modul lain).
    Route::get('/barang', [SarprasAssetController::class, 'index'])->name('assets.index');
    Route::get('/barang/create', [SarprasAssetController::class, 'create'])->name('assets.create');
    Route::post('/barang', [SarprasAssetController::class, 'store'])->name('assets.store');
    Route::get('/barang/{asset}', [SarprasAssetController::class, 'show'])->name('assets.show');
    Route::get('/barang/{asset}/edit', [SarprasAssetController::class, 'edit'])->name('assets.edit');
    Route::put('/barang/{asset}', [SarprasAssetController::class, 'update'])->name('assets.update');
    Route::delete('/barang/{asset}', [SarprasAssetController::class, 'destroy'])->name('assets.destroy');

    // Riwayat Kerusakan
    Route::get('/riwayat-kerusakan', [\App\Http\Controllers\SarprasRiwayatKerusakanController::class, 'index'])->name('riwayat-kerusakan.index');
    Route::post('/riwayat-kerusakan', [\App\Http\Controllers\SarprasRiwayatKerusakanController::class, 'store'])->name('riwayat-kerusakan.store');
    Route::put('/riwayat-kerusakan/{riwayat}', [\App\Http\Controllers\SarprasRiwayatKerusakanController::class, 'update'])->name('riwayat-kerusakan.update');
    Route::delete('/riwayat-kerusakan/{riwayat}', [\App\Http\Controllers\SarprasRiwayatKerusakanController::class, 'destroy'])->name('riwayat-kerusakan.destroy');

    // Peminjaman
    Route::get('/peminjaman', [\App\Http\Controllers\SarprasPeminjamanController::class, 'index'])->name('peminjaman.index');
    Route::post('/peminjaman', [\App\Http\Controllers\SarprasPeminjamanController::class, 'store'])->name('peminjaman.store');
    Route::post('/peminjaman/{peminjaman}/kembalikan', [\App\Http\Controllers\SarprasPeminjamanController::class, 'kembalikan'])->name('peminjaman.kembalikan');
    Route::delete('/peminjaman/{peminjaman}', [\App\Http\Controllers\SarprasPeminjamanController::class, 'destroy'])->name('peminjaman.destroy');

    // Import Excel
    Route::get('/import', [\App\Http\Controllers\SarprasImportController::class, 'showImport'])->name('import.form');
    Route::get('/import/template', [\App\Http\Controllers\SarprasImportController::class, 'downloadTemplate'])->name('import.template');
    Route::post('/import', [\App\Http\Controllers\SarprasImportController::class, 'import'])->name('import.process');

    // Scan QR
    Route::get('/scan', [\App\Http\Controllers\SarprasImportController::class, 'scanForm'])->name('scan.form');
    Route::post('/scan', [\App\Http\Controllers\SarprasImportController::class, 'scanCari'])->name('scan.cari');

    // Pengaturan
    Route::middleware('admin')->group(function () {
        Route::get('/pengaturan', [\App\Http\Controllers\SarprasPengaturanController::class, 'index'])->name('pengaturan.index');
        Route::put('/pengaturan', [\App\Http\Controllers\SarprasPengaturanController::class, 'update'])->name('pengaturan.update');
    });
});

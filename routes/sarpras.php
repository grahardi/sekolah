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
});

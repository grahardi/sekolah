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

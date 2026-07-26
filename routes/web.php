<?php

use App\Http\Controllers\SimulationController;
use Illuminate\Support\Facades\Route;

// Modul Laboratorium Interaktif - salah satu fitur di platform sekolah.co.id.
// Modul lain (server ujian, buku induk, program BK, manajemen sekolah) didaftarkan
// di file routes terpisah, mis. routes/ujian.php, routes/buku-induk.php, dst,
// lalu di-include di sini atau lewat RouteServiceProvider agar tetap modular.
Route::middleware(['web', 'auth'])->prefix('lab')->name('lab.')->group(function () {
    Route::get('/', [SimulationController::class, 'index'])->name('index');
    Route::get('/{slug}', [SimulationController::class, 'show'])->name('show');
});

// require __DIR__.'/ujian.php';
// require __DIR__.'/buku-induk.php';
// require __DIR__.'/bk.php';
// require __DIR__.'/manajemen.php';

<?php

use App\Http\Controllers\ModulAjarController;
use App\Http\Controllers\NpsnLookupController;
use App\Http\Controllers\SekolahRegistrationController;
use App\Http\Controllers\SimulationController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Landing page publik. Kalau sudah login, arahkan ke dashboard portal.
Route::get('/', function () {
    if (auth()->check()) {
        return redirect('/dashboard');
    }

    return Inertia::render('Welcome', [
        'canLogin' => true,
        'canRegister' => true,
    ]);
})->name('welcome');

Route::get('/demo', function () {
    return Inertia::render('DemoProgram', [
        'canLogin' => true,
        'canRegister' => true,
    ]);
})->name('demo.program');

Route::get('/program/{slug}', function (string $slug) {
    return Inertia::render('ProgramDetail', ['slug' => $slug]);
})->name('program.detail');


// Beranda portal setelah login.
Route::middleware(['web', 'auth'])->get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->name('dashboard');

// Modul Laboratorium Interaktif - salah satu fitur di platform sekolah.co.id,
// tampil sebagai submenu "Lab Interaktif" di dalam PortalLayout. Dibuka tanpa
// login (seperti PhET Interactive Simulations) supaya siapa saja bisa coba
// simulasinya langsung sebelum daftar/masuk ke portal.
Route::middleware(['web'])->prefix('lab')->name('lab.')->group(function () {
    Route::get('/', [SimulationController::class, 'index'])->name('index');
    Route::get('/{slug}', [SimulationController::class, 'show'])->name('show');
});

// Modul Ajar SMP Kurikulum Merdeka - katalog perangkat ajar per mapel dgn
// pencarian, dibuka tanpa login sama seperti Lab Interaktif.
Route::middleware(['web'])->get('/modul-ajar', [ModulAjarController::class, 'index'])->name('modul-ajar.index');

// Modul lain didaftarkan dengan pola yang sama, lalu ditambahkan sebagai
// entri baru di MENU pada resources/js/Layouts/PortalLayout.jsx:
require __DIR__.'/buku-induk.php';
require __DIR__.'/superadmin.php';
// require __DIR__.'/ujian.php';
// require __DIR__.'/bk.php';
// require __DIR__.'/manajemen.php';

// Registrasi sekolah (2 langkah: cari NPSN -> konfirmasi -> buat akun admin).
// Terpisah dari /register bawaan Breeze supaya tidak tertimpa saat
// `breeze:install` dijalankan ulang di masa depan.
Route::middleware(['web'])->group(function () {
    Route::get('/npsn-lookup', [NpsnLookupController::class, 'lookup'])->name('npsn.lookup');
    Route::get('/registrasi-sekolah', [SekolahRegistrationController::class, 'create'])->name('sekolah.register');
    Route::post('/registrasi-sekolah', [SekolahRegistrationController::class, 'store']);
});

require __DIR__.'/auth.php';

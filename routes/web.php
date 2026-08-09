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


// Profil Sekolah - bisa diedit oleh admin sekolah (nama, alamat, dll),
// NPSN tetap read-only karena itu identitas resmi dari Dapodik.
Route::middleware(['web', 'auth', 'admin'])->group(function () {
    Route::get('/profil-sekolah', [App\Http\Controllers\SekolahProfilController::class, 'edit'])->name('sekolah.profil.edit');
    Route::put('/profil-sekolah', [App\Http\Controllers\SekolahProfilController::class, 'update'])->name('sekolah.profil.update');
});

// Beranda portal setelah login.
Route::middleware(['web', 'auth'])->get('/dashboard', [App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');

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
require __DIR__.'/kepegawaian.php';
require __DIR__.'/bk.php';
require __DIR__.'/erapor.php';
require __DIR__.'/pengguna.php';
require __DIR__.'/superadmin.php';
require __DIR__.'/manajemen-sekolah.php';
require __DIR__.'/sarpras.php';

Route::middleware(['web', 'auth'])->prefix('server-ujian')->name('server-ujian.')->group(function () {
    Route::get('/', [\App\Http\Controllers\ServerUjianController::class, 'index'])->name('index');
    Route::post('/request', [\App\Http\Controllers\ServerUjianController::class, 'ajukanRequest'])->name('request');
    Route::get('/{instance}/auto-login', [\App\Http\Controllers\ServerUjianController::class, 'autoLogin'])->name('auto-login');
    Route::post('/{instance}/run', [\App\Http\Controllers\ServerUjianController::class, 'run'])->name('run');
    Route::post('/{instance}/sinkron-siswa', [\App\Http\Controllers\ServerUjianController::class, 'sinkronSiswa'])->name('sinkron-siswa');
    Route::post('/{instance}/stop', [\App\Http\Controllers\ServerUjianController::class, 'stop'])->name('stop');
});

// Portal Siswa - login pakai NISN + tanggal lahir, TANPA akun admin
Route::prefix('siswa')->name('siswa-portal.')->group(function () {
    Route::get('/login', [\App\Http\Controllers\SiswaPortalController::class, 'showLogin'])->name('login');
    Route::post('/login', [\App\Http\Controllers\SiswaPortalController::class, 'login'])->name('login.submit');
    Route::get('/logout', [\App\Http\Controllers\SiswaPortalController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [\App\Http\Controllers\SiswaPortalController::class, 'dashboard'])->name('dashboard');
    Route::get('/{nisn}/qr/{kode}', [\App\Http\Controllers\SiswaPortalController::class, 'lihatViaQr'])->name('qr');
});
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

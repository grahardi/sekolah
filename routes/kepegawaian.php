<?php

use App\Http\Controllers\PegawaiController;
use App\Http\Controllers\PegawaiRiwayatController;
use Illuminate\Support\Facades\Route;

// Modul Kepegawaian. Sama seperti Buku Induk: login pakai Breeze yang sudah
// ada, bukan sistem terpisah. Multi-sekolah otomatis lewat global scope di
// model Pegawai (pola sama persis dengan Siswa).
//
// PENTING: route /laporan/* dan /{pegawai} bisa bentrok kalau urutan salah -
// Laravel mencocokkan route dari atas ke bawah, jadi /laporan/duk HARUS
// didaftarkan SEBELUM /{pegawai} supaya tidak dianggap sebagai ID pegawai.
Route::middleware(['web', 'auth'])->prefix('kepegawaian')->name('pegawai.')->group(function () {
    Route::get('/', [PegawaiController::class, 'index'])->name('index');

    // Laporan (read-only, dihitung otomatis dari data pegawai) - didaftarkan
    // duluan sebelum /{pegawai} biar tidak ketiban route model binding.
    Route::get('/laporan/duk', [PegawaiController::class, 'duk'])->name('duk');
    Route::get('/laporan/kendali-pangkat', [PegawaiController::class, 'kendaliPangkat'])->name('kendali-pangkat');
    Route::get('/laporan/gaji-berkala', [PegawaiController::class, 'gajiBerkala'])->name('gaji-berkala');

    Route::middleware('admin')->group(function () {
        Route::get('/create', [PegawaiController::class, 'create'])->name('create');
        Route::post('/', [PegawaiController::class, 'store'])->name('store');
        Route::get('/{pegawai}/edit', [PegawaiController::class, 'edit'])->name('edit');
        Route::put('/{pegawai}', [PegawaiController::class, 'update'])->name('update');
        Route::delete('/{pegawai}', [PegawaiController::class, 'destroy'])->name('destroy');

        // Export
        Route::get('/export/pilih', [PegawaiController::class, 'exportChoice'])->name('export.choice');
        Route::get('/export/excel', [PegawaiController::class, 'exportExcel'])->name('export.excel');
        Route::get('/export/pdf', [PegawaiController::class, 'exportPdfAll'])->name('export.pdf');

        // Import
        Route::get('/import/form', [PegawaiController::class, 'showImport'])->name('import.form');
        Route::post('/import/process', [PegawaiController::class, 'import'])->name('import.process');
        Route::post('/import/dapodik', [PegawaiController::class, 'importDapodik'])->name('import.dapodik');
        Route::get('/import/template', [PegawaiController::class, 'downloadTemplate'])->name('import.template');
        Route::get('/import/errors/{token}', [PegawaiController::class, 'importErrors'])->name('import.errors');

        // Data tambahan per pegawai
        Route::post('/{pegawai}/pendidikan', [PegawaiRiwayatController::class, 'storePendidikan'])->name('pendidikan.store');
        Route::delete('/{pegawai}/pendidikan/{riwayat}', [PegawaiRiwayatController::class, 'destroyPendidikan'])->name('pendidikan.destroy');

        Route::post('/{pegawai}/keluarga', [PegawaiRiwayatController::class, 'storeKeluarga'])->name('keluarga.store');
        Route::delete('/{pegawai}/keluarga/{keluarga}', [PegawaiRiwayatController::class, 'destroyKeluarga'])->name('keluarga.destroy');

        Route::post('/{pegawai}/cuti', [PegawaiRiwayatController::class, 'storeCuti'])->name('cuti.store');
        Route::delete('/{pegawai}/cuti/{cuti}', [PegawaiRiwayatController::class, 'destroyCuti'])->name('cuti.destroy');

        Route::post('/{pegawai}/mutasi', [PegawaiRiwayatController::class, 'storeMutasi'])->name('mutasi.store');
        Route::delete('/{pegawai}/mutasi/{mutasi}', [PegawaiRiwayatController::class, 'destroyMutasi'])->name('mutasi.destroy');
    });

    // Detail pegawai (dilihat semua yang login, edit-nya dibatasi admin di
    // dalam halaman itu sendiri) - didaftarkan PALING BAWAH karena
    // /{pegawai} adalah wildcard yang bisa "menelan" path lain di atasnya.
    Route::get('/{pegawai}', [PegawaiController::class, 'show'])->name('show');
});

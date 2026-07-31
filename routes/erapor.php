<?php

use App\Http\Controllers\EraporController;
use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'auth'])->prefix('erapor')->name('erapor.')->group(function () {
    Route::get('/', [EraporController::class, 'index'])->name('index');

    Route::middleware('admin')->group(function () {
        Route::get('/tahun-ajaran', [EraporController::class, 'tahunAjaran'])->name('tahun-ajaran');
        Route::post('/tahun-ajaran', [EraporController::class, 'storeTahunAjaran'])->name('tahun-ajaran.store');
        Route::post('/tahun-ajaran/{tahunAjaran}/aktifkan', [EraporController::class, 'aktifkanTahunAjaran'])->name('tahun-ajaran.aktifkan');
        Route::delete('/tahun-ajaran/{tahunAjaran}', [EraporController::class, 'destroyTahunAjaran'])->name('tahun-ajaran.destroy');

        Route::get('/mata-pelajaran', [EraporController::class, 'mataPelajaran'])->name('mata-pelajaran');
        Route::post('/mata-pelajaran', [EraporController::class, 'storeMataPelajaran'])->name('mata-pelajaran.store');
        Route::delete('/mata-pelajaran/{mataPelajaran}', [EraporController::class, 'destroyMataPelajaran'])->name('mata-pelajaran.destroy');

        Route::get('/penugasan', [EraporController::class, 'penugasan'])->name('penugasan');

        Route::post('/wali-kelas', [EraporController::class, 'storeWaliKelas'])->name('wali-kelas.store');
        Route::delete('/wali-kelas/{waliKelas}', [EraporController::class, 'destroyWaliKelas'])->name('wali-kelas.destroy');

        Route::post('/guru-pengajar', [EraporController::class, 'storeGuruPengajar'])->name('guru-pengajar.store');
        Route::delete('/guru-pengajar/{guruPengajar}', [EraporController::class, 'destroyGuruPengajar'])->name('guru-pengajar.destroy');

        Route::post('/guru-ekstrakurikuler', [EraporController::class, 'storeGuruEkstrakurikuler'])->name('guru-ekstrakurikuler.store');
        Route::delete('/guru-ekstrakurikuler/{guruEkstrakurikuler}', [EraporController::class, 'destroyGuruEkstrakurikuler'])->name('guru-ekstrakurikuler.destroy');

        Route::post('/guru-kokurikuler', [EraporController::class, 'storeGuruKokurikuler'])->name('guru-kokurikuler.store');
        Route::delete('/guru-kokurikuler/{guruKokurikuler}', [EraporController::class, 'destroyGuruKokurikuler'])->name('guru-kokurikuler.destroy');

        // Roster Guru (Kepegawaian + Guru Bantu non-Kepegawaian)
        Route::get('/guru', [EraporController::class, 'guruIndex'])->name('guru.index');
        Route::post('/guru/bantu', [EraporController::class, 'storeGuruBantu'])->name('guru.store-bantu');
        Route::delete('/guru/{guru}/bantu', [EraporController::class, 'destroyGuruBantu'])->name('guru.destroy-bantu');

        // Tugas Mengajar (toggle grid), diakses langsung dari E-Rapor atau dari
        // halaman detail Pegawai di Kepegawaian (via redirect find-or-create).
        Route::get('/dari-pegawai/{pegawai}/tugas-mengajar', [EraporController::class, 'tugasMengajarDariPegawai'])->name('tugas-mengajar.dari-pegawai');
        Route::get('/guru/{guru}/tugas-mengajar', [EraporController::class, 'tugasMengajarPage'])->name('guru.tugas-mengajar');
        Route::get('/guru/{guru}/tugas-mengajar/data', [EraporController::class, 'tugasMengajarData'])->name('tugas-mengajar.data');
        Route::post('/guru/{guru}/tugas-mengajar/toggle', [EraporController::class, 'tugasMengajarToggle'])->name('tugas-mengajar.toggle');
    });
});

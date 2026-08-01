<?php

use App\Http\Controllers\EraporController;
use App\Http\Controllers\PenilaianController;
use App\Http\Controllers\RaporController;
use App\Http\Controllers\TujuanPembelajaranController;
use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'auth'])->prefix('erapor')->name('erapor.')->group(function () {
    Route::get('/', [EraporController::class, 'index'])->name('index');

    // ── Khusus Admin sekolah (master data & penugasan) ──────────────────
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
        Route::get('/guru/{guru}/login-sebagai', [EraporController::class, 'loginSebagaiGuru'])->name('guru.login-sebagai');

        Route::get('/dari-pegawai/{pegawai}/tugas-mengajar', [EraporController::class, 'tugasMengajarDariPegawai'])->name('tugas-mengajar.dari-pegawai');
    });

    // Admin BISA kembali dari impersonate guru - tidak boleh dibatasi 'admin'
    // middleware krn saat impersonate, sesi aktif adalah akun guru.
    Route::get('/kembali-admin', [EraporController::class, 'kembaliKeAdmin'])->name('kembali-admin');

    // ── Admin ATAU Guru (guru login sendiri butuh akses ini) ────────────
    Route::middleware('admin_or_guru')->group(function () {
        // Tujuan Pembelajaran (TP)
        Route::get('/tp', [TujuanPembelajaranController::class, 'index'])->name('tp.index');
        Route::get('/tp/create', [TujuanPembelajaranController::class, 'create'])->name('tp.create');
        Route::post('/tp', [TujuanPembelajaranController::class, 'store'])->name('tp.store');
        Route::delete('/tp/{tp}', [TujuanPembelajaranController::class, 'destroy'])->name('tp.destroy');

        // Penilaian & Input Nilai
        Route::get('/penilaian', [PenilaianController::class, 'index'])->name('penilaian.index');
        Route::get('/penilaian/create', [PenilaianController::class, 'create'])->name('penilaian.create');
        Route::get('/penilaian/tp-untuk-konteks', [PenilaianController::class, 'tpUntukKonteks'])->name('penilaian.tp-untuk-konteks');
        Route::post('/penilaian', [PenilaianController::class, 'store'])->name('penilaian.store');
        Route::get('/penilaian/{penilaian}', [PenilaianController::class, 'show'])->name('penilaian.show');
        Route::post('/penilaian/{penilaian}/nilai', [PenilaianController::class, 'saveNilai'])->name('penilaian.save-nilai');
        Route::delete('/penilaian/{penilaian}', [PenilaianController::class, 'destroy'])->name('penilaian.destroy');

        // Cetak Rapor
        Route::get('/rapor', [RaporController::class, 'index'])->name('rapor.index');
        Route::post('/rapor/generate', [RaporController::class, 'generateKelas'])->name('rapor.generate');
        Route::get('/rapor/{rapor}/edit', [RaporController::class, 'edit'])->name('rapor.edit');
        Route::put('/rapor/{rapor}', [RaporController::class, 'update'])->name('rapor.update');
        Route::get('/rapor/{rapor}/cetak', [RaporController::class, 'cetak'])->name('rapor.cetak');

        // Tugas Mengajar (toggle grid) - guru lihat/atur tugas mengajarnya sendiri
        Route::get('/guru/{guru}/tugas-mengajar', [EraporController::class, 'tugasMengajarPage'])->name('guru.tugas-mengajar');
        Route::get('/guru/{guru}/tugas-mengajar/data', [EraporController::class, 'tugasMengajarData'])->name('tugas-mengajar.data');
        Route::post('/guru/{guru}/tugas-mengajar/toggle', [EraporController::class, 'tugasMengajarToggle'])->name('tugas-mengajar.toggle');
    });
});

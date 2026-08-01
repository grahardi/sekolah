<?php

use App\Http\Controllers\EraporController;
use App\Http\Controllers\KokurikulerController;
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
        Route::post('/tahun-ajaran/{tahunAjaran}/nonaktifkan', [EraporController::class, 'nonaktifkanTahunAjaran'])->name('tahun-ajaran.nonaktifkan');
        Route::get('/tahun-ajaran/{tahunAjaran}/edit', [EraporController::class, 'editTahunAjaran'])->name('tahun-ajaran.edit');
        Route::put('/tahun-ajaran/{tahunAjaran}', [EraporController::class, 'updateTahunAjaran'])->name('tahun-ajaran.update');

        Route::get('/mata-pelajaran', [EraporController::class, 'mataPelajaran'])->name('mata-pelajaran');
        Route::post('/mata-pelajaran', [EraporController::class, 'storeMataPelajaran'])->name('mata-pelajaran.store');
        Route::delete('/mata-pelajaran/{mataPelajaran}', [EraporController::class, 'destroyMataPelajaran'])->name('mata-pelajaran.destroy');

        Route::get('/pengaturan-cetak', [EraporController::class, 'pengaturanCetak'])->name('pengaturan-cetak');
        Route::put('/pengaturan-cetak', [EraporController::class, 'updatePengaturanCetak'])->name('pengaturan-cetak.update');

        Route::get('/kokurikuler', [KokurikulerController::class, 'index'])->name('kokurikuler.index');
        Route::get('/kokurikuler/create', [KokurikulerController::class, 'create'])->name('kokurikuler.create');
        Route::post('/kokurikuler', [KokurikulerController::class, 'store'])->name('kokurikuler.store');
        Route::get('/kokurikuler/{kegiatan}/edit', [KokurikulerController::class, 'edit'])->name('kokurikuler.edit');
        Route::put('/kokurikuler/{kegiatan}', [KokurikulerController::class, 'update'])->name('kokurikuler.update');
        Route::delete('/kokurikuler/{kegiatan}', [KokurikulerController::class, 'destroy'])->name('kokurikuler.destroy');
        Route::get('/kokurikuler/deskripsi-otomatis', [KokurikulerController::class, 'deskripsiOtomatis'])->name('kokurikuler.deskripsi-otomatis');

        Route::get('/penugasan', [EraporController::class, 'penugasan'])->name('penugasan');
        Route::get('/rekap-pengajar', [EraporController::class, 'rekapPengajar'])->name('rekap-pengajar');

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

        // Generate User massal
        Route::get('/guru/generate-user', [EraporController::class, 'generateUserIndex'])->name('guru.generate-user');
        Route::post('/guru/generate-user-massal', [EraporController::class, 'generateUserMassal'])->name('guru.generate-user-massal');
        Route::get('/guru/template-user', [EraporController::class, 'downloadTemplateUser'])->name('guru.template-user');
        Route::post('/guru/import-user', [EraporController::class, 'importUser'])->name('guru.import-user');
        Route::get('/guru/export-user', [EraporController::class, 'exportUser'])->name('guru.export-user');

        Route::get('/dari-pegawai/{pegawai}/tugas-mengajar', [EraporController::class, 'tugasMengajarDariPegawai'])->name('tugas-mengajar.dari-pegawai');
    });

    // Admin BISA kembali dari impersonate guru - tidak boleh dibatasi 'admin'
    // middleware krn saat impersonate, sesi aktif adalah akun guru.
    Route::get('/kembali-admin', [EraporController::class, 'kembaliKeAdmin'])->name('kembali-admin');

    // ── Admin ATAU Guru (guru login sendiri butuh akses ini) ────────────
    Route::middleware('admin_or_guru')->group(function () {
        // Kokurikuler - Input Asesmen (koordinator guru bisa akses)
        Route::get('/kokurikuler/pilih-asesmen', [KokurikulerController::class, 'pilihAsesmen'])->name('kokurikuler.pilih-asesmen');
        Route::post('/kokurikuler/save-asesmen', [KokurikulerController::class, 'saveAsesmen'])->name('kokurikuler.save-asesmen');

        // Tujuan Pembelajaran (TP)
        Route::get('/tp', [TujuanPembelajaranController::class, 'index'])->name('tp.index');
        Route::get('/tp/create', [TujuanPembelajaranController::class, 'create'])->name('tp.create');
        Route::post('/tp', [TujuanPembelajaranController::class, 'store'])->name('tp.store');
        Route::delete('/tp/massal', [TujuanPembelajaranController::class, 'destroyMassal'])->name('tp.destroy-massal');
        Route::get('/tp/{tp}/edit', [TujuanPembelajaranController::class, 'edit'])->name('tp.edit');
        Route::put('/tp/{tp}', [TujuanPembelajaranController::class, 'update'])->name('tp.update');
        Route::get('/tp/{tp}/penugasan-kelas', [TujuanPembelajaranController::class, 'penugasanKelas'])->name('tp.penugasan-kelas');
        Route::put('/tp/{tp}/penugasan-kelas', [TujuanPembelajaranController::class, 'updatePenugasanKelas'])->name('tp.update-penugasan-kelas');
        Route::delete('/tp/{tp}', [TujuanPembelajaranController::class, 'destroy'])->name('tp.destroy');

        // Penilaian & Input Nilai
        Route::get('/penilaian', [PenilaianController::class, 'index'])->name('penilaian.index');
        Route::get('/penilaian/create', [PenilaianController::class, 'create'])->name('penilaian.create');
        Route::get('/penilaian/tp-untuk-konteks', [PenilaianController::class, 'tpUntukKonteks'])->name('penilaian.tp-untuk-konteks');
        Route::get('/penilaian/template-kelas', [PenilaianController::class, 'downloadTemplateKelas'])->name('penilaian.template-kelas');
        Route::post('/penilaian/import-kelas', [PenilaianController::class, 'importTemplateKelas'])->name('penilaian.import-kelas');
        Route::post('/penilaian', [PenilaianController::class, 'store'])->name('penilaian.store');
        Route::get('/penilaian/{penilaian}', [PenilaianController::class, 'show'])->name('penilaian.show');
        Route::get('/penilaian/{penilaian}/template-nilai', [PenilaianController::class, 'downloadTemplateNilai'])->name('penilaian.template-nilai');
        Route::post('/penilaian/{penilaian}/import-nilai', [PenilaianController::class, 'importNilai'])->name('penilaian.import-nilai');
        Route::post('/penilaian/{penilaian}/nilai', [PenilaianController::class, 'saveNilai'])->name('penilaian.save-nilai');
        Route::delete('/penilaian/{penilaian}', [PenilaianController::class, 'destroy'])->name('penilaian.destroy');

        // Cetak Rapor
        Route::get('/rapor', [RaporController::class, 'index'])->name('rapor.index');
        Route::post('/rapor/generate', [RaporController::class, 'generateKelas'])->name('rapor.generate');
        Route::get('/rapor/template-absensi', [RaporController::class, 'downloadTemplateAbsensi'])->name('rapor.template-absensi');
        Route::post('/rapor/import-absensi', [RaporController::class, 'importAbsensi'])->name('rapor.import-absensi');
        Route::get('/rapor/cetak-kelas', [RaporController::class, 'cetakKelas'])->name('rapor.cetak-kelas');
        Route::post('/rapor/finalisasi-semua', [RaporController::class, 'finalisasiSemua'])->name('rapor.finalisasi-semua');
        Route::post('/rapor/batalkan-finalisasi-semua', [RaporController::class, 'batalkanFinalisasiSemua'])->name('rapor.batalkan-finalisasi-semua');
        Route::get('/rapor/{rapor}/edit', [RaporController::class, 'edit'])->name('rapor.edit');
        Route::get('/rapor/{rapor}/catatan-otomatis', [RaporController::class, 'catatanOtomatis'])->name('rapor.catatan-otomatis');
        Route::put('/rapor/{rapor}', [RaporController::class, 'update'])->name('rapor.update');
        Route::get('/rapor/{rapor}/cetak', [RaporController::class, 'cetak'])->name('rapor.cetak');

        // Wali Kelas
        Route::get('/progres-penilaian', [EraporController::class, 'progresPenilaian'])->name('progres-penilaian');
        Route::get('/catatan-wali', [EraporController::class, 'catatanWaliIndex'])->name('catatan-wali.index');
        Route::post('/catatan-wali', [EraporController::class, 'catatanWaliStore'])->name('catatan-wali.store');

        // Tugas Mengajar (toggle grid) - guru lihat/atur tugas mengajarnya sendiri
        Route::get('/guru/{guru}/tugas-mengajar', [EraporController::class, 'tugasMengajarPage'])->name('guru.tugas-mengajar');
        Route::get('/guru/{guru}/tugas-mengajar/data', [EraporController::class, 'tugasMengajarData'])->name('tugas-mengajar.data');
        Route::post('/guru/{guru}/tugas-mengajar/toggle', [EraporController::class, 'tugasMengajarToggle'])->name('tugas-mengajar.toggle');
    });
});

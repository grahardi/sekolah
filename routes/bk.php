<?php

use App\Http\Controllers\SurveyController;
use App\Http\Controllers\SurveyPesertaController;
use App\Http\Controllers\SurveyPublicController;
use Illuminate\Support\Facades\Route;

// Modul Program BK - dimulai dari fitur Survey (DCM/AUM/Custom), Catatan
// Konseling menyusul di iterasi berikutnya. Sisi guru butuh login, sisi
// siswa (isi survey) sengaja TANPA login - siswa cukup akses link yang
// dibagikan guru dan pilih namanya dari daftar kelas target.
Route::middleware(['web', 'auth'])->prefix('bk')->name('bk.')->group(function () {
    Route::get('/', function () { return redirect()->route('bk.survey.index'); });

    Route::prefix('survey')->name('survey.')->group(function () {
        Route::get('/', [SurveyController::class, 'index'])->name('index');

        Route::middleware('admin')->group(function () {
            Route::get('/create', [SurveyController::class, 'create'])->name('create');
            Route::post('/', [SurveyController::class, 'store'])->name('store');
            Route::get('/{survey}/edit', [SurveyController::class, 'edit'])->name('edit');
            Route::put('/{survey}', [SurveyController::class, 'update'])->name('update');
            Route::delete('/{survey}', [SurveyController::class, 'destroy'])->name('destroy');
        });

        // Wildcard /{survey} PALING BAWAH - kalau didaftarkan di atas /create,
        // Laravel akan mengira "create" adalah ID survey (bikin error bigint).
        Route::get('/{survey}', [SurveyController::class, 'show'])->name('show');
        Route::get('/{survey}/hasil/{siswa}', [SurveyController::class, 'hasilSiswa'])->name('hasil-siswa');
    });

    // Pilih Peserta / "Project" - assign survey ke kelas, terpisah dari
    // pembuatan survey, punya link publik sendiri per project.
    Route::middleware('admin')->prefix('peserta')->name('peserta.')->group(function () {
        Route::get('/', [SurveyPesertaController::class, 'index'])->name('index');
        Route::get('/create', [SurveyPesertaController::class, 'create'])->name('create');
        Route::post('/', [SurveyPesertaController::class, 'store'])->name('store');
        Route::get('/{peserta}/hasil/{siswa}', [SurveyPesertaController::class, 'hasilSiswa'])->name('hasil-siswa');
        Route::get('/{peserta}', [SurveyPesertaController::class, 'show'])->name('show');
        Route::delete('/{peserta}', [SurveyPesertaController::class, 'destroy'])->name('destroy');
    });
});

// Publik - siswa isi survey via link PROJECT (bukan link survey langsung),
// masukkan NISN dulu utk verifikasi, TANPA login sama sekali.
Route::middleware(['web'])->prefix('isi-survey')->name('survey.public.')->group(function () {
    Route::get('/{token}', [SurveyPublicController::class, 'showForm'])->name('form');
    Route::post('/{token}/verifikasi', [SurveyPublicController::class, 'verifikasiNisn'])->name('verifikasi');
    Route::post('/{token}', [SurveyPublicController::class, 'submit'])->name('submit');
});

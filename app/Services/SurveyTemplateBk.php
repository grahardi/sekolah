<?php

namespace App\Services;

class SurveyTemplateBk
{
    /**
     * Template pertanyaan DCM standar per jenjang. Dipakai untuk auto-buat
     * survey default begitu sekolah selesai registrasi (SekolahRegistrationController).
     * Kategori mengikuti bidang bimbingan klasik: Pribadi, Sosial, Belajar, Karir.
     */
    public static function forJenjang(string $bentukPendidikan): array
    {
        $jenjang = strtoupper(trim($bentukPendidikan));

        return match (true) {
            str_contains($jenjang, 'SD') => self::sd(),
            str_contains($jenjang, 'SMA') || str_contains($jenjang, 'MA') || str_contains($jenjang, 'SMK') => self::sma(),
            default => self::smp(), // default SMP kalau tidak cocok SD/SMA
        };
    }

    public static function judulUntuk(string $bentukPendidikan): string
    {
        $jenjang = strtoupper(trim($bentukPendidikan));
        return match (true) {
            str_contains($jenjang, 'SD') => 'DCM Awal Tahun - Jenjang SD',
            str_contains($jenjang, 'SMA') || str_contains($jenjang, 'MA') || str_contains($jenjang, 'SMK') => 'DCM Awal Tahun - Jenjang SMA/SMK',
            default => 'DCM Awal Tahun - Jenjang SMP',
        };
    }

    private static function sd(): array
    {
        return [
            ['kategori' => 'Pribadi', 'teks' => 'Saya sering merasa cemas atau takut tanpa alasan yang jelas.'],
            ['kategori' => 'Pribadi', 'teks' => 'Saya merasa kurang percaya diri saat tampil di depan kelas.'],
            ['kategori' => 'Pribadi', 'teks' => 'Saya suka menyendiri dan menghindari teman-teman.'],
            ['kategori' => 'Pribadi', 'teks' => 'Saya sering merasa sedih tanpa tahu sebabnya.'],
            ['kategori' => 'Sosial', 'teks' => 'Saya kesulitan berteman dengan teman sekelas.'],
            ['kategori' => 'Sosial', 'teks' => 'Saya pernah diejek atau diganggu oleh teman.'],
            ['kategori' => 'Sosial', 'teks' => 'Saya merasa tidak nyaman bermain kelompok.'],
            ['kategori' => 'Sosial', 'teks' => 'Saya sulit mengikuti aturan permainan bersama teman.'],
            ['kategori' => 'Belajar', 'teks' => 'Saya kesulitan memahami pelajaran di kelas.'],
            ['kategori' => 'Belajar', 'teks' => 'Saya sering lupa mengerjakan PR.'],
            ['kategori' => 'Belajar', 'teks' => 'Saya sulit berkonsentrasi saat belajar.'],
            ['kategori' => 'Belajar', 'teks' => 'Saya merasa pelajaran di sekolah terlalu sulit.'],
            ['kategori' => 'Belajar', 'teks' => 'Saya tidak suka membaca buku.'],
            ['kategori' => 'Keluarga', 'teks' => 'Saya merasa kurang diperhatikan oleh orang tua di rumah.'],
            ['kategori' => 'Keluarga', 'teks' => 'Saya sering bertengkar dengan saudara di rumah.'],
            ['kategori' => 'Kesehatan', 'teks' => 'Saya sering merasa sakit (pusing/sakit perut) di sekolah.'],
        ];
    }

    private static function smp(): array
    {
        return [
            ['kategori' => 'Pribadi', 'teks' => 'Saya sering merasa cemas atau khawatir berlebihan.'],
            ['kategori' => 'Pribadi', 'teks' => 'Saya merasa kurang percaya diri dengan penampilan saya.'],
            ['kategori' => 'Pribadi', 'teks' => 'Saya sulit mengendalikan emosi (marah/sedih) saya.'],
            ['kategori' => 'Pribadi', 'teks' => 'Saya sering merasa bosan dan tidak bersemangat.'],
            ['kategori' => 'Sosial', 'teks' => 'Saya kesulitan bergaul dengan teman-teman di sekolah.'],
            ['kategori' => 'Sosial', 'teks' => 'Saya pernah mengalami perundungan (bullying) di sekolah.'],
            ['kategori' => 'Sosial', 'teks' => 'Saya merasa sulit menyesuaikan diri dengan lingkungan baru.'],
            ['kategori' => 'Sosial', 'teks' => 'Saya lebih nyaman berkomunikasi lewat media sosial daripada bertatap muka.'],
            ['kategori' => 'Belajar', 'teks' => 'Saya kesulitan mengatur waktu belajar di rumah.'],
            ['kategori' => 'Belajar', 'teks' => 'Saya merasa nilai saya belum sesuai dengan usaha yang saya lakukan.'],
            ['kategori' => 'Belajar', 'teks' => 'Saya sulit berkonsentrasi saat guru menjelaskan pelajaran.'],
            ['kategori' => 'Belajar', 'teks' => 'Saya sering menunda-nunda mengerjakan tugas sekolah.'],
            ['kategori' => 'Belajar', 'teks' => 'Ada mata pelajaran tertentu yang menurut saya sangat sulit.'],
            ['kategori' => 'Karir', 'teks' => 'Saya belum tahu akan melanjutkan ke sekolah mana setelah lulus.'],
            ['kategori' => 'Karir', 'teks' => 'Saya belum mengetahui minat dan bakat saya sendiri.'],
            ['kategori' => 'Keluarga', 'teks' => 'Saya memiliki masalah dengan orang tua/keluarga di rumah.'],
            ['kategori' => 'Keluarga', 'teks' => 'Kondisi ekonomi keluarga membuat saya khawatir.'],
            ['kategori' => 'Kesehatan', 'teks' => 'Saya sering merasa lelah atau kurang tidur.'],
        ];
    }

    private static function sma(): array
    {
        return [
            ['kategori' => 'Pribadi', 'teks' => 'Saya sering merasa tertekan (stres) dengan tuntutan akademik.'],
            ['kategori' => 'Pribadi', 'teks' => 'Saya merasa kurang percaya diri dengan kemampuan saya sendiri.'],
            ['kategori' => 'Pribadi', 'teks' => 'Saya kesulitan mengelola emosi ketika menghadapi masalah.'],
            ['kategori' => 'Pribadi', 'teks' => 'Saya merasa kehilangan arah/tujuan hidup akhir-akhir ini.'],
            ['kategori' => 'Sosial', 'teks' => 'Saya kesulitan menjalin hubungan pertemanan yang sehat.'],
            ['kategori' => 'Sosial', 'teks' => 'Saya pernah mengalami konflik serius dengan teman/pacar.'],
            ['kategori' => 'Sosial', 'teks' => 'Saya merasa tertekan karena pengaruh media sosial/pergaulan.'],
            ['kategori' => 'Sosial', 'teks' => 'Saya kesulitan menolak ajakan negatif dari teman.'],
            ['kategori' => 'Belajar', 'teks' => 'Saya kesulitan mengatur waktu antara belajar dan kegiatan lain.'],
            ['kategori' => 'Belajar', 'teks' => 'Saya merasa metode belajar saya kurang efektif.'],
            ['kategori' => 'Belajar', 'teks' => 'Saya sulit fokus belajar karena banyak gangguan (gadget, dll).'],
            ['kategori' => 'Belajar', 'teks' => 'Saya khawatir tidak bisa mencapai target nilai/ujian.'],
            ['kategori' => 'Karir', 'teks' => 'Saya belum yakin dengan pilihan jurusan kuliah/karir ke depan.'],
            ['kategori' => 'Karir', 'teks' => 'Saya merasa bingung memilih antara kuliah, kerja, atau lainnya setelah lulus.'],
            ['kategori' => 'Karir', 'teks' => 'Saya belum memahami potensi dan minat diri saya secara jelas.'],
            ['kategori' => 'Keluarga', 'teks' => 'Saya memiliki masalah dengan orang tua terkait rencana masa depan saya.'],
            ['kategori' => 'Keluarga', 'teks' => 'Kondisi ekonomi keluarga mempengaruhi rencana pendidikan saya.'],
            ['kategori' => 'Kesehatan', 'teks' => 'Saya sering merasa lelah secara fisik maupun mental.'],
            ['kategori' => 'Kesehatan', 'teks' => 'Saya kesulitan tidur karena memikirkan banyak hal.'],
        ];
    }
}

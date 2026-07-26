<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class ModulAjarController extends Controller
{
    /**
     * Katalog Modul Ajar SMP Kurikulum Merdeka Kelas 7 (Fase D).
     *
     * Setiap entri sudah diverifikasi punya file DOC/DOCX asli yang bisa
     * diunduh langsung dari Google Drive (sumber: Modul Guruku, modulguruku.com),
     * BUKAN link karangan. `file_key` dipakai untuk mencocokkan dengan file lokal
     * di storage/app/public/modul-ajar/ setelah dijalankan lewat
     * scripts/download-modul-ajar.sh di server.
     *
     * Kalau file lokal sudah ada -> tombol unduh otomatis pakai file dari
     * server sendiri. Kalau belum -> fallback ke link Google Drive asli.
     * Tidak perlu ubah kode lagi setelah script download dijalankan.
     */
    private const CATALOG = [
        // ---------------- MATEMATIKA (Bab 1-7) ----------------
        ['mapel' => 'Matematika', 'kelas' => 7, 'title' => 'Bab 1: Bilangan Bulat', 'desc' => 'Bilangan positif dan negatif, penjumlahan-pengurangan, perkalian-pembagian.', 'file_key' => 'matematika-bab1-bilangan-bulat', 'drive_id' => '1a3GsyQeqoXCwURbWHhDQk1e10FX09z72'],
        ['mapel' => 'Matematika', 'kelas' => 7, 'title' => 'Bab 2: Aljabar', 'desc' => 'Aljabar dalam kalimat matematika dan menyederhanakan bentuk aljabar.', 'file_key' => 'matematika-bab2-aljabar', 'drive_id' => '1DdjMQDfEh2guJmzOUfRU8QyYgOqhWz8B'],
        ['mapel' => 'Matematika', 'kelas' => 7, 'title' => 'Bab 3: Persamaan Linear', 'desc' => 'Persamaan dan pertidaksamaan, penerapan persamaan linear satu variabel.', 'file_key' => 'matematika-bab3-persamaan-linear', 'drive_id' => '1okLpr-AkKG1o7eNrnyqTl-TYPFS91jKh'],
        ['mapel' => 'Matematika', 'kelas' => 7, 'title' => 'Bab 4: Perbandingan Senilai & Berbalik Nilai', 'desc' => 'Fungsi, perbandingan senilai, dan perbandingan berbalik nilai.', 'file_key' => 'matematika-bab4-perbandingan', 'drive_id' => '1Z2cpK7q9gqLy8zCqbz9DerrEkXG9z5xI'],
        ['mapel' => 'Matematika', 'kelas' => 7, 'title' => 'Bab 5: Bangun Datar', 'desc' => 'Sifat-sifat bangun datar, melukis garis-sudut, transformasi geometri.', 'file_key' => 'matematika-bab5-bangun-datar', 'drive_id' => '1Rb1mgXSwGVt1YS1ktCa2c1RhrAiFnDJG'],
        ['mapel' => 'Matematika', 'kelas' => 7, 'title' => 'Bab 6: Bangun Ruang', 'desc' => 'Sifat-sifat bangun ruang dan pengukurannya.', 'file_key' => 'matematika-bab6-bangun-ruang', 'drive_id' => '1QUhda9VMS5bxiRWXwQvR7mTGoP326cUt'],
        ['mapel' => 'Matematika', 'kelas' => 7, 'title' => 'Bab 7: Menggunakan Data', 'desc' => 'Menyelidiki kecenderungan data dan penyajian data statistik.', 'file_key' => 'matematika-bab7-data', 'drive_id' => '1o21RRI1tu9EO18UJ7yY7rV56Kl_GdSQr'],

        // ---------------- IPA (Bab 1-7) ----------------
        ['mapel' => 'IPA', 'kelas' => 7, 'title' => 'Bab 1: Hakikat Ilmu Sains dan Metode Ilmiah', 'desc' => 'Apa itu sains, laboratorium IPA, merancang percobaan, dan pengukuran.', 'file_key' => 'ipa-bab1-hakikat-sains', 'drive_id' => '1mXIFmyMlIfMoRXpQ_dZvs25ER1fjbWH_'],
        ['mapel' => 'IPA', 'kelas' => 7, 'title' => 'Bab 2: Zat dan Perubahannya', 'desc' => 'Wujud zat, model partikel, perubahan fisika dan kimia, kerapatan zat.', 'file_key' => 'ipa-bab2-zat-perubahannya', 'drive_id' => '1MdXvU6vKf1r-tKJCvZxdf0EqsYCzBjBr'],
        ['mapel' => 'IPA', 'kelas' => 7, 'title' => 'Bab 3: Suhu, Kalor, dan Pemuaian', 'desc' => 'Konsep suhu, kalor, dan pemuaian zat dalam kehidupan sehari-hari.', 'file_key' => 'ipa-bab3-suhu-kalor', 'drive_id' => '1atgnkFxzJxEunlHuhFk2eIXuHFWSoPCx'],
        ['mapel' => 'IPA', 'kelas' => 7, 'title' => 'Bab 4: Gerak dan Gaya', 'desc' => 'Gerak benda dan gaya yang mempengaruhinya.', 'file_key' => 'ipa-bab4-gerak-gaya', 'drive_id' => '1_PrQN8p2vuDjp_YgwqtTAWi7IZUjEG6i'],
        ['mapel' => 'IPA', 'kelas' => 7, 'title' => 'Bab 5: Klasifikasi Makhluk Hidup', 'desc' => 'Prinsip pengelompokan makhluk hidup dan keanekaragamannya.', 'file_key' => 'ipa-bab5-klasifikasi', 'drive_id' => '18XW3O6QY2-nV4wj6VUzgZWTRjHm2Uq0e'],
        ['mapel' => 'IPA', 'kelas' => 7, 'title' => 'Bab 6: Ekologi dan Keanekaragaman Hayati', 'desc' => 'Interaksi ekosistem, keanekaragaman hayati Indonesia, dan konservasi.', 'file_key' => 'ipa-bab6-ekologi', 'drive_id' => '1DE64UYxsoPM-QRy-68jsx556QEjqWtHj'],
        ['mapel' => 'IPA', 'kelas' => 7, 'title' => 'Bab 7: Bumi dan Tata Surya', 'desc' => 'Sistem tata surya, Bumi dan satelitnya, serta Matahari.', 'file_key' => 'ipa-bab7-tata-surya', 'drive_id' => '1kcrF0HNrJ6uM9kL5yxRRqnrA4SWUtJkS'],

        // ---------------- IPS (Tema 01-04) ----------------
        ['mapel' => 'IPS', 'kelas' => 7, 'title' => 'Tema 1: Keluarga Awal Kehidupan', 'desc' => 'Sejarah keluarga, lokasi wilayah, dan manusia sebagai makhluk sosial.', 'file_key' => 'ips-tema1-keluarga', 'drive_id' => '1RuajunOgmYwOCpO29J3NgzVfDyjDKtHD'],
        ['mapel' => 'IPS', 'kelas' => 7, 'title' => 'Tema 2: Keberagaman Lingkungan Sekitar', 'desc' => 'Pelestarian lingkungan, sejarah pra-sejarah, dan pembangunan berkelanjutan.', 'file_key' => 'ips-tema2-lingkungan', 'drive_id' => '1uYlKN2RETcG_uTCsO3QGtSSjXSbzaHym'],
        ['mapel' => 'IPS', 'kelas' => 7, 'title' => 'Tema 3: Potensi Ekonomi Lingkungan', 'desc' => 'Sumber daya alam, kegiatan ekonomi, dan pelaku ekonomi.', 'file_key' => 'ips-tema3-ekonomi', 'drive_id' => '1IKpNi641AmbuCunR3GnKd_45ts9xLJ9I'],
        ['mapel' => 'IPS', 'kelas' => 7, 'title' => 'Tema 4: Pemberdayaan Masyarakat', 'desc' => 'Keragaman sosial budaya dan pemberdayaan ekonomi masyarakat.', 'file_key' => 'ips-tema4-pemberdayaan', 'drive_id' => '1myJdS4xbqhc4FM4-Qfq-bVTHKb3IO1yR'],

        // ---------------- INFORMATIKA (Bab 1-9) ----------------
        ['mapel' => 'Informatika', 'kelas' => 7, 'title' => 'Bab 1: Informatika dan Keterampilan Generik', 'desc' => 'Pengantar informatika untuk pelajar SMP dan keterampilan generik.', 'file_key' => 'informatika-bab1-generik', 'drive_id' => '1TFUSL2WyIG4SkCcHIFwgyX-6_J7Aj5rx'],
        ['mapel' => 'Informatika', 'kelas' => 7, 'title' => 'Bab 2: Berpikir Komputasional', 'desc' => 'Algoritma, optimasi penjadwalan, struktur dan representasi data.', 'file_key' => 'informatika-bab2-komputasional', 'drive_id' => '1C_z5MF5Lhm7kowHM3Ew0n9MraCFLr5nS'],
        ['mapel' => 'Informatika', 'kelas' => 7, 'title' => 'Bab 3: Teknologi Informasi dan Komunikasi', 'desc' => 'Antarmuka pengguna, folder-file, peramban, surel, aplikasi perkantoran.', 'file_key' => 'informatika-bab3-tik', 'drive_id' => '1syCE0lZlSl9DElM3Mc99umdT0VBeTkZ-'],
        ['mapel' => 'Informatika', 'kelas' => 7, 'title' => 'Bab 4: Sistem Komputer', 'desc' => 'Perangkat keras, perangkat lunak, dan bilangan biner.', 'file_key' => 'informatika-bab4-sistem-komputer', 'drive_id' => '1p-7qXQZpKYmWvptXpM9IYE3E-FKWC1uh'],
        ['mapel' => 'Informatika', 'kelas' => 7, 'title' => 'Bab 5: Jaringan Komputer dan Internet', 'desc' => 'Pengantar jaringan, koneksi internet, dan proteksi data.', 'file_key' => 'informatika-bab5-jaringan', 'drive_id' => '1puI5LnjxS4diDstpXGNSvR9EvU5R_WK6'],
        ['mapel' => 'Informatika', 'kelas' => 7, 'title' => 'Bab 6: Analisis Data', 'desc' => 'Pengolahan data dengan lembar kerja, dasar dan lanjutan.', 'file_key' => 'informatika-bab6-analisis-data', 'drive_id' => '11C1BJ78m-XZrqb0UF_VRMuqSSCi7uwIj'],
        ['mapel' => 'Informatika', 'kelas' => 7, 'title' => 'Bab 7: Algoritma dan Pemrograman', 'desc' => 'Dasar pemrograman, eksplorasi fungsi, dan robot manual.', 'file_key' => 'informatika-bab7-algoritma', 'drive_id' => '16It1zRI5G97BqPfgX8V1M193Yb5ZniXT'],
        ['mapel' => 'Informatika', 'kelas' => 7, 'title' => 'Bab 8: Dampak Sosial Informatika', 'desc' => 'Kolaborasi dunia maya, media sosial, dan hukum privasi.', 'file_key' => 'informatika-bab8-dampak-sosial', 'drive_id' => '1JG8lVKTYWu0hP5J2wfdYYP267vrmcEwh'],
        ['mapel' => 'Informatika', 'kelas' => 7, 'title' => 'Bab 9: Praktik Lintas Bidang Informatika', 'desc' => 'Pengembangan artefak komputasional dan aktivitas unplugged.', 'file_key' => 'informatika-bab9-praktik', 'drive_id' => '1iMuZbGFFPeC_mcrsZYqWeJ05EmHGD50t'],

        // ---------------- PJOK (Unit 1-8) ----------------
        ['mapel' => 'PJOK', 'kelas' => 7, 'title' => 'Unit 1: Permainan Invasi - Bola Basket', 'desc' => 'Keterampilan gerak dan nilai karakter dalam permainan bola basket.', 'file_key' => 'pjok-unit1-basket', 'drive_id' => '1U2qI6IdU02WvTZprTBVRkv2W67SRfhyI'],
        ['mapel' => 'PJOK', 'kelas' => 7, 'title' => 'Unit 2: Permainan Net - Bola Voli', 'desc' => 'Keterampilan gerak dan nilai karakter dalam permainan bola voli.', 'file_key' => 'pjok-unit2-voli', 'drive_id' => '177aPjnQvbBJFHF5U4QqVc3tM78L1VP_O'],
        ['mapel' => 'PJOK', 'kelas' => 7, 'title' => 'Unit 3: Permainan Lapangan - Kasti', 'desc' => 'Keterampilan gerak dan nilai karakter dalam permainan kasti.', 'file_key' => 'pjok-unit3-kasti', 'drive_id' => '1YUxIrrqwmbROe2sWtvh9QstEOty4flE9'],
        ['mapel' => 'PJOK', 'kelas' => 7, 'title' => 'Unit 4: Bela Diri', 'desc' => 'Keterampilan gerak dan nilai karakter dalam bela diri.', 'file_key' => 'pjok-unit4-beladiri', 'drive_id' => '1BkWygwAJYQVJkqLD0Kh2uDXyt70EHkDT'],
        ['mapel' => 'PJOK', 'kelas' => 7, 'title' => 'Unit 5: Atletik', 'desc' => 'Keterampilan gerak dan nilai karakter dalam nomor atletik.', 'file_key' => 'pjok-unit5-atletik', 'drive_id' => '1yfO5z57mOg_EiUdNAd-ck-_lZVM9l8p9'],
        ['mapel' => 'PJOK', 'kelas' => 7, 'title' => 'Unit 6: Senam Lantai', 'desc' => 'Keterampilan gerak dan nilai karakter dalam senam lantai.', 'file_key' => 'pjok-unit6-senam-lantai', 'drive_id' => '1sZRZHRXNFZ_oeXqro6r1vQTZLQG9WbYf'],
        ['mapel' => 'PJOK', 'kelas' => 7, 'title' => 'Unit 7: Senam Irama', 'desc' => 'Keterampilan gerak dan nilai karakter dalam senam irama.', 'file_key' => 'pjok-unit7-senam-irama', 'drive_id' => '1FsN-02-1FNOtH2bA2zZAhCKgNlk3JyNs'],
        ['mapel' => 'PJOK', 'kelas' => 7, 'title' => 'Unit 8.1: Aktivitas Kebugaran untuk Kesehatan', 'desc' => 'Latihan kebugaran jasmani untuk menunjang kesehatan.', 'file_key' => 'pjok-unit8-1-kebugaran', 'drive_id' => '1Y2KqHidnMsTc1LfSXZ8szzn8j8WHZ1wZ'],
        ['mapel' => 'PJOK', 'kelas' => 7, 'title' => 'Unit 8.2: Pola Makan Sehat, Bergizi, dan Seimbang', 'desc' => 'Prinsip gizi seimbang untuk mendukung pola hidup sehat.', 'file_key' => 'pjok-unit8-2-gizi', 'drive_id' => '18cWfMhxM0uY9EpdaazyfsMEjtQ_ZADiW'],
    ];

    public function index(Request $request): Response
    {
        $modules = collect(self::CATALOG)
            ->map(function ($m) {
                $localPath = "modul-ajar/{$m['file_key']}.docx";
                $existsLocally = Storage::disk('public')->exists($localPath);

                $m['fase'] = 'D';
                $m['tipe'] = 'docx';
                $m['download_url'] = $existsLocally
                    ? Storage::disk('public')->url($localPath)
                    : "https://drive.google.com/uc?export=download&id={$m['drive_id']}";
                $m['sumber'] = $existsLocally ? 'Server sekolah.co.id' : 'Google Drive (Modul Guruku)';

                unset($m['drive_id'], $m['file_key']);
                return $m;
            })
            ->values();

        return Inertia::render('ModulAjar/Index', [
            'modules' => $modules,
            'mapelList' => collect(self::CATALOG)->pluck('mapel')->unique()->values(),
        ]);
    }
}

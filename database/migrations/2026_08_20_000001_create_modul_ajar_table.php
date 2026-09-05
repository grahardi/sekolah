<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('modul_ajar', function (Blueprint $table) {
            $table->id();
            $table->string('mapel');
            $table->unsignedTinyInteger('kelas')->default(7);
            $table->string('fase', 5)->default('D');
            $table->string('title');
            $table->text('deskripsi')->nullable();
            $table->string('file_key')->unique(); // dipakai cocokkan file lokal storage/app/public/modul-ajar/
            $table->string('drive_id')->nullable(); // fallback link Google Drive kalau file lokal blm ada
            $table->integer('urutan')->default(0);
            $table->boolean('aktif')->default(true);
            $table->timestamps();
        });

        // Seed dari data yg sudah ada di ModulAjarController::CATALOG (36 modul,
        // 5 mapel: Matematika, IPA, IPS, Informatika, PJOK) - biar SuperAdmin
        // langsung lihat & kelola data asli, gak kosong.
        $data = [
            ['mapel' => 'Matematika', 'kelas' => 7, 'fase' => 'D', 'title' => 'Bab 1: Bilangan Bulat', 'deskripsi' => 'Bilangan positif dan negatif, penjumlahan-pengurangan, perkalian-pembagian.', 'file_key' => 'matematika-bab1-bilangan-bulat', 'drive_id' => '1a3GsyQeqoXCwURbWHhDQk1e10FX09z72'],
            ['mapel' => 'Matematika', 'kelas' => 7, 'fase' => 'D', 'title' => 'Bab 2: Aljabar', 'deskripsi' => 'Aljabar dalam kalimat matematika dan menyederhanakan bentuk aljabar.', 'file_key' => 'matematika-bab2-aljabar', 'drive_id' => '1DdjMQDfEh2guJmzOUfRU8QyYgOqhWz8B'],
            ['mapel' => 'Matematika', 'kelas' => 7, 'fase' => 'D', 'title' => 'Bab 3: Persamaan Linear', 'deskripsi' => 'Persamaan dan pertidaksamaan, penerapan persamaan linear satu variabel.', 'file_key' => 'matematika-bab3-persamaan-linear', 'drive_id' => '1okLpr-AkKG1o7eNrnyqTl-TYPFS91jKh'],
            ['mapel' => 'Matematika', 'kelas' => 7, 'fase' => 'D', 'title' => 'Bab 4: Perbandingan Senilai & Berbalik Nilai', 'deskripsi' => 'Fungsi, perbandingan senilai, dan perbandingan berbalik nilai.', 'file_key' => 'matematika-bab4-perbandingan', 'drive_id' => '1Z2cpK7q9gqLy8zCqbz9DerrEkXG9z5xI'],
            ['mapel' => 'Matematika', 'kelas' => 7, 'fase' => 'D', 'title' => 'Bab 5: Bangun Datar', 'deskripsi' => 'Sifat-sifat bangun datar, melukis garis-sudut, transformasi geometri.', 'file_key' => 'matematika-bab5-bangun-datar', 'drive_id' => '1Rb1mgXSwGVt1YS1ktCa2c1RhrAiFnDJG'],
            ['mapel' => 'Matematika', 'kelas' => 7, 'fase' => 'D', 'title' => 'Bab 6: Bangun Ruang', 'deskripsi' => 'Sifat-sifat bangun ruang dan pengukurannya.', 'file_key' => 'matematika-bab6-bangun-ruang', 'drive_id' => '1QUhda9VMS5bxiRWXwQvR7mTGoP326cUt'],
            ['mapel' => 'Matematika', 'kelas' => 7, 'fase' => 'D', 'title' => 'Bab 7: Menggunakan Data', 'deskripsi' => 'Menyelidiki kecenderungan data dan penyajian data statistik.', 'file_key' => 'matematika-bab7-data', 'drive_id' => '1o21RRI1tu9EO18UJ7yY7rV56Kl_GdSQr'],
            ['mapel' => 'IPA', 'kelas' => 7, 'fase' => 'D', 'title' => 'Bab 1: Hakikat Ilmu Sains dan Metode Ilmiah', 'deskripsi' => 'Apa itu sains, laboratorium IPA, merancang percobaan, dan pengukuran.', 'file_key' => 'ipa-bab1-hakikat-sains', 'drive_id' => '1mXIFmyMlIfMoRXpQ_dZvs25ER1fjbWH_'],
            ['mapel' => 'IPA', 'kelas' => 7, 'fase' => 'D', 'title' => 'Bab 2: Zat dan Perubahannya', 'deskripsi' => 'Wujud zat, model partikel, perubahan fisika dan kimia, kerapatan zat.', 'file_key' => 'ipa-bab2-zat-perubahannya', 'drive_id' => '1MdXvU6vKf1r-tKJCvZxdf0EqsYCzBjBr'],
            ['mapel' => 'IPA', 'kelas' => 7, 'fase' => 'D', 'title' => 'Bab 3: Suhu, Kalor, dan Pemuaian', 'deskripsi' => 'Konsep suhu, kalor, dan pemuaian zat dalam kehidupan sehari-hari.', 'file_key' => 'ipa-bab3-suhu-kalor', 'drive_id' => '1atgnkFxzJxEunlHuhFk2eIXuHFWSoPCx'],
            ['mapel' => 'IPA', 'kelas' => 7, 'fase' => 'D', 'title' => 'Bab 4: Gerak dan Gaya', 'deskripsi' => 'Gerak benda dan gaya yang mempengaruhinya.', 'file_key' => 'ipa-bab4-gerak-gaya', 'drive_id' => '1_PrQN8p2vuDjp_YgwqtTAWi7IZUjEG6i'],
            ['mapel' => 'IPA', 'kelas' => 7, 'fase' => 'D', 'title' => 'Bab 5: Klasifikasi Makhluk Hidup', 'deskripsi' => 'Prinsip pengelompokan makhluk hidup dan keanekaragamannya.', 'file_key' => 'ipa-bab5-klasifikasi', 'drive_id' => '18XW3O6QY2-nV4wj6VUzgZWTRjHm2Uq0e'],
            ['mapel' => 'IPA', 'kelas' => 7, 'fase' => 'D', 'title' => 'Bab 6: Ekologi dan Keanekaragaman Hayati', 'deskripsi' => 'Interaksi ekosistem, keanekaragaman hayati Indonesia, dan konservasi.', 'file_key' => 'ipa-bab6-ekologi', 'drive_id' => '1DE64UYxsoPM-QRy-68jsx556QEjqWtHj'],
            ['mapel' => 'IPA', 'kelas' => 7, 'fase' => 'D', 'title' => 'Bab 7: Bumi dan Tata Surya', 'deskripsi' => 'Sistem tata surya, Bumi dan satelitnya, serta Matahari.', 'file_key' => 'ipa-bab7-tata-surya', 'drive_id' => '1kcrF0HNrJ6uM9kL5yxRRqnrA4SWUtJkS'],
            ['mapel' => 'IPS', 'kelas' => 7, 'fase' => 'D', 'title' => 'Tema 1: Keluarga Awal Kehidupan', 'deskripsi' => 'Sejarah keluarga, lokasi wilayah, dan manusia sebagai makhluk sosial.', 'file_key' => 'ips-tema1-keluarga', 'drive_id' => '1RuajunOgmYwOCpO29J3NgzVfDyjDKtHD'],
            ['mapel' => 'IPS', 'kelas' => 7, 'fase' => 'D', 'title' => 'Tema 2: Keberagaman Lingkungan Sekitar', 'deskripsi' => 'Pelestarian lingkungan, sejarah pra-sejarah, dan pembangunan berkelanjutan.', 'file_key' => 'ips-tema2-lingkungan', 'drive_id' => '1uYlKN2RETcG_uTCsO3QGtSSjXSbzaHym'],
            ['mapel' => 'IPS', 'kelas' => 7, 'fase' => 'D', 'title' => 'Tema 3: Potensi Ekonomi Lingkungan', 'deskripsi' => 'Sumber daya alam, kegiatan ekonomi, dan pelaku ekonomi.', 'file_key' => 'ips-tema3-ekonomi', 'drive_id' => '1IKpNi641AmbuCunR3GnKd_45ts9xLJ9I'],
            ['mapel' => 'IPS', 'kelas' => 7, 'fase' => 'D', 'title' => 'Tema 4: Pemberdayaan Masyarakat', 'deskripsi' => 'Keragaman sosial budaya dan pemberdayaan ekonomi masyarakat.', 'file_key' => 'ips-tema4-pemberdayaan', 'drive_id' => '1myJdS4xbqhc4FM4-Qfq-bVTHKb3IO1yR'],
            ['mapel' => 'Informatika', 'kelas' => 7, 'fase' => 'D', 'title' => 'Bab 1: Informatika dan Keterampilan Generik', 'deskripsi' => 'Pengantar informatika untuk pelajar SMP dan keterampilan generik.', 'file_key' => 'informatika-bab1-generik', 'drive_id' => '1TFUSL2WyIG4SkCcHIFwgyX-6_J7Aj5rx'],
            ['mapel' => 'Informatika', 'kelas' => 7, 'fase' => 'D', 'title' => 'Bab 2: Berpikir Komputasional', 'deskripsi' => 'Algoritma, optimasi penjadwalan, struktur dan representasi data.', 'file_key' => 'informatika-bab2-komputasional', 'drive_id' => '1C_z5MF5Lhm7kowHM3Ew0n9MraCFLr5nS'],
            ['mapel' => 'Informatika', 'kelas' => 7, 'fase' => 'D', 'title' => 'Bab 3: Teknologi Informasi dan Komunikasi', 'deskripsi' => 'Antarmuka pengguna, folder-file, peramban, surel, aplikasi perkantoran.', 'file_key' => 'informatika-bab3-tik', 'drive_id' => '1syCE0lZlSl9DElM3Mc99umdT0VBeTkZ-'],
            ['mapel' => 'Informatika', 'kelas' => 7, 'fase' => 'D', 'title' => 'Bab 4: Sistem Komputer', 'deskripsi' => 'Perangkat keras, perangkat lunak, dan bilangan biner.', 'file_key' => 'informatika-bab4-sistem-komputer', 'drive_id' => '1p-7qXQZpKYmWvptXpM9IYE3E-FKWC1uh'],
            ['mapel' => 'Informatika', 'kelas' => 7, 'fase' => 'D', 'title' => 'Bab 5: Jaringan Komputer dan Internet', 'deskripsi' => 'Pengantar jaringan, koneksi internet, dan proteksi data.', 'file_key' => 'informatika-bab5-jaringan', 'drive_id' => '1puI5LnjxS4diDstpXGNSvR9EvU5R_WK6'],
            ['mapel' => 'Informatika', 'kelas' => 7, 'fase' => 'D', 'title' => 'Bab 6: Analisis Data', 'deskripsi' => 'Pengolahan data dengan lembar kerja, dasar dan lanjutan.', 'file_key' => 'informatika-bab6-analisis-data', 'drive_id' => '11C1BJ78m-XZrqb0UF_VRMuqSSCi7uwIj'],
            ['mapel' => 'Informatika', 'kelas' => 7, 'fase' => 'D', 'title' => 'Bab 7: Algoritma dan Pemrograman', 'deskripsi' => 'Dasar pemrograman, eksplorasi fungsi, dan robot manual.', 'file_key' => 'informatika-bab7-algoritma', 'drive_id' => '16It1zRI5G97BqPfgX8V1M193Yb5ZniXT'],
            ['mapel' => 'Informatika', 'kelas' => 7, 'fase' => 'D', 'title' => 'Bab 8: Dampak Sosial Informatika', 'deskripsi' => 'Kolaborasi dunia maya, media sosial, dan hukum privasi.', 'file_key' => 'informatika-bab8-dampak-sosial', 'drive_id' => '1JG8lVKTYWu0hP5J2wfdYYP267vrmcEwh'],
            ['mapel' => 'Informatika', 'kelas' => 7, 'fase' => 'D', 'title' => 'Bab 9: Praktik Lintas Bidang Informatika', 'deskripsi' => 'Pengembangan artefak komputasional dan aktivitas unplugged.', 'file_key' => 'informatika-bab9-praktik', 'drive_id' => '1iMuZbGFFPeC_mcrsZYqWeJ05EmHGD50t'],
            ['mapel' => 'PJOK', 'kelas' => 7, 'fase' => 'D', 'title' => 'Unit 1: Permainan Invasi - Bola Basket', 'deskripsi' => 'Keterampilan gerak dan nilai karakter dalam permainan bola basket.', 'file_key' => 'pjok-unit1-basket', 'drive_id' => '1U2qI6IdU02WvTZprTBVRkv2W67SRfhyI'],
            ['mapel' => 'PJOK', 'kelas' => 7, 'fase' => 'D', 'title' => 'Unit 2: Permainan Net - Bola Voli', 'deskripsi' => 'Keterampilan gerak dan nilai karakter dalam permainan bola voli.', 'file_key' => 'pjok-unit2-voli', 'drive_id' => '177aPjnQvbBJFHF5U4QqVc3tM78L1VP_O'],
            ['mapel' => 'PJOK', 'kelas' => 7, 'fase' => 'D', 'title' => 'Unit 3: Permainan Lapangan - Kasti', 'deskripsi' => 'Keterampilan gerak dan nilai karakter dalam permainan kasti.', 'file_key' => 'pjok-unit3-kasti', 'drive_id' => '1YUxIrrqwmbROe2sWtvh9QstEOty4flE9'],
            ['mapel' => 'PJOK', 'kelas' => 7, 'fase' => 'D', 'title' => 'Unit 4: Bela Diri', 'deskripsi' => 'Keterampilan gerak dan nilai karakter dalam bela diri.', 'file_key' => 'pjok-unit4-beladiri', 'drive_id' => '1BkWygwAJYQVJkqLD0Kh2uDXyt70EHkDT'],
            ['mapel' => 'PJOK', 'kelas' => 7, 'fase' => 'D', 'title' => 'Unit 5: Atletik', 'deskripsi' => 'Keterampilan gerak dan nilai karakter dalam nomor atletik.', 'file_key' => 'pjok-unit5-atletik', 'drive_id' => '1yfO5z57mOg_EiUdNAd-ck-_lZVM9l8p9'],
            ['mapel' => 'PJOK', 'kelas' => 7, 'fase' => 'D', 'title' => 'Unit 6: Senam Lantai', 'deskripsi' => 'Keterampilan gerak dan nilai karakter dalam senam lantai.', 'file_key' => 'pjok-unit6-senam-lantai', 'drive_id' => '1sZRZHRXNFZ_oeXqro6r1vQTZLQG9WbYf'],
            ['mapel' => 'PJOK', 'kelas' => 7, 'fase' => 'D', 'title' => 'Unit 7: Senam Irama', 'deskripsi' => 'Keterampilan gerak dan nilai karakter dalam senam irama.', 'file_key' => 'pjok-unit7-senam-irama', 'drive_id' => '1FsN-02-1FNOtH2bA2zZAhCKgNlk3JyNs'],
            ['mapel' => 'PJOK', 'kelas' => 7, 'fase' => 'D', 'title' => 'Unit 8.1: Aktivitas Kebugaran untuk Kesehatan', 'deskripsi' => 'Latihan kebugaran jasmani untuk menunjang kesehatan.', 'file_key' => 'pjok-unit8-1-kebugaran', 'drive_id' => '1Y2KqHidnMsTc1LfSXZ8szzn8j8WHZ1wZ'],
            ['mapel' => 'PJOK', 'kelas' => 7, 'fase' => 'D', 'title' => 'Unit 8.2: Pola Makan Sehat, Bergizi, dan Seimbang', 'deskripsi' => 'Prinsip gizi seimbang untuk mendukung pola hidup sehat.', 'file_key' => 'pjok-unit8-2-gizi', 'drive_id' => '18cWfMhxM0uY9EpdaazyfsMEjtQ_ZADiW'],
        ];

        foreach ($data as $i => $row) {
            $row['urutan'] = $i;
            $row['created_at'] = now();
            $row['updated_at'] = now();
            DB::table('modul_ajar')->insert($row);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('modul_ajar');
    }
};

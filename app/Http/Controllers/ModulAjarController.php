<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ModulAjarController extends Controller
{
    /**
     * Katalog Modul Ajar SMP Kurikulum Merdeka (Fase D, kelas 7-9).
     *
     * `download_url` sengaja diarahkan ke Platform Merdeka Mengajar resmi
     * (guru.kemdikbud.go.id) karena dokumen Modul Ajar asli didistribusikan
     * lewat sana dan butuh akun belajar.id - bukan link PDF spesifik yang
     * dikarang. Begitu sekolah punya file PDF/DOCX sendiri (hasil unduhan
     * PMM atau buatan guru), tinggal ganti `download_url` per modul ke
     * link Google Drive atau path storage lokal (mis. /storage/modul-ajar/xxx.pdf).
     */
    private const PMM_URL = 'https://guru.kemdikbud.go.id/';

    private const CATALOG = [
        // ---------------- MATEMATIKA ----------------
        ['mapel' => 'Matematika', 'kelas' => 7, 'title' => 'Bilangan Bulat dan Pecahan', 'desc' => 'Operasi hitung bilangan bulat, pecahan, desimal, dan persentase dalam konteks sehari-hari.', 'tipe' => 'pdf'],
        ['mapel' => 'Matematika', 'kelas' => 7, 'title' => 'Himpunan', 'desc' => 'Konsep himpunan, operasi irisan-gabungan, dan diagram Venn.', 'tipe' => 'docx'],
        ['mapel' => 'Matematika', 'kelas' => 7, 'title' => 'Persamaan & Pertidaksamaan Linear Satu Variabel', 'desc' => 'Menyusun dan menyelesaikan persamaan/pertidaksamaan linear satu variabel.', 'tipe' => 'pdf'],
        ['mapel' => 'Matematika', 'kelas' => 8, 'title' => 'Teorema Pythagoras', 'desc' => 'Pembuktian dan penerapan teorema Pythagoras pada segitiga siku-siku.', 'tipe' => 'pdf'],
        ['mapel' => 'Matematika', 'kelas' => 9, 'title' => 'Statistika: Penyajian & Ukuran Data', 'desc' => 'Mengumpulkan, menyajikan, dan menganalisis data dengan ukuran pemusatan.', 'tipe' => 'docx'],

        // ---------------- IPA ----------------
        ['mapel' => 'IPA', 'kelas' => 7, 'title' => 'Klasifikasi Makhluk Hidup', 'desc' => 'Prinsip klasifikasi dan keanekaragaman makhluk hidup di sekitar.', 'tipe' => 'pdf'],
        ['mapel' => 'IPA', 'kelas' => 7, 'title' => 'Zat dan Perubahannya', 'desc' => 'Wujud zat, perubahan fisika-kimia, dan sifat campuran zat.', 'tipe' => 'pdf'],
        ['mapel' => 'IPA', 'kelas' => 8, 'title' => 'Sistem Organisasi Kehidupan', 'desc' => 'Struktur sel, jaringan, organ, hingga sistem organ pada makhluk hidup.', 'tipe' => 'docx'],
        ['mapel' => 'IPA', 'kelas' => 8, 'title' => 'Getaran, Gelombang, dan Bunyi', 'desc' => 'Konsep getaran dan gelombang serta penerapannya pada indera pendengaran.', 'tipe' => 'pdf'],
        ['mapel' => 'IPA', 'kelas' => 9, 'title' => 'Listrik Statis dan Dinamis', 'desc' => 'Muatan listrik, rangkaian sederhana, dan penerapan listrik dalam kehidupan.', 'tipe' => 'pdf'],

        // ---------------- IPS ----------------
        ['mapel' => 'IPS', 'kelas' => 7, 'title' => 'Interaksi Sosial dan Lembaga Sosial', 'desc' => 'Bentuk interaksi sosial dan fungsi lembaga sosial di masyarakat.', 'tipe' => 'docx'],
        ['mapel' => 'IPS', 'kelas' => 7, 'title' => 'Potensi Sumber Daya Alam Indonesia', 'desc' => 'Persebaran dan pemanfaatan sumber daya alam secara berkelanjutan.', 'tipe' => 'pdf'],
        ['mapel' => 'IPS', 'kelas' => 8, 'title' => 'Mobilitas Sosial dan Pluralitas Masyarakat', 'desc' => 'Faktor pendorong mobilitas sosial dan keberagaman masyarakat Indonesia.', 'tipe' => 'pdf'],
        ['mapel' => 'IPS', 'kelas' => 9, 'title' => 'Perdagangan Internasional & Globalisasi Ekonomi', 'desc' => 'Dampak globalisasi ekonomi dan perdagangan antarnegara bagi Indonesia.', 'tipe' => 'docx'],

        // ---------------- BAHASA INDONESIA ----------------
        ['mapel' => 'Bahasa Indonesia', 'kelas' => 7, 'title' => 'Teks Deskripsi', 'desc' => 'Menulis dan menganalisis teks deskripsi tentang objek di lingkungan sekitar.', 'tipe' => 'pdf'],
        ['mapel' => 'Bahasa Indonesia', 'kelas' => 7, 'title' => 'Teks Narasi: Cerita Fantasi', 'desc' => 'Struktur dan unsur kebahasaan teks narasi cerita fantasi.', 'tipe' => 'docx'],
        ['mapel' => 'Bahasa Indonesia', 'kelas' => 8, 'title' => 'Teks Eksposisi', 'desc' => 'Menyusun teks eksposisi dengan argumen dan data pendukung.', 'tipe' => 'pdf'],
        ['mapel' => 'Bahasa Indonesia', 'kelas' => 8, 'title' => 'Teks Puisi', 'desc' => 'Unsur pembangun puisi serta praktik menulis dan membacakan puisi.', 'tipe' => 'pdf'],
        ['mapel' => 'Bahasa Indonesia', 'kelas' => 9, 'title' => 'Teks Argumentasi dan Debat', 'desc' => 'Menyusun argumen logis dan praktik debat dengan tata krama berbahasa.', 'tipe' => 'docx'],

        // ---------------- BAHASA INGGRIS ----------------
        ['mapel' => 'Bahasa Inggris', 'kelas' => 7, 'title' => 'Descriptive Text: My Daily Life', 'desc' => 'Menulis dan mempresentasikan teks deskriptif tentang kehidupan sehari-hari.', 'tipe' => 'pdf'],
        ['mapel' => 'Bahasa Inggris', 'kelas' => 7, 'title' => 'Recount Text: Past Experience', 'desc' => 'Menceritakan pengalaman masa lalu menggunakan simple past tense.', 'tipe' => 'docx'],
        ['mapel' => 'Bahasa Inggris', 'kelas' => 8, 'title' => 'Procedure Text', 'desc' => 'Menyusun teks prosedur (resep/cara penggunaan) dalam bahasa Inggris.', 'tipe' => 'pdf'],
        ['mapel' => 'Bahasa Inggris', 'kelas' => 9, 'title' => 'Narrative Text: Folklore', 'desc' => 'Menganalisis dan menceritakan kembali cerita rakyat dalam bahasa Inggris.', 'tipe' => 'pdf'],

        // ---------------- PPKN ----------------
        ['mapel' => 'PPKn', 'kelas' => 7, 'title' => 'Norma dan Keadilan', 'desc' => 'Jenis-jenis norma dalam masyarakat dan penegakan keadilan.', 'tipe' => 'docx'],
        ['mapel' => 'PPKn', 'kelas' => 7, 'title' => 'Sejarah Perumusan Pancasila', 'desc' => 'Proses perumusan dan penetapan Pancasila sebagai dasar negara.', 'tipe' => 'pdf'],
        ['mapel' => 'PPKn', 'kelas' => 8, 'title' => 'Bhinneka Tunggal Ika', 'desc' => 'Makna persatuan dalam keberagaman suku, agama, ras, dan antargolongan.', 'tipe' => 'pdf'],
        ['mapel' => 'PPKn', 'kelas' => 9, 'title' => 'Bela Negara dan NKRI', 'desc' => 'Bentuk-bentuk usaha bela negara dan keutuhan Negara Kesatuan RI.', 'tipe' => 'docx'],

        // ---------------- PENDIDIKAN AGAMA ISLAM ----------------
        ['mapel' => 'Pendidikan Agama', 'kelas' => 7, 'title' => 'Akidah: Iman kepada Allah', 'desc' => 'Penguatan akidah melalui pemahaman sifat wajib dan mustahil bagi Allah.', 'tipe' => 'pdf'],
        ['mapel' => 'Pendidikan Agama', 'kelas' => 8, 'title' => 'Akhlak Terpuji dalam Kehidupan Sehari-hari', 'desc' => 'Penerapan akhlak terpuji (jujur, amanah, istiqomah) dalam pergaulan.', 'tipe' => 'docx'],
        ['mapel' => 'Pendidikan Agama', 'kelas' => 9, 'title' => 'Sejarah Dakwah Islam di Nusantara', 'desc' => 'Proses masuk dan berkembangnya Islam di wilayah Nusantara.', 'tipe' => 'pdf'],

        // ---------------- SENI BUDAYA ----------------
        ['mapel' => 'Seni Budaya', 'kelas' => 7, 'title' => 'Seni Rupa: Menggambar Model', 'desc' => 'Teknik dan prinsip menggambar model benda dua/tiga dimensi.', 'tipe' => 'pdf'],
        ['mapel' => 'Seni Budaya', 'kelas' => 8, 'title' => 'Seni Musik: Alat Musik Tradisional', 'desc' => 'Mengenal dan memainkan alat musik tradisional Indonesia secara sederhana.', 'tipe' => 'docx'],
        ['mapel' => 'Seni Budaya', 'kelas' => 9, 'title' => 'Seni Tari: Tari Kreasi Nusantara', 'desc' => 'Eksplorasi gerak dan penciptaan tari kreasi berbasis budaya lokal.', 'tipe' => 'pdf'],

        // ---------------- PJOK ----------------
        ['mapel' => 'PJOK', 'kelas' => 7, 'title' => 'Permainan Bola Besar: Sepak Bola & Bola Voli', 'desc' => 'Teknik dasar dan aturan permainan sepak bola serta bola voli.', 'tipe' => 'pdf'],
        ['mapel' => 'PJOK', 'kelas' => 8, 'title' => 'Atletik: Lari, Lompat, dan Lempar', 'desc' => 'Teknik dasar nomor-nomor atletik dan penerapannya dalam perlombaan.', 'tipe' => 'docx'],
        ['mapel' => 'PJOK', 'kelas' => 9, 'title' => 'Kebugaran Jasmani dan Pola Hidup Sehat', 'desc' => 'Komponen kebugaran jasmani serta penerapan pola hidup sehat.', 'tipe' => 'pdf'],

        // ---------------- INFORMATIKA ----------------
        ['mapel' => 'Informatika', 'kelas' => 7, 'title' => 'Berpikir Komputasional', 'desc' => 'Dekomposisi, pengenalan pola, abstraksi, dan algoritma dalam pemecahan masalah.', 'tipe' => 'pdf'],
        ['mapel' => 'Informatika', 'kelas' => 7, 'title' => 'Teknologi Informasi dan Komunikasi Dasar', 'desc' => 'Pengenalan perangkat keras, perangkat lunak, dan etika digital.', 'tipe' => 'docx'],
        ['mapel' => 'Informatika', 'kelas' => 8, 'title' => 'Algoritma dan Pemrograman Dasar', 'desc' => 'Menyusun algoritma sederhana dan pengenalan pemrograman blok.', 'tipe' => 'pdf'],
        ['mapel' => 'Informatika', 'kelas' => 9, 'title' => 'Dampak Sosial Informatika', 'desc' => 'Dampak positif-negatif teknologi informasi bagi individu dan masyarakat.', 'tipe' => 'docx'],
    ];

    public function index(Request $request): Response
    {
        $modules = collect(self::CATALOG)
            ->map(function ($m) {
                $m['fase'] = 'D';
                $m['download_url'] = self::PMM_URL;
                $m['sumber'] = 'Platform Merdeka Mengajar';
                return $m;
            })
            ->values();

        return Inertia::render('ModulAjar/Index', [
            'modules' => $modules,
            'mapelList' => collect(self::CATALOG)->pluck('mapel')->unique()->values(),
        ]);
    }
}

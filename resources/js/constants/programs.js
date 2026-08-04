// Data program sekolah - satu sumber dipakai bareng oleh dropdown navbar
// (PublicNavbar.jsx) dan halaman detail per program (ProgramDetail.jsx).
// Cuma "Buku Induk" yang aktif & punya info detail lengkap, sisanya placeholder
// "segera hadir" sampai programnya benar-benar dibangun.
export const PROGRAMS = [
    {
        slug: 'buku-induk',
        title: 'Buku Induk',
        status: 'Aktif',
        summary: 'Data induk siswa terintegrasi Dapodik, siap cetak.',
        detail: 'Import data siswa langsung dari file Dapodik asli (tanpa perlu ubah format), lengkap dengan biodata, data ayah/ibu/wali, riwayat kelas, hingga arsip berkas (KK, akta, ijazah). Cetak biodata dan kartu siswa langsung dalam format PDF. Import berkas massal (foto, KK, akta) juga didukung - tinggal cocokkan nama file dengan NIS/NISN siswa.',
        href: '/buku-induk',
        cta: 'Buka Buku Induk',
        demoHref: '/demo',
    },
    {
        slug: 'kepegawaian',
        title: 'Kepegawaian',
        status: 'Aktif',
        summary: 'Data pegawai, DUK, Kendali Pangkat, dan Gaji Berkala.',
        detail: 'Kelola data seluruh pegawai (PNS, PPPK, GTT, PTT, GTY, PTY) lengkap dengan riwayat pendidikan, tunjangan keluarga, cuti, dan mutasi. Laporan otomatis: Daftar Urut Kepangkatan, Kendali Pangkat (jatuh tempo kenaikan pangkat), dan Gaji Berkala - semua dihitung otomatis dari data yang sudah ada.',
        href: '/kepegawaian',
        cta: 'Buka Kepegawaian',
    },
    {
        slug: 'bimbingan-konseling',
        title: 'Bimbingan Konseling',
        status: 'Aktif',
        summary: 'Survey/asesmen siswa (DCM, AUM) dengan link isi mandiri.',
        detail: 'Buat survey atau asesmen (DCM, AUM, atau kuesioner custom), pilih target kelas dari data Buku Induk, lalu bagikan link ke siswa - siswa isi tanpa perlu login. Pantau progress siapa yang sudah/belum mengisi, dan lihat jawaban lengkap per siswa. Catatan Konseling menyusul di iterasi berikutnya.',
        href: '/bk',
        cta: 'Buka Program BK',
    },
    {
        slug: 'persuratan',
        title: 'Persuratan',
        status: 'Segera',
        summary: 'Surat masuk-keluar dan arsip digital sekolah.',
        detail: 'Sedang dikembangkan - akan mencakup pencatatan nomor surat otomatis, template surat resmi, dan pelacakan disposisi.',
    },
    {
        slug: 'e-rapor',
        title: 'E-Rapor',
        status: 'Aktif',
        summary: 'Wali kelas, guru pengajar, ekstrakurikuler, dan kokurikuler (P5).',
        detail: 'Kelola tahun ajaran dan mata pelajaran, tetapkan wali kelas per rombel, penugasan guru pengajar per mapel, guru pembina ekstrakurikuler, dan guru pembina kokurikuler (Projek P5) - semua terhubung ke data guru dari Kepegawaian dan data kelas dari Buku Induk. Input nilai & cetak rapor menyusul.',
        href: '/erapor',
        cta: 'Buka E-Rapor',
    },
    {
        slug: 'manajemen-sekolah',
        title: 'Manajemen Sekolah',
        status: 'Aktif',
        summary: 'Absensi harian siswa, data siswa & guru terpadu.',
        detail: 'Absensi harian per kelas, rekap bulanan, plus akses cepat ke data siswa (Buku Induk) dan guru (E-Rapor) dalam satu tempat. Modul tata tertib, BK, kebersihan kelas, dan peminjaman ruang menyusul.',
        href: '/manajemen-sekolah',
        cta: 'Buka Manajemen Sekolah',
    },
    {
        slug: 'program-ujian',
        title: 'Program Ujian',
        status: 'Segera',
        summary: 'Ujian online terjadwal dengan bank soal.',
        detail: 'Sedang dikembangkan - ujian online dengan bank soal, pengacakan otomatis, dan pengawasan digital.',
    },
];

export function findProgram(slug) {
    return PROGRAMS.find((p) => p.slug === slug);
}

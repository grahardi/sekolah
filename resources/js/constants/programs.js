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
        status: 'Segera',
        summary: 'Data guru & tenaga kependidikan dalam satu sistem.',
        detail: 'Sedang dikembangkan - akan mencakup data induk pegawai, riwayat jabatan, kepangkatan, dan sertifikasi guru.',
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
        status: 'Segera',
        summary: 'Pengolahan nilai dan rapor digital.',
        detail: 'Sedang dikembangkan - pengolahan nilai sesuai Kurikulum Merdeka, terhubung langsung dengan data siswa di Buku Induk.',
    },
    {
        slug: 'manajemen-sekolah',
        title: 'Manajemen Sekolah',
        status: 'Segera',
        summary: 'Jadwal, keuangan, dan administrasi umum.',
        detail: 'Sedang dikembangkan - akan mencakup penjadwalan pelajaran, pencatatan keuangan sekolah, dan administrasi umum lainnya.',
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

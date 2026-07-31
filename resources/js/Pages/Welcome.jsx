import { Link } from '@inertiajs/react';
import PublicNavbar from '../Components/PublicNavbar';
import PublicFooter from '../Components/PublicFooter';
import ShowcaseSlider from '../Components/ShowcaseSlider';
import { PROGRAMS as PROGRAM_LIST } from '../constants/programs';

const STATS = [
    { value: '60+', label: 'Simulasi Interaktif' },
    { value: '7', label: 'Program Sekolah' },
    { value: '24/7', label: 'Akses Belajar' },
    { value: '100%', label: 'Berbasis Web' },
];

const FEATURES = [
    { title: 'Terintegrasi Dapodik', desc: 'Import data siswa langsung dari file Dapodik asli, tanpa perlu ubah format apapun.', icon: LayersIcon },
    { title: 'Siap Cetak', desc: 'Cetak biodata, kartu siswa, dan dokumen lain langsung dari sistem dalam format PDF.', icon: BookIcon },
    { title: 'Simulasi Real-time', desc: 'Parameter sains bisa diubah langsung, hasilnya terlihat seketika di layar.', icon: BeakerIcon },
    { title: 'Akses Kapan Saja', desc: 'Berjalan langsung di browser, tanpa instalasi aplikasi tambahan.', icon: ClockIcon },
];

const SHOWCASE = [
    { title: 'Bandul Sederhana', subject: 'Fisika · Getaran', href: '/lab/bandul' },
    { title: 'Gerak Peluru', subject: 'Fisika · Kinematika', href: '/lab/gerak-peluru' },
    { title: 'Rangkaian Listrik Seri', subject: 'Fisika · Listrik', href: '/lab/rangkaian-listrik' },
];

// Placeholder slide showcase - ganti title/desc/background/href sesuai konten
// asli nanti (foto sekolah, testimoni guru, dokumentasi kegiatan, dll).
const HERO_SLIDES = [
    {
        type: 'hero',
        tag: 'Portal Sekolah Digital #1 untuk Kurikulum Merdeka',
        title: 'Sekolahmu, satu portal, nol ribet',
        desc: 'Buku Induk terintegrasi Dapodik, simulasi sains interaktif, modul ajar Kurikulum Merdeka, hingga E-Rapor dan ujian digital — semuanya terhubung dalam satu tempat.',
        background: 'linear-gradient(135deg, #1E293B, #2563EB)',
        buttons: [
            { label: 'Mulai Sekarang', href: '/registrasi-sekolah' },
            { label: 'Jelajahi Modul', href: '#program-sekolah', variant: 'secondary' },
        ],
    },
    {
        tag: 'Buku Induk',
        title: 'Data siswa langsung dari Dapodik, tanpa input ulang',
        desc: 'Import file Dapodik asli dalam hitungan detik - biodata, data ayah/ibu/wali, sampai riwayat kelas otomatis tersusun rapi.',
        background: 'linear-gradient(135deg, #1E293B, #2563EB)',
        href: '/buku-induk',
    },
    {
        tag: 'Lab Interaktif',
        title: '60+ simulasi sains yang bisa dicoba langsung di kelas',
        desc: 'Fisika, Matematika, dan Biologi - siswa bisa eksplorasi konsep abstrak jadi visual dan interaktif, langsung dari browser.',
        background: 'linear-gradient(135deg, #1E293B, #16A34A)',
        href: '/lab',
    },
    {
        tag: 'Modul Ajar',
        title: 'Perangkat ajar Kurikulum Merdeka, siap pakai',
        desc: 'Modul ajar per mata pelajaran untuk Fase D, tinggal unduh dan sesuaikan dengan kelasmu.',
        background: 'linear-gradient(135deg, #1E293B, #FBBF24)',
        href: '/modul-ajar',
    },
];

export default function Welcome({ canLogin = true, canRegister = true }) {
    return (
        <div className="min-h-screen bg-cream text-navy">
            <PublicNavbar canLogin={canLogin} canRegister={canRegister} />

            {/* Hero + Slider - jadi satu, slide pertama = headline utama */}
            <section id="home" className="max-w-6xl mx-auto px-6 lg:px-8 pt-6 lg:pt-8">
                <ShowcaseSlider slides={HERO_SLIDES} />
            </section>

            {/* Stats bar */}
            <section className="bg-teal text-cream">
                <div className="max-w-6xl mx-auto px-6 lg:px-8 py-10 grid grid-cols-2 lg:grid-cols-4 gap-6 text-center">
                    {STATS.map((s) => (
                        <div key={s.label}>
                            <p className="font-display font-700 text-3xl lg:text-4xl text-coral">{s.value}</p>
                            <p className="text-sm text-cream/70 mt-1">{s.label}</p>
                        </div>
                    ))}
                </div>
            </section>

            {/* Fitur unggulan */}
            <section className="max-w-6xl mx-auto px-6 lg:px-8 py-16 lg:py-20">
                <div className="text-center max-w-xl mx-auto mb-10">
                    <span className="text-xs font-mono uppercase tracking-wide text-teal">Kenapa Kami</span>
                    <h2 className="font-display font-700 text-2xl lg:text-3xl mt-2">Dibangun untuk kebutuhan belajar nyata</h2>
                </div>
                <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    {FEATURES.map((f) => (
                        <div key={f.title} className="rounded-2xl bg-white border border-navy/10 p-5">
                            <div className="h-10 w-10 rounded-lg bg-teal-light text-teal flex items-center justify-center mb-3">
                                <f.icon className="w-5 h-5" />
                            </div>
                            <h3 className="font-display font-600 text-navy">{f.title}</h3>
                            <p className="text-sm text-navy/60 mt-1.5">{f.desc}</p>
                        </div>
                    ))}
                </div>
            </section>

            {/* Modul Ajar (placeholder ringkas, siap diisi konten kurikulum) */}
            <section id="modul-ajar" className="bg-white border-y border-navy/10">
                <div className="max-w-6xl mx-auto px-6 lg:px-8 py-16 lg:py-20 grid grid-cols-1 lg:grid-cols-2 gap-10 items-center">
                    <div>
                        <span className="text-xs font-mono uppercase tracking-wide text-teal">Modul Ajar</span>
                        <h2 className="font-display font-700 text-2xl lg:text-3xl mt-2">
                            Perangkat ajar SMP Kurikulum Merdeka, siap unduh
                        </h2>
                        <p className="text-navy/60 mt-3">
                            40+ modul ajar Fase D (kelas 7-9) untuk 10 mata pelajaran, lengkap dengan
                            deskripsi dan pencarian per topik. Format PDF/DOCX.
                        </p>
                        <Link href="/modul-ajar" className="inline-flex items-center gap-2 mt-5 text-teal font-medium text-sm">
                            Lihat semua modul ajar &rarr;
                        </Link>
                    </div>
                    <div className="rounded-2xl bg-teal-light p-6 space-y-3">
                        {['Matematika · Teorema Pythagoras', 'Bahasa Indonesia · Teks Deskripsi', 'IPA · Sistem Organisasi Kehidupan'].map((m) => (
                            <div key={m} className="bg-white rounded-lg px-4 py-3 text-sm font-medium text-navy/80 shadow-sm">
                                {m}
                            </div>
                        ))}
                    </div>
                </div>
            </section>

            {/* Program Sekolah */}
            <section id="program-sekolah" className="max-w-5xl mx-auto px-6 lg:px-8 py-16 lg:py-20">
                <div className="text-center max-w-xl mx-auto mb-10">
                    <span className="text-xs font-mono uppercase tracking-wide text-teal">Program Sekolah</span>
                    <h2 className="font-display font-700 text-2xl lg:text-3xl mt-2">Satu akun, semua urusan sekolah beres</h2>
                    <p className="text-navy/60 mt-2">Klik salah satu program untuk lihat detailnya.</p>
                </div>
                <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                    {PROGRAM_LIST.map((p) => (
                        <a
                            key={p.slug}
                            href={`/program/${p.slug}`}
                            className="rounded-2xl bg-white border border-navy/10 p-5 block hover:shadow-md hover:-translate-y-0.5 transition-all"
                        >
                            <div className="flex items-center justify-between mb-2">
                                <h3 className="font-display font-600 text-navy">{p.title}</h3>
                                <span className={`text-[10px] font-mono uppercase tracking-wide rounded-full px-2 py-0.5 ${
                                    p.status === 'Aktif' ? 'bg-teal-light text-teal' : 'bg-navy/5 text-navy/40'
                                }`}>
                                    {p.status}
                                </span>
                            </div>
                            <p className="text-sm text-navy/60">{p.summary}</p>
                        </a>
                    ))}
                </div>
            </section>

            {/* Showcase */}
            <section id="showcase" className="bg-navy text-cream">
                <div className="max-w-6xl mx-auto px-6 lg:px-8 py-16 lg:py-20">
                    <div className="text-center max-w-xl mx-auto mb-10">
                        <span className="text-xs font-mono uppercase tracking-wide text-coral">Showcase</span>
                        <h2 className="font-display font-700 text-2xl lg:text-3xl mt-2 text-white">Simulasi yang bisa dicoba langsung</h2>
                    </div>

                    <div className="grid grid-cols-1 sm:grid-cols-3 gap-5">
                        {SHOWCASE.map((s) => (
                            <Link
                                key={s.title}
                                href={s.href}
                                className="rounded-2xl bg-white/5 border border-white/10 p-5 flex flex-col justify-between min-h-[130px] hover:bg-white/10 transition-colors"
                            >
                                <span className="text-xs font-mono text-cream/50">{s.subject}</span>
                                <div className="flex items-end justify-between mt-3">
                                    <h3 className="font-display font-600 text-white">{s.title}</h3>
                                    <span className="text-coral text-sm">&rarr;</span>
                                </div>
                            </Link>
                        ))}
                    </div>
                </div>
            </section>

            {/* CTA penutup */}
            <section className="max-w-6xl mx-auto px-6 lg:px-8 py-16 text-center">
                <h2 className="font-display font-700 text-2xl lg:text-3xl">Cukup masukkan NPSN, kami urus sisanya</h2>
                <p className="text-navy/60 mt-3 max-w-xl mx-auto">
                    Data sekolahmu terisi otomatis, Buku Induk siap diisi dari Dapodik,
                    dan Lab Interaktif langsung bisa dipakai hari ini juga.
                </p>
                <Link href="/registrasi-sekolah" className="inline-block mt-6 bg-coral text-navy font-medium rounded-lg px-6 py-3 text-sm hover:brightness-95">
                    Daftarkan Sekolahmu, Gratis
                </Link>
            </section>

            <PublicFooter />
        </div>
    );
}

/* Ikon inline ringan, tanpa dependency tambahan */
function BeakerIcon(p) { return <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" {...p}><path d="M9 3h6M10 3v6l-6 10a1 1 0 001 1.5h14a1 1 0 001-1.5L14 9V3" /></svg>; }
function BookIcon(p) { return <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" {...p}><path d="M4 19.5A2.5 2.5 0 016.5 17H20V4H6.5A2.5 2.5 0 004 6.5v13z" /></svg>; }
function LayersIcon(p) { return <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" {...p}><path d="M12 2l9 5-9 5-9-5 9-5zM3 12l9 5 9-5M3 17l9 5 9-5" /></svg>; }
function ClockIcon(p) { return <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" {...p}><circle cx="12" cy="12" r="9" /><path d="M12 7v5l3 3" /></svg>; }

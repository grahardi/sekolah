import { Link } from '@inertiajs/react';

const MODULES = [
    { title: 'Lab Interaktif', desc: 'Simulasi sains interaktif ala PhET — bandul, gerak peluru, rangkaian listrik, dan terus bertambah.', tag: 'Aktif' },
    { title: 'Server Ujian', desc: 'Ujian online terjadwal, bank soal, dan pengawasan otomatis.', tag: 'Segera' },
    { title: 'Buku Induk', desc: 'Data induk siswa dan arsip akademik digital.', tag: 'Segera' },
    { title: 'Program BK', desc: 'Pencatatan konseling dan perkembangan siswa.', tag: 'Segera' },
    { title: 'Manajemen Sekolah', desc: 'Jadwal, keuangan, dan administrasi umum sekolah.', tag: 'Segera' },
];

export default function Welcome({ canLogin = true, canRegister = true }) {
    return (
        <div className="min-h-screen bg-cream text-navy">
            <header className="flex items-center justify-between px-6 lg:px-10 py-5 border-b border-navy/10">
                <p className="font-display font-700 text-lg">
                    sekolah<span className="text-coral">.co.id</span>
                </p>
                <div className="flex items-center gap-3">
                    {canLogin && (
                        <Link href="/login" className="text-sm font-medium text-navy/80 hover:text-navy">
                            Masuk
                        </Link>
                    )}
                    {canRegister && (
                        <Link
                            href="/register"
                            className="text-sm font-medium bg-teal text-white rounded-lg px-4 py-2 hover:brightness-110"
                        >
                            Daftar
                        </Link>
                    )}
                </div>
            </header>

            <section className="px-6 lg:px-10 py-16 lg:py-24 max-w-4xl mx-auto text-center">
                <span className="inline-block text-xs font-mono uppercase tracking-wide text-teal bg-teal-light rounded-full px-3 py-1">
                    Portal Sekolah Terpadu
                </span>
                <h1 className="font-display font-700 text-3xl lg:text-5xl mt-5 leading-tight">
                    Satu portal untuk seluruh<br className="hidden lg:block" /> kegiatan akademik sekolah
                </h1>
                <p className="text-navy/60 mt-5 text-base lg:text-lg max-w-2xl mx-auto">
                    Dari eksplorasi sains interaktif di Lab, ujian online, hingga administrasi
                    Buku Induk dan BK — semuanya terhubung dalam satu tempat.
                </p>
                <div className="flex items-center justify-center gap-3 mt-8">
                    <Link
                        href="/register"
                        className="bg-coral text-navy font-medium rounded-lg px-5 py-3 text-sm hover:brightness-95"
                    >
                        Mulai Sekarang
                    </Link>
                    <Link
                        href="/login"
                        className="border border-navy/20 text-navy font-medium rounded-lg px-5 py-3 text-sm hover:bg-navy/5"
                    >
                        Saya sudah punya akun
                    </Link>
                </div>
            </section>

            <section className="px-6 lg:px-10 pb-20 max-w-5xl mx-auto">
                <h2 className="font-display font-600 text-xl text-center mb-8">Modul yang tersedia</h2>
                <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                    {MODULES.map((m) => (
                        <div key={m.title} className="rounded-2xl bg-white border border-navy/10 p-5">
                            <div className="flex items-center justify-between mb-2">
                                <h3 className="font-display font-600 text-navy">{m.title}</h3>
                                <span
                                    className={`text-[10px] font-mono uppercase tracking-wide rounded-full px-2 py-0.5 ${
                                        m.tag === 'Aktif' ? 'bg-teal-light text-teal' : 'bg-navy/5 text-navy/40'
                                    }`}
                                >
                                    {m.tag}
                                </span>
                            </div>
                            <p className="text-sm text-navy/60">{m.desc}</p>
                        </div>
                    ))}
                </div>
            </section>

            <footer className="border-t border-navy/10 px-6 lg:px-10 py-6 text-center text-xs text-navy/40">
                &copy; {new Date().getFullYear()} sekolah.co.id &middot; Portal Sekolah Terpadu
            </footer>
        </div>
    );
}

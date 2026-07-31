import { useState } from 'react';
import { Link } from '@inertiajs/react';
import PublicNavbar from '../Components/PublicNavbar';
import PublicFooter from '../Components/PublicFooter';

const DEMO_CREDENTIALS = {
    email: 'demo@sekolah.co.id',
    password: 'demo12345',
};

export default function DemoProgram({ canLogin = true, canRegister = true }) {
    const [copied, setCopied] = useState(null);

    const copy = (text, key) => {
        navigator.clipboard?.writeText(text);
        setCopied(key);
        setTimeout(() => setCopied(null), 1500);
    };

    return (
        <div className="min-h-screen bg-cream text-navy flex flex-col">
            <PublicNavbar canLogin={canLogin} canRegister={canRegister} />

            <section className="max-w-4xl mx-auto px-6 lg:px-8 py-14 lg:py-20 flex-1">
                <div className="text-center mb-10">
                    <span className="inline-block text-xs font-mono uppercase tracking-wide text-teal bg-teal-light rounded-full px-3 py-1">
                        Coba Sebelum Daftar
                    </span>
                    <h1 className="font-display font-700 text-3xl lg:text-4xl mt-4">
                        Lihat sendiri, bukan cuma baca
                    </h1>
                    <p className="text-navy/60 mt-3 max-w-xl mx-auto">
                        Kami siapkan akun demo Buku Induk berisi 20 data siswa contoh -
                        lengkap dengan foto, kartu keluarga, dan akta kelahiran (dummy) -
                        supaya kamu bisa jelajahi sistemnya langsung sebelum daftar.
                    </p>
                </div>

                <div className="rounded-2xl bg-white border border-navy/10 p-6 lg:p-8 mb-8">
                    <h2 className="font-display font-600 text-lg mb-4">Kredensial Akun Demo</h2>
                    <div className="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
                        <div>
                            <p className="text-xs text-navy/40 mb-1">Email</p>
                            <button
                                onClick={() => copy(DEMO_CREDENTIALS.email, 'email')}
                                className="w-full flex items-center justify-between gap-2 rounded-lg bg-teal-light px-4 py-3 text-sm font-mono text-teal hover:brightness-95"
                            >
                                {DEMO_CREDENTIALS.email}
                                <span className="text-xs">{copied === 'email' ? 'Tersalin!' : 'Salin'}</span>
                            </button>
                        </div>
                        <div>
                            <p className="text-xs text-navy/40 mb-1">Password</p>
                            <button
                                onClick={() => copy(DEMO_CREDENTIALS.password, 'password')}
                                className="w-full flex items-center justify-between gap-2 rounded-lg bg-teal-light px-4 py-3 text-sm font-mono text-teal hover:brightness-95"
                            >
                                {DEMO_CREDENTIALS.password}
                                <span className="text-xs">{copied === 'password' ? 'Tersalin!' : 'Salin'}</span>
                            </button>
                        </div>
                    </div>
                    <a
                        href="/login"
                        className="block text-center bg-coral text-navy font-medium rounded-lg px-6 py-3 text-sm hover:brightness-95"
                    >
                        Masuk dengan Akun Demo &rarr;
                    </a>
                </div>

                <div className="grid grid-cols-1 sm:grid-cols-3 gap-5 mb-10">
                    <div className="rounded-2xl bg-white border border-navy/10 p-5">
                        <p className="font-display font-600 text-navy mb-1">Read Only</p>
                        <p className="text-sm text-navy/60">Akun demo cuma bisa lihat data, tidak bisa ubah/hapus - aman dijelajahi siapa saja.</p>
                    </div>
                    <div className="rounded-2xl bg-white border border-navy/10 p-5">
                        <p className="font-display font-600 text-navy mb-1">Data Nyata Tapi Fiktif</p>
                        <p className="text-sm text-navy/60">20 siswa contoh dengan struktur data lengkap - persis seperti sekolah beneran, tapi semua nama & berkas dummy.</p>
                    </div>
                    <div className="rounded-2xl bg-white border border-navy/10 p-5">
                        <p className="font-display font-600 text-navy mb-1">Fitur Lengkap Terlihat</p>
                        <p className="text-sm text-navy/60">Menu import/export tetap kelihatan (ditandai "Demo"), jadi kamu tahu semua fitur yang akan kamu dapat.</p>
                    </div>
                </div>

                <div className="text-center">
                    <p className="text-navy/60 mb-4">Sudah yakin? Daftarkan sekolahmu sendiri, gratis.</p>
                    <Link href="/registrasi-sekolah" className="inline-block bg-teal text-white font-medium rounded-lg px-6 py-3 text-sm hover:brightness-110">
                        Daftarkan Sekolahmu
                    </Link>
                </div>
            </section>

            <PublicFooter />
        </div>
    );
}

import { Link } from '@inertiajs/react';
import PublicNavbar from '../Components/PublicNavbar';
import PublicFooter from '../Components/PublicFooter';

export default function DemoProgram({ canLogin = true, canRegister = true }) {
    return (
        <div className="min-h-screen bg-cream text-navy flex flex-col">
            <PublicNavbar canLogin={canLogin} canRegister={canRegister} />

            <section className="max-w-4xl mx-auto px-6 lg:px-8 py-14 lg:py-20 flex-1">
                <div className="text-center mb-10">
                    <span className="inline-block text-xs font-mono uppercase tracking-wide text-navy/50 bg-navy/5 rounded-full px-3 py-1">
                        Sementara Tidak Tersedia
                    </span>
                    <h1 className="font-display font-700 text-3xl lg:text-4xl mt-4">
                        Fitur demo sedang ditutup
                    </h1>
                    <p className="text-navy/60 mt-3 max-w-xl mx-auto">
                        Kami sedang menyiapkan akun demo yang lebih aman di lingkungan terpisah.
                        Sementara ini, langsung daftarkan sekolahmu saja - gratis dan bisa dicoba langsung
                        dengan data sekolahmu sendiri.
                    </p>
                </div>

                <div className="text-center">
                    <Link href="/registrasi-sekolah" className="inline-block bg-teal text-white font-medium rounded-lg px-6 py-3 text-sm hover:brightness-110">
                        Daftarkan Sekolahmu
                    </Link>
                </div>
            </section>

            <PublicFooter />
        </div>
    );
}

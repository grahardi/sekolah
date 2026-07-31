import Logo from './Logo';

export default function PublicFooter() {
    return (
        <footer className="bg-navy text-cream/70">
            <div className="max-w-6xl mx-auto px-6 lg:px-8 py-12 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                <div>
                    <Logo light className="mb-3" />
                    <p className="text-sm">
                        Portal sekolah terpadu — simulasi sains interaktif, modul ajar,
                        dan administrasi sekolah dalam satu platform.
                    </p>
                </div>

                <div>
                    <p className="font-display font-600 text-sm text-white mb-3">Navigasi</p>
                    <ul className="space-y-2 text-sm">
                        <li><a href="/" className="hover:text-white">Home</a></li>
                        <li><a href="/lab" className="hover:text-white">Simulasi</a></li>
                        <li><a href="/#program-sekolah" className="hover:text-white">Program Sekolah</a></li>
                        <li><a href="/demo" className="hover:text-white">Demo Program</a></li>
                        <li><a href="/#showcase" className="hover:text-white">Showcase</a></li>
                        <li><a href="/modul-ajar" className="hover:text-white">Modul Ajar</a></li>
                    </ul>
                </div>

                <div>
                    <p className="font-display font-600 text-sm text-white mb-3">Kontak</p>
                    <ul className="space-y-2 text-sm">
                        <li>admin@sekolah.co.id</li>
                        <li>+62 8xx-xxxx-xxxx</li>
                        <li>Malang, Jawa Timur</li>
                    </ul>
                </div>

                <div>
                    <p className="font-display font-600 text-sm text-white mb-3">Jam Layanan</p>
                    <p className="text-sm">Senin - Jumat: 07.00 - 16.00</p>
                    <p className="text-sm mt-1">Dukungan teknis via WhatsApp setiap hari</p>
                </div>
            </div>
            <div className="border-t border-white/10 px-6 py-5 text-center text-xs text-cream/40">
                &copy; {new Date().getFullYear()} sekolah.co.id &middot; Portal Sekolah Terpadu
            </div>
        </footer>
    );
}

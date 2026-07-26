import { Link } from '@inertiajs/react';

// Daftar modul platform sekolah.co.id secara umum. Modul lain (Ujian, Buku Induk,
// BK, Manajemen Sekolah) diasumsikan berada di aplikasi Laravel yang sama sebagai
// route/controller terpisah - Lab Interaktif hanya salah satu entri di sini.
const MODULES = [
    { key: 'lab', label: 'Lab Interaktif', href: '/lab', active: true },
    { key: 'ujian', label: 'Server Ujian', href: '/ujian', active: false },
    { key: 'induk', label: 'Buku Induk', href: '/buku-induk', active: false },
    { key: 'bk', label: 'Program BK', href: '/bk', active: false },
    { key: 'manajemen', label: 'Manajemen Sekolah', href: '/manajemen', active: false },
];

export default function AppLayout({ children, title, subtitle }) {
    return (
        <div className="min-h-screen bg-ink bg-grid-paper bg-grid-24 flex">
            <aside className="hidden md:flex w-60 shrink-0 flex-col border-r border-slate/15 bg-[#0F1729] px-5 py-6">
                <div className="mb-8">
                    <p className="font-display font-700 text-lg text-paper">sekolah<span className="text-amber">.co.id</span></p>
                    <p className="text-xs text-slate mt-1">Platform Sekolah Terpadu</p>
                </div>
                <nav className="flex flex-col gap-1">
                    {MODULES.map((m) => (
                        <a
                            key={m.key}
                            href={m.href}
                            className={`rounded-lg px-3 py-2 text-sm transition-colors ${
                                m.active
                                    ? 'bg-amber/15 text-amber font-medium'
                                    : 'text-slate hover:bg-white/5 hover:text-paper'
                            }`}
                        >
                            {m.label}
                        </a>
                    ))}
                </nav>
                <div className="mt-auto text-xs text-slate/70 font-mono">
                    v0.1 &middot; modul lab
                </div>
            </aside>

            <div className="flex-1 flex flex-col min-w-0">
                <header className="border-b border-slate/15 px-6 py-5 md:px-10">
                    <Link href="/lab" className="text-xs text-amber font-mono hover:underline">
                        &larr; Katalog Simulasi
                    </Link>
                    <h1 className="font-display font-600 text-2xl md:text-3xl text-paper mt-1">
                        {title}
                    </h1>
                    {subtitle && <p className="text-slate mt-1 max-w-2xl">{subtitle}</p>}
                </header>
                <main className="flex-1 px-6 py-8 md:px-10">{children}</main>
            </div>
        </div>
    );
}

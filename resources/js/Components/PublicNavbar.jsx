import { useState } from 'react';
import { Link } from '@inertiajs/react';
import Logo from './Logo';

const NAV_ITEMS = [
    { label: 'Home', href: '#home' },
    { label: 'Simulasi', href: '/lab' },
    { label: 'Modul Ajar', href: '#modul-ajar' },
    { label: 'Program Sekolah', href: '#program-sekolah' },
    { label: 'Showcase', href: '#showcase' },
];

export default function PublicNavbar({ canLogin = true, canRegister = true }) {
    const [open, setOpen] = useState(false);

    return (
        <header className="sticky top-0 z-40 bg-white/95 backdrop-blur border-b border-navy/10">
            <div className="max-w-6xl mx-auto px-6 lg:px-8 h-16 flex items-center justify-between">
                <Link href="/" className="shrink-0">
                    <Logo />
                </Link>

                <nav className="hidden lg:flex items-center gap-8">
                    {NAV_ITEMS.map((item) => (
                        <a
                            key={item.label}
                            href={item.href}
                            className="text-sm font-medium text-navy/70 hover:text-teal transition-colors"
                        >
                            {item.label}
                        </a>
                    ))}
                </nav>

                <div className="hidden lg:flex items-center gap-3 shrink-0">
                    {canLogin && (
                        <Link href="/login" className="text-sm font-medium text-navy/80 hover:text-navy">
                            Masuk
                        </Link>
                    )}
                    {canRegister && (
                        <Link
                            href="/register"
                            className="text-sm font-medium bg-teal text-white rounded-lg px-4 py-2.5 hover:brightness-110"
                        >
                            Daftar
                        </Link>
                    )}
                </div>

                <button className="lg:hidden text-navy" onClick={() => setOpen(!open)} aria-label="Buka menu">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" className="w-6 h-6">
                        {open ? <path d="M6 6l12 12M18 6L6 18" /> : <path d="M4 6h16M4 12h16M4 18h16" />}
                    </svg>
                </button>
            </div>

            {open && (
                <div className="lg:hidden border-t border-navy/10 px-6 py-4 space-y-3 bg-white">
                    {NAV_ITEMS.map((item) => (
                        <a
                            key={item.label}
                            href={item.href}
                            onClick={() => setOpen(false)}
                            className="block text-sm font-medium text-navy/80"
                        >
                            {item.label}
                        </a>
                    ))}
                    <div className="flex items-center gap-3 pt-3 border-t border-navy/10">
                        {canLogin && <Link href="/login" className="text-sm font-medium text-navy/80">Masuk</Link>}
                        {canRegister && (
                            <Link href="/register" className="text-sm font-medium bg-teal text-white rounded-lg px-4 py-2">
                                Daftar
                            </Link>
                        )}
                    </div>
                </div>
            )}
        </header>
    );
}

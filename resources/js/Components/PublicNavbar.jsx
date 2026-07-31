import { useState } from 'react';
import { Link } from '@inertiajs/react';
import Logo from './Logo';
import { PROGRAMS } from '../constants/programs';

const NAV_ITEMS = [
    { label: 'Home', href: '/' },
    { label: 'Simulasi', href: '/lab' },
    { label: 'Demo Program', href: '/demo' },
    { label: 'Showcase', href: '/#showcase' },
];

export default function PublicNavbar({ canLogin = true, canRegister = true }) {
    const [open, setOpen] = useState(false);
    const [programOpen, setProgramOpen] = useState(false);
    const [mobileProgramOpen, setMobileProgramOpen] = useState(false);

    return (
        <header className="sticky top-0 z-40 bg-white/95 backdrop-blur border-b border-navy/10">
            <div className="max-w-6xl mx-auto px-6 lg:px-8 h-16 flex items-center justify-between">
                <Link href="/" className="shrink-0">
                    <Logo />
                </Link>

                <nav className="hidden lg:flex items-center gap-8">
                    <a href="/" className="text-sm font-medium text-navy/70 hover:text-teal transition-colors">Home</a>
                    <a href="/lab" className="text-sm font-medium text-navy/70 hover:text-teal transition-colors">Simulasi</a>

                    {/* Dropdown Program Sekolah */}
                    <div className="relative">
                        <button
                            onClick={() => setProgramOpen((o) => !o)}
                            className="flex items-center gap-1 text-sm font-medium text-navy/70 hover:text-teal transition-colors"
                        >
                            Program Sekolah
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" className={`w-3.5 h-3.5 transition-transform ${programOpen ? 'rotate-180' : ''}`}>
                                <path d="M6 9l6 6 6-6" />
                            </svg>
                        </button>

                        {programOpen && (
                            <>
                                <div className="fixed inset-0 z-40" onClick={() => setProgramOpen(false)} />
                                <div className="absolute left-1/2 -translate-x-1/2 top-full mt-3 w-72 bg-white rounded-xl border border-navy/10 shadow-lg z-50 py-2">
                                    {PROGRAMS.map((p) => (
                                        <a
                                            key={p.slug}
                                            href={`/program/${p.slug}`}
                                            onClick={() => setProgramOpen(false)}
                                            className="flex items-center justify-between gap-3 px-4 py-2.5 hover:bg-navy/[0.03]"
                                        >
                                            <span className="text-sm text-navy/80">{p.title}</span>
                                            <span className={`text-[9px] font-mono uppercase tracking-wide rounded-full px-2 py-0.5 ${
                                                p.status === 'Aktif' ? 'bg-teal-light text-teal' : 'bg-navy/5 text-navy/40'
                                            }`}>
                                                {p.status}
                                            </span>
                                        </a>
                                    ))}
                                </div>
                            </>
                        )}
                    </div>

                    <a href="/demo" className="text-sm font-medium text-navy/70 hover:text-teal transition-colors">Demo Program</a>
                    <a href="/#showcase" className="text-sm font-medium text-navy/70 hover:text-teal transition-colors">Showcase</a>
                </nav>

                <div className="hidden lg:flex items-center gap-3 shrink-0">
                    {canLogin && (
                        <Link href="/login" className="text-sm font-medium text-navy/80 hover:text-navy">
                            Masuk
                        </Link>
                    )}
                    {canRegister && (
                        <Link
                            href="/registrasi-sekolah"
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
                <div className="lg:hidden border-t border-navy/10 px-6 py-4 space-y-1 bg-white">
                    {NAV_ITEMS.filter((i) => i.label !== 'Demo Program' && i.label !== 'Showcase').map((item) => (
                        <a key={item.label} href={item.href} onClick={() => setOpen(false)} className="block py-2 text-sm font-medium text-navy/80">
                            {item.label}
                        </a>
                    ))}

                    {/* Submenu Program Sekolah di mobile */}
                    <button
                        onClick={() => setMobileProgramOpen((o) => !o)}
                        className="w-full flex items-center justify-between py-2 text-sm font-medium text-navy/80"
                    >
                        Program Sekolah
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" className={`w-4 h-4 transition-transform ${mobileProgramOpen ? 'rotate-180' : ''}`}>
                            <path d="M6 9l6 6 6-6" />
                        </svg>
                    </button>
                    {mobileProgramOpen && (
                        <div className="pl-4 border-l border-navy/10 space-y-1 mb-1">
                            {PROGRAMS.map((p) => (
                                <a
                                    key={p.slug}
                                    href={`/program/${p.slug}`}
                                    onClick={() => setOpen(false)}
                                    className="flex items-center justify-between gap-2 py-1.5 text-sm text-navy/70"
                                >
                                    {p.title}
                                    <span className={`text-[9px] font-mono uppercase rounded-full px-1.5 py-0.5 ${p.status === 'Aktif' ? 'bg-teal-light text-teal' : 'bg-navy/5 text-navy/40'}`}>{p.status}</span>
                                </a>
                            ))}
                        </div>
                    )}

                    <a href="/demo" onClick={() => setOpen(false)} className="block py-2 text-sm font-medium text-navy/80">Demo Program</a>
                    <a href="/#showcase" onClick={() => setOpen(false)} className="block py-2 text-sm font-medium text-navy/80">Showcase</a>

                    <div className="flex items-center gap-3 pt-3 mt-2 border-t border-navy/10">
                        {canLogin && <Link href="/login" className="text-sm font-medium text-navy/80">Masuk</Link>}
                        {canRegister && (
                            <Link href="/registrasi-sekolah" className="text-sm font-medium bg-teal text-white rounded-lg px-4 py-2">
                                Daftar
                            </Link>
                        )}
                    </div>
                </div>
            )}
        </header>
    );
}

import { useState } from 'react';
import { Link, usePage } from '@inertiajs/react';

const MENU = [
    { key: 'dashboard', label: 'Dashboard', href: '/admin-portal' },
    { key: 'tiket', label: 'Tiket Dukungan', href: '/admin-portal/tiket' },
    { key: 'log-aktivitas', label: 'Log Aktivitas', href: '/admin-portal/log-aktivitas' },
    { key: 'exo', label: 'Extraordinary CBT', href: '/admin-portal/exo' },
];

export default function SuperAdminLayout({ title, breadcrumb = [], children }) {
    const { props, url } = usePage();
    const user = props?.auth?.user;
    const flash = props?.flash || {};
    const [mobileOpen, setMobileOpen] = useState(false);

    const isActive = (href) => url === href || (href !== '/admin-portal' && url.startsWith(href));

    const SidebarContent = () => (
        <>
            <div className="px-5 py-5 flex items-center gap-2.5 border-b border-white/10">
                <div className="h-8 w-8 rounded-lg bg-white/10 flex items-center justify-center">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#fff" strokeWidth="2"><path d="M12 2 2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5" /></svg>
                </div>
                <div>
                    <p className="font-display font-700 text-white text-sm leading-tight">sekolah.co.id</p>
                    <p className="text-[10px] text-white/50 uppercase tracking-wider">System Panel</p>
                </div>
            </div>

            <nav className="flex-1 px-3 py-4 flex flex-col gap-1">
                {MENU.map((m) => (
                    <Link
                        key={m.key}
                        href={m.href}
                        className={`px-3 py-2.5 rounded-lg text-sm font-500 transition-colors ${
                            isActive(m.href) ? 'bg-white/10 text-white' : 'text-white/60 hover:text-white hover:bg-white/5'
                        }`}
                    >
                        {m.label}
                    </Link>
                ))}
            </nav>

            <div className="px-3 py-4 border-t border-white/10">
                <a href="/dashboard" className="block px-3 py-2 rounded-lg text-xs text-white/50 hover:text-white hover:bg-white/5 mb-1">&larr; Kembali ke Portal Sekolah</a>
                <p className="px-3 text-[11px] text-white/40 truncate">{user?.name}</p>
            </div>
        </>
    );

    return (
        <div className="min-h-screen bg-gray-50 flex">
            {/* Sidebar desktop - tersembunyi otomatis di layar kecil */}
            <aside className="hidden lg:flex w-60 bg-navy flex-shrink-0 flex-col">
                <SidebarContent />
            </aside>

            {/* Sidebar mobile - overlay, nutupin konten (bukan geser) */}
            {mobileOpen && (
                <div className="fixed inset-0 z-40 lg:hidden">
                    <div className="fixed inset-0 bg-black/45" onClick={() => setMobileOpen(false)} />
                    <aside className="relative w-64 h-full bg-navy flex flex-col">
                        <button onClick={() => setMobileOpen(false)} className="absolute top-4 right-4 text-white/60 hover:text-white text-xl leading-none">&times;</button>
                        <SidebarContent />
                    </aside>
                </div>
            )}

            <div className="flex-1 min-w-0">
                <header className="bg-white border-b border-black/5 px-4 sm:px-8 py-4 flex items-center gap-3">
                    <button className="lg:hidden text-navy/70" onClick={() => setMobileOpen(true)}>
                        <i className="ti ti-menu-2 text-xl" />
                    </button>
                    <div className="min-w-0">
                        <p className="text-[11px] text-navy/40 mb-0.5 truncate">{['System Panel', ...breadcrumb].join(' / ')}</p>
                        <h1 className="font-display font-700 text-lg sm:text-xl text-navy truncate">{title}</h1>
                    </div>
                </header>
                <main className="p-4 sm:p-8">
                    {flash.success && (
                        <div className="mb-5 rounded-xl bg-emerald-50 text-emerald-700 text-sm px-4 py-3">{flash.success}</div>
                    )}
                    {flash.error && (
                        <div className="mb-5 rounded-xl bg-red-50 text-red-700 text-sm px-4 py-3">{flash.error}</div>
                    )}
                    {children}
                </main>
            </div>
        </div>
    );
}

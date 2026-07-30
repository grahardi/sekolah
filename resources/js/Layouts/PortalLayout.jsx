import { useState } from 'react';
import { Link, usePage } from '@inertiajs/react';
import Logo from '../Components/Logo';

// Struktur menu portal. Tiap modul lain (Ujian, Buku Induk, BK, Manajemen)
// tinggal ditambah sebagai entri baru di sini dengan pola yang sama seperti
// "lab" - kalau perlu submenu, isi array `children`.
const MENU = [
    { key: 'dashboard', label: 'Beranda', href: '/dashboard', icon: HomeIcon },
    {
        key: 'lab',
        label: 'Lab Interaktif',
        href: '/lab',
        icon: FlaskIcon,
        children: [
            { label: 'Semua Simulasi', href: '/lab' },
            { label: 'Bandul Sederhana', href: '/lab/bandul' },
            { label: 'Gerak Peluru', href: '/lab/gerak-peluru' },
            { label: 'Rangkaian Listrik', href: '/lab/rangkaian-listrik' },
        ],
    },
    { key: 'ujian', label: 'Server Ujian', href: '/ujian', icon: DocIcon, disabled: true },
    { key: 'induk', label: 'Buku Induk', href: '/buku-induk', icon: BookIcon, disabled: false },
    { key: 'bk', label: 'Program BK', href: '/bk', icon: HeartIcon, disabled: true },
    { key: 'manajemen', label: 'Manajemen Sekolah', href: '/manajemen', icon: GearIcon, disabled: true },
];

export default function PortalLayout({ children, title, breadcrumb = [] }) {
    const { url, props } = usePage();
    const user = props?.auth?.user;
    const [openMenu, setOpenMenu] = useState('lab');
    const [mobileOpen, setMobileOpen] = useState(false);

    const isActive = (href) => url === href || url.startsWith(href + '/');

    return (
        <div className="min-h-screen bg-cream text-navy flex">
            {/* Sidebar desktop */}
            <aside className="hidden lg:flex w-64 shrink-0 flex-col bg-teal text-cream">
                <div className="px-5 py-6 border-b border-white/10">
                    <Logo light />
                    <p className="text-xs text-cream/60 mt-2">Portal Sekolah Terpadu</p>
                </div>

                <nav className="flex-1 px-3 py-4 space-y-1 overflow-y-auto">
                    {MENU.map((item) => (
                        <SidebarItem
                            key={item.key}
                            item={item}
                            isActive={isActive}
                            open={openMenu === item.key}
                            onToggle={() => setOpenMenu(openMenu === item.key ? null : item.key)}
                        />
                    ))}
                </nav>

                <div className="px-5 py-4 border-t border-white/10 text-xs text-cream/50">
                    v0.1 &middot; portal
                </div>
            </aside>

            {/* Sidebar mobile (drawer) */}
            {mobileOpen && (
                <div className="fixed inset-0 z-40 lg:hidden">
                    <div className="absolute inset-0 bg-black/40" onClick={() => setMobileOpen(false)} />
                    <aside className="absolute inset-y-0 left-0 w-72 bg-teal text-cream flex flex-col">
                        <div className="px-5 py-6 border-b border-white/10 flex items-center justify-between">
                            <Logo light />
                            <button onClick={() => setMobileOpen(false)} className="text-cream/70">✕</button>
                        </div>
                        <nav className="flex-1 px-3 py-4 space-y-1 overflow-y-auto">
                            {MENU.map((item) => (
                                <SidebarItem
                                    key={item.key}
                                    item={item}
                                    isActive={isActive}
                                    open={openMenu === item.key}
                                    onToggle={() => setOpenMenu(openMenu === item.key ? null : item.key)}
                                />
                            ))}
                        </nav>
                    </aside>
                </div>
            )}

            {/* Konten utama */}
            <div className="flex-1 min-w-0 flex flex-col">
                <header className="sticky top-0 z-30 bg-cream/90 backdrop-blur border-b border-navy/10 px-5 lg:px-8 py-4 flex items-center justify-between">
                    <div className="flex items-center gap-3 min-w-0">
                        <button className="lg:hidden text-navy/70" onClick={() => setMobileOpen(true)}>
                            <MenuIcon />
                        </button>
                        <div className="min-w-0">
                            {breadcrumb.length > 0 && (
                                <p className="text-xs text-navy/50 font-mono truncate">
                                    {breadcrumb.join(' / ')}
                                </p>
                            )}
                            <h1 className="font-display font-600 text-xl lg:text-2xl text-navy truncate">
                                {title}
                            </h1>
                        </div>
                    </div>

                    <div className="flex items-center gap-3 shrink-0">
                        <div className="hidden sm:flex flex-col items-end leading-tight">
                            <span className="text-sm font-medium text-navy">{user?.name ?? 'Tamu'}</span>
                            <span className="text-xs text-navy/50">{user?.role ?? 'Pengunjung'}</span>
                        </div>
                        <div className="h-9 w-9 rounded-full bg-teal text-white flex items-center justify-center font-display text-sm">
                            {(user?.name ?? 'T').charAt(0).toUpperCase()}
                        </div>
                    </div>
                </header>

                <main className="flex-1 px-5 lg:px-8 py-6">{children}</main>
            </div>
        </div>
    );
}

function SidebarItem({ item, isActive, open, onToggle }) {
    const Icon = item.icon;
    const active = isActive(item.href);

    if (item.disabled) {
        return (
            <div className="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm text-cream/35 cursor-not-allowed">
                <Icon className="w-4 h-4 shrink-0" />
                <span className="flex-1">{item.label}</span>
                <span className="text-[10px] font-mono bg-white/10 rounded px-1.5 py-0.5">segera</span>
            </div>
        );
    }

    if (!item.children) {
        return (
            <Link
                href={item.href}
                className={`flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm transition-colors ${
                    active ? 'bg-white text-teal font-medium' : 'text-cream/85 hover:bg-white/10'
                }`}
            >
                <Icon className="w-4 h-4 shrink-0" />
                <span>{item.label}</span>
            </Link>
        );
    }

    return (
        <div>
            <button
                onClick={onToggle}
                className={`w-full flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm transition-colors ${
                    active ? 'bg-white/10 text-white font-medium' : 'text-cream/85 hover:bg-white/10'
                }`}
            >
                <Icon className="w-4 h-4 shrink-0" />
                <span className="flex-1 text-left">{item.label}</span>
                <ChevronIcon className={`w-3.5 h-3.5 transition-transform ${open ? 'rotate-90' : ''}`} />
            </button>
            {open && (
                <div className="mt-1 ml-4 pl-3 border-l border-white/15 space-y-0.5">
                    {item.children.map((child) => (
                        <Link
                            key={child.href}
                            href={child.href}
                            className={`block rounded-lg px-3 py-2 text-sm transition-colors ${
                                isActive(child.href)
                                    ? 'bg-white text-teal font-medium'
                                    : 'text-cream/70 hover:bg-white/10 hover:text-cream'
                            }`}
                        >
                            {child.label}
                        </Link>
                    ))}
                </div>
            )}
        </div>
    );
}

/* Ikon inline ringan - tidak menambah dependency baru */
function HomeIcon(p) { return <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" {...p}><path d="M3 11l9-8 9 8M5 10v10h14V10" /></svg>; }
function FlaskIcon(p) { return <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" {...p}><path d="M9 3h6M10 3v6l-6 10a1 1 0 001 1.5h14a1 1 0 001-1.5L14 9V3" /></svg>; }
function DocIcon(p) { return <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" {...p}><path d="M6 2h9l5 5v15H6z" /><path d="M14 2v6h6" /></svg>; }
function BookIcon(p) { return <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" {...p}><path d="M4 19.5A2.5 2.5 0 016.5 17H20V4H6.5A2.5 2.5 0 004 6.5v13z" /></svg>; }
function HeartIcon(p) { return <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" {...p}><path d="M12 21s-7-4.5-9.5-9C.7 8.3 3 4 7 4c2 0 3.5 1 5 3 1.5-2 3-3 5-3 4 0 6.3 4.3 4.5 8-2.5 4.5-9.5 9-9.5 9z" /></svg>; }
function GearIcon(p) { return <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" {...p}><circle cx="12" cy="12" r="3" /><path d="M19.4 15a1.7 1.7 0 00.3 1.9l.1.1a2 2 0 11-2.8 2.8l-.1-.1a1.7 1.7 0 00-1.9-.3 1.7 1.7 0 00-1 1.5V21a2 2 0 11-4 0v-.1a1.7 1.7 0 00-1-1.6 1.7 1.7 0 00-1.9.3l-.1.1a2 2 0 11-2.8-2.8l.1-.1a1.7 1.7 0 00.3-1.9 1.7 1.7 0 00-1.5-1H3a2 2 0 110-4h.1a1.7 1.7 0 001.5-1 1.7 1.7 0 00-.3-1.9l-.1-.1a2 2 0 112.8-2.8l.1.1a1.7 1.7 0 001.9.3H9a1.7 1.7 0 001-1.5V3a2 2 0 114 0v.1a1.7 1.7 0 001 1.5 1.7 1.7 0 001.9-.3l.1-.1a2 2 0 112.8 2.8l-.1.1a1.7 1.7 0 00-.3 1.9V9a1.7 1.7 0 001.5 1H21a2 2 0 110 4h-.1a1.7 1.7 0 00-1.5 1z" /></svg>; }
function ChevronIcon(p) { return <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" {...p}><path d="M9 18l6-6-6-6" /></svg>; }
function MenuIcon(p) { return <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" className="w-6 h-6" {...p}><path d="M4 6h16M4 12h16M4 18h16" /></svg>; }

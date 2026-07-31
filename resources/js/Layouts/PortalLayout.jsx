import { useState } from 'react';
import { Link, usePage, router } from '@inertiajs/react';
import Logo from '../Components/Logo';

// Struktur menu portal. Tiap modul lain (Ujian, Buku Induk, BK, Manajemen)
// tinggal ditambah sebagai entri baru di sini dengan pola yang sama seperti
// "lab" - kalau perlu submenu, isi array `children`.
const MENU = [
    { key: 'dashboard', label: 'Beranda', href: '/dashboard', icon: HomeIcon },
    { key: 'induk', label: 'Buku Induk', href: '/buku-induk', icon: BookIcon, disabled: false, external: true },
    { key: 'kepegawaian', label: 'Kepegawaian', href: '/kepegawaian', icon: UsersIcon, disabled: false, external: true },
    { key: 'pengguna', label: 'Manajemen User', href: '/pengguna', icon: ShieldIcon, disabled: false, external: true },
    { key: 'ujian', label: 'Server Ujian', href: '/ujian', icon: DocIcon, disabled: true },
    { key: 'bk', label: 'Program BK', href: '/bk', icon: HeartIcon, disabled: false, external: true },
    { key: 'manajemen', label: 'Manajemen Sekolah', href: '/manajemen', icon: GearIcon, disabled: true },
    { key: 'modul-ajar', label: 'Modul Ajar', href: '/modul-ajar', icon: DocIcon },
    {
        key: 'lab',
        label: 'Lab Interaktif',
        href: '/lab',
        icon: FlaskIcon,
    },
];

// Cuma muncul untuk user dengan is_super_admin = true (bukan admin sekolah biasa)
const SUPERADMIN_ITEM = { key: 'superadmin', label: 'Admin Portal', href: '/admin-portal', icon: ShieldIcon };

export default function PortalLayout({ children, title, breadcrumb = [] }) {
    const { url, props } = usePage();
    const user = props?.auth?.user;
    const [openMenu, setOpenMenu] = useState('lab');
    const [mobileOpen, setMobileOpen] = useState(false);
    const menuItems = user?.is_super_admin ? [...MENU, SUPERADMIN_ITEM] : MENU;

    const isActive = (href) => url === href || url.startsWith(href + '/');

    return (
        <div className="min-h-screen bg-cream text-navy flex">
            {/* Sidebar desktop */}
            <aside className="hidden lg:flex w-64 shrink-0 flex-col bg-teal text-cream">
                <div className="px-5 py-6 border-b border-white/10">
                    <div className="bg-white rounded-xl px-3 py-2.5 inline-block">
                        <Logo />
                    </div>
                    <p className="text-xs text-cream/60 mt-2">Portal Sekolah Terpadu</p>
                </div>

                <nav className="flex-1 px-3 py-4 space-y-1 overflow-y-auto">
                    {menuItems.map((item) => (
                        <SidebarItem
                            key={item.key}
                            item={item}
                            isActive={isActive}
                            open={openMenu === item.key}
                            onToggle={() => setOpenMenu(openMenu === item.key ? null : item.key)}
                        />
                    ))}
                </nav>

                <div className="px-3 py-3 border-t border-white/10">
                    <div className="flex items-center gap-2.5 px-2 py-2 mb-1">
                        <div className="h-8 w-8 rounded-full bg-white/15 text-white flex items-center justify-center font-display text-sm shrink-0">
                            {(user?.name ?? 'T').charAt(0).toUpperCase()}
                        </div>
                        <div className="min-w-0">
                            <p className="text-sm font-medium text-white truncate">{user?.name ?? 'Tamu'}</p>
                            <p className="text-xs text-cream/60 truncate">{user?.role ?? 'Pengunjung'}</p>
                        </div>
                    </div>
                    <a
                        href="/buku-induk/ganti-password"
                        className="flex items-center gap-3 rounded-lg px-3 py-2 text-sm text-cream/85 hover:bg-white/10"
                    >
                        <KeyIcon className="w-4 h-4 shrink-0" />
                        Ganti Password
                    </a>
                    <button
                        onClick={() => router.post('/logout')}
                        className="w-full flex items-center gap-3 rounded-lg px-3 py-2 text-sm text-red-200 hover:bg-white/10"
                    >
                        <LogoutIcon className="w-4 h-4 shrink-0" />
                        Keluar
                    </button>
                </div>
            </aside>

            {/* Sidebar mobile (drawer) */}
            {mobileOpen && (
                <div className="fixed inset-0 z-40 lg:hidden">
                    <div className="absolute inset-0 bg-black/40" onClick={() => setMobileOpen(false)} />
                    <aside className="absolute inset-y-0 left-0 w-72 bg-teal text-cream flex flex-col">
                        <div className="px-5 py-6 border-b border-white/10 flex items-center justify-between">
                            <div className="bg-white rounded-xl px-3 py-2.5 inline-block">
                                <Logo />
                            </div>
                            <button onClick={() => setMobileOpen(false)} className="text-cream/70">✕</button>
                        </div>
                        <nav className="flex-1 px-3 py-4 space-y-1 overflow-y-auto">
                            {menuItems.map((item) => (
                                <SidebarItem
                                    key={item.key}
                                    item={item}
                                    isActive={isActive}
                                    open={openMenu === item.key}
                                    onToggle={() => setOpenMenu(openMenu === item.key ? null : item.key)}
                                />
                            ))}
                        </nav>

                        <div className="px-3 py-3 border-t border-white/10">
                            <div className="flex items-center gap-2.5 px-2 py-2 mb-1">
                                <div className="h-8 w-8 rounded-full bg-white/15 text-white flex items-center justify-center font-display text-sm shrink-0">
                                    {(user?.name ?? 'T').charAt(0).toUpperCase()}
                                </div>
                                <div className="min-w-0">
                                    <p className="text-sm font-medium text-white truncate">{user?.name ?? 'Tamu'}</p>
                                    <p className="text-xs text-cream/60 truncate">{user?.role ?? 'Pengunjung'}</p>
                                </div>
                            </div>
                            <a
                                href="/buku-induk/ganti-password"
                                className="flex items-center gap-3 rounded-lg px-3 py-2 text-sm text-cream/85 hover:bg-white/10"
                            >
                                <KeyIcon className="w-4 h-4 shrink-0" />
                                Ganti Password
                            </a>
                            <button
                                onClick={() => router.post('/logout')}
                                className="w-full flex items-center gap-3 rounded-lg px-3 py-2 text-sm text-red-200 hover:bg-white/10"
                            >
                                <LogoutIcon className="w-4 h-4 shrink-0" />
                                Keluar
                            </button>
                        </div>
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
        const className = `flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm transition-colors ${
            active ? 'bg-white text-teal font-medium' : 'text-cream/85 hover:bg-white/10'
        }`;

        // Item non-Inertia (halaman Blade seperti Buku Induk) pakai <a> biasa
        // supaya browser reload penuh, bukan "SPA-visit" ala Inertia yang
        // bikin transisi nyangkut/menimpa saat responsnya bukan JSON Inertia.
        if (item.external) {
            return (
                <a href={item.href} className={className}>
                    <Icon className="w-4 h-4 shrink-0" />
                    <span>{item.label}</span>
                </a>
            );
        }

        return (
            <Link
                href={item.href}
                className={className}
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
function UsersIcon(p) { return <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" {...p}><circle cx="9" cy="8" r="3.5" /><path d="M2.5 20c0-3.5 3-6 6.5-6s6.5 2.5 6.5 6" /><circle cx="17" cy="9" r="2.8" /><path d="M15 20c.2-2.8 2-5 4.5-5.5" /></svg>; }
function HeartIcon(p) { return <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" {...p}><path d="M12 21s-7-4.5-9.5-9C.7 8.3 3 4 7 4c2 0 3.5 1 5 3 1.5-2 3-3 5-3 4 0 6.3 4.3 4.5 8-2.5 4.5-9.5 9-9.5 9z" /></svg>; }
function GearIcon(p) { return <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" {...p}><circle cx="12" cy="12" r="3" /><path d="M19.4 15a1.7 1.7 0 00.3 1.9l.1.1a2 2 0 11-2.8 2.8l-.1-.1a1.7 1.7 0 00-1.9-.3 1.7 1.7 0 00-1 1.5V21a2 2 0 11-4 0v-.1a1.7 1.7 0 00-1-1.6 1.7 1.7 0 00-1.9.3l-.1.1a2 2 0 11-2.8-2.8l.1-.1a1.7 1.7 0 00.3-1.9 1.7 1.7 0 00-1.5-1H3a2 2 0 110-4h.1a1.7 1.7 0 001.5-1 1.7 1.7 0 00-.3-1.9l-.1-.1a2 2 0 112.8-2.8l.1.1a1.7 1.7 0 001.9.3H9a1.7 1.7 0 001-1.5V3a2 2 0 114 0v.1a1.7 1.7 0 001 1.5 1.7 1.7 0 001.9-.3l.1-.1a2 2 0 112.8 2.8l-.1.1a1.7 1.7 0 00-.3 1.9V9a1.7 1.7 0 001.5 1H21a2 2 0 110 4h-.1a1.7 1.7 0 00-1.5 1z" /></svg>; }
function ShieldIcon(p) { return <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" {...p}><path d="M12 3l8 3v6c0 4.5-3 8-8 9-5-1-8-4.5-8-9V6l8-3z" /><path d="M9.5 12l1.8 1.8L15 10" /></svg>; }
function ChevronIcon(p) { return <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" {...p}><path d="M9 18l6-6-6-6" /></svg>; }
function MenuIcon(p) { return <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" className="w-6 h-6" {...p}><path d="M4 6h16M4 12h16M4 18h16" /></svg>; }
function KeyIcon(p) { return <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" {...p}><circle cx="8" cy="15" r="4" /><path d="M10.5 12.5L20 3M17 6l3 3M14 9l2 2" /></svg>; }
function LogoutIcon(p) { return <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" {...p}><path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4M16 17l5-5-5-5M21 12H9" /></svg>; }

import { useState } from 'react';
import { Link, router } from '@inertiajs/react';
import SuperAdminLayout from '../../Layouts/SuperAdminLayout';

const EVENT_STYLE = {
    login: { label: 'Login', bg: 'bg-emerald-50', text: 'text-emerald-700' },
    logout: { label: 'Logout', bg: 'bg-slate-100', text: 'text-slate-600' },
    registrasi: { label: 'Registrasi', bg: 'bg-blue-50', text: 'text-blue-700' },
};

export default function LogAktivitas({ logs, filters, stats }) {
    const [search, setSearch] = useState(filters.search || '');

    const submitSearch = (e) => {
        e.preventDefault();
        router.get('/admin-portal/log-aktivitas', { search, event: filters.event }, { preserveState: true });
    };

    const gantiEvent = (event) => {
        router.get('/admin-portal/log-aktivitas', { search: filters.search, event: event || undefined }, { preserveState: true });
    };

    return (
        <SuperAdminLayout title="Log Aktivitas" breadcrumb={['Log Aktivitas']}>
            <p className="text-navy/60 mb-6 max-w-2xl">
                Rekam jejak login, logout, dan registrasi akun di seluruh sekolah.co.id.
            </p>

            <div className="grid grid-cols-1 sm:grid-cols-3 gap-5 mb-8">
                <button onClick={() => gantiEvent('login')} className={`text-left rounded-2xl bg-emerald-50 p-5 transition-shadow ${filters.event === 'login' ? 'ring-2 ring-emerald-400' : ''}`}>
                    <i className="ti ti-login-2 text-emerald-700 text-lg" />
                    <p className="font-display font-700 text-3xl text-emerald-700 mt-2">{stats.total_login}</p>
                    <p className="text-xs text-navy/50 mt-1">Total Login</p>
                </button>
                <button onClick={() => gantiEvent('logout')} className={`text-left rounded-2xl bg-slate-100 p-5 transition-shadow ${filters.event === 'logout' ? 'ring-2 ring-slate-400' : ''}`}>
                    <i className="ti ti-logout text-slate-600 text-lg" />
                    <p className="font-display font-700 text-3xl text-slate-600 mt-2">{stats.total_logout}</p>
                    <p className="text-xs text-navy/50 mt-1">Total Logout</p>
                </button>
                <button onClick={() => gantiEvent('registrasi')} className={`text-left rounded-2xl bg-blue-50 p-5 transition-shadow ${filters.event === 'registrasi' ? 'ring-2 ring-blue-400' : ''}`}>
                    <i className="ti ti-user-plus text-blue-700 text-lg" />
                    <p className="font-display font-700 text-3xl text-blue-700 mt-2">{stats.total_registrasi}</p>
                    <p className="text-xs text-navy/50 mt-1">Total Registrasi</p>
                </button>
            </div>

            <div className="flex items-center gap-3 mb-5 flex-wrap">
                <form onSubmit={submitSearch} className="max-w-md flex-1 min-w-[220px]">
                    <input
                        type="text"
                        value={search}
                        onChange={(e) => setSearch(e.target.value)}
                        placeholder="Cari nama atau email..."
                        className="w-full rounded-lg border border-navy/15 bg-white px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-teal/40"
                    />
                </form>
                {filters.event && (
                    <button onClick={() => gantiEvent(null)} className="text-xs text-navy/50 hover:text-navy underline">
                        Hapus filter "{EVENT_STYLE[filters.event]?.label}"
                    </button>
                )}
            </div>

            <div className="rounded-2xl bg-white border border-navy/10 overflow-hidden">
                <table className="w-full text-sm">
                    <thead>
                        <tr className="bg-navy/5 text-left text-navy/60 text-xs uppercase tracking-wide">
                            <th className="px-5 py-3">Waktu</th>
                            <th className="px-5 py-3">Pengguna</th>
                            <th className="px-5 py-3">Sekolah</th>
                            <th className="px-5 py-3">Event</th>
                            <th className="px-5 py-3">IP Address</th>
                        </tr>
                    </thead>
                    <tbody>
                        {logs.data.length === 0 && (
                            <tr><td colSpan={5} className="px-5 py-8 text-center text-navy/40">Belum ada catatan aktivitas.</td></tr>
                        )}
                        {logs.data.map((log) => {
                            const style = EVENT_STYLE[log.event] || { label: log.event, bg: 'bg-slate-100', text: 'text-slate-600' };
                            return (
                                <tr key={log.id} className="border-t border-navy/5 hover:bg-navy/[0.02]">
                                    <td className="px-5 py-3 text-navy/60 text-xs whitespace-nowrap">
                                        {new Date(log.created_at).toLocaleString('id-ID', { dateStyle: 'medium', timeStyle: 'short' })}
                                    </td>
                                    <td className="px-5 py-3">
                                        <p className="font-medium text-navy">{log.nama_snapshot || '-'}</p>
                                        <p className="text-xs text-navy/40">{log.email_snapshot}</p>
                                    </td>
                                    <td className="px-5 py-3 text-navy/70">
                                        {log.sekolah ? (
                                            <Link href={`/admin-portal/sekolah/${log.sekolah.id}`} className="hover:text-teal">{log.sekolah.nama}</Link>
                                        ) : '-'}
                                    </td>
                                    <td className="px-5 py-3">
                                        <span className={`inline-block px-2.5 py-1 rounded-full text-xs font-medium ${style.bg} ${style.text}`}>{style.label}</span>
                                    </td>
                                    <td className="px-5 py-3 font-mono text-navy/50 text-xs">{log.ip_address || '-'}</td>
                                </tr>
                            );
                        })}
                    </tbody>
                </table>
            </div>

            {logs.links && logs.links.length > 3 && (
                <div className="flex flex-wrap gap-1 mt-5">
                    {logs.links.map((link, i) => (
                        <Link
                            key={i}
                            href={link.url || '#'}
                            preserveState
                            className={`text-xs px-3 py-1.5 rounded-lg ${
                                link.active ? 'bg-teal text-white' : 'bg-white border border-navy/10 text-navy/60'
                            } ${!link.url ? 'opacity-40 pointer-events-none' : 'hover:border-teal/40'}`}
                            dangerouslySetInnerHTML={{ __html: link.label }}
                        />
                    ))}
                </div>
            )}
        </SuperAdminLayout>
    );
}

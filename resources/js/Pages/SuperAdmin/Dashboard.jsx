import { useState } from 'react';
import { Link, router } from '@inertiajs/react';
import PortalLayout from '../../Layouts/PortalLayout';

export default function Dashboard({ sekolahs, stats, filters }) {
    const [search, setSearch] = useState(filters.search || '');

    const submitSearch = (e) => {
        e.preventDefault();
        router.get('/admin-portal', { search }, { preserveState: true });
    };

    return (
        <PortalLayout title="Admin Portal" breadcrumb={['Portal', 'Admin Portal']}>
            <p className="text-navy/60 mb-6 max-w-2xl">
                Kelola seluruh sekolah yang terdaftar di sekolah.co.id - ini beda dari
                Buku Induk, yang cuma mengurus satu sekolah.
            </p>

            {/* Stat cards */}
            <div className="grid grid-cols-1 sm:grid-cols-3 gap-5 mb-8">
                <div className="rounded-2xl bg-white border border-navy/10 p-5">
                    <p className="text-xs font-mono text-navy/40 uppercase tracking-wide">Total Sekolah</p>
                    <p className="font-display font-700 text-3xl text-navy mt-1">{stats.total_sekolah}</p>
                </div>
                <div className="rounded-2xl bg-white border border-navy/10 p-5">
                    <p className="text-xs font-mono text-navy/40 uppercase tracking-wide">Total Akun Pengguna</p>
                    <p className="font-display font-700 text-3xl text-navy mt-1">{stats.total_user}</p>
                </div>
                <div className="rounded-2xl bg-white border border-navy/10 p-5">
                    <p className="text-xs font-mono text-navy/40 uppercase tracking-wide">Total Data Siswa</p>
                    <p className="font-display font-700 text-3xl text-navy mt-1">{stats.total_siswa}</p>
                </div>
            </div>

            {/* Search */}
            <form onSubmit={submitSearch} className="mb-5 max-w-md">
                <input
                    type="text"
                    value={search}
                    onChange={(e) => setSearch(e.target.value)}
                    placeholder="Cari nama sekolah atau NPSN..."
                    className="w-full rounded-lg border border-navy/15 bg-white px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-teal/40"
                />
            </form>

            {/* Table */}
            <div className="rounded-2xl bg-white border border-navy/10 overflow-hidden">
                <table className="w-full text-sm">
                    <thead>
                        <tr className="bg-navy/5 text-left text-navy/60 text-xs uppercase tracking-wide">
                            <th className="px-5 py-3">Sekolah</th>
                            <th className="px-5 py-3">NPSN</th>
                            <th className="px-5 py-3">Jenjang</th>
                            <th className="px-5 py-3 text-center">Pengguna</th>
                            <th className="px-5 py-3 text-center">Siswa</th>
                            <th className="px-5 py-3">Terdaftar</th>
                        </tr>
                    </thead>
                    <tbody>
                        {sekolahs.data.length === 0 && (
                            <tr><td colSpan={6} className="px-5 py-8 text-center text-navy/40">Belum ada sekolah terdaftar.</td></tr>
                        )}
                        {sekolahs.data.map((s) => (
                            <tr key={s.id} className="border-t border-navy/5 hover:bg-navy/[0.02]">
                                <td className="px-5 py-3">
                                    <Link href={`/admin-portal/sekolah/${s.id}`} className="font-medium text-navy hover:text-teal">
                                        {s.nama}
                                    </Link>
                                    <p className="text-xs text-navy/40">{[s.kecamatan, s.kabupaten_kota, s.provinsi].filter(Boolean).join(', ')}</p>
                                </td>
                                <td className="px-5 py-3 font-mono text-navy/70">{s.npsn}</td>
                                <td className="px-5 py-3 text-navy/70">{s.bentuk_pendidikan || '-'}</td>
                                <td className="px-5 py-3 text-center text-navy/70">{s.users_count}</td>
                                <td className="px-5 py-3 text-center text-navy/70">{s.siswas_count}</td>
                                <td className="px-5 py-3 text-navy/50 text-xs">{new Date(s.created_at).toLocaleDateString('id-ID')}</td>
                            </tr>
                        ))}
                    </tbody>
                </table>
            </div>

            {/* Pagination sederhana */}
            {sekolahs.links && sekolahs.links.length > 3 && (
                <div className="flex flex-wrap gap-1 mt-5">
                    {sekolahs.links.map((link, i) => (
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
        </PortalLayout>
    );
}

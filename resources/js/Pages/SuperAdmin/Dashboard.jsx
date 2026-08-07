import { useState } from 'react';
import { Link, router } from '@inertiajs/react';
import SuperAdminLayout from '../../Layouts/SuperAdminLayout';

export default function Dashboard({ sekolahs, stats, filters }) {
    const [search, setSearch] = useState(filters.search || '');

    const submitSearch = (e) => {
        e.preventDefault();
        router.get('/admin-portal', { search }, { preserveState: true });
    };

    return (
        <SuperAdminLayout title="Dashboard" breadcrumb={["Dashboard"]}>
            <p className="text-navy/60 mb-6 max-w-2xl">
                Kelola seluruh sekolah yang terdaftar di sekolah.co.id - ini beda dari
                Buku Induk, yang cuma mengurus satu sekolah.
            </p>

            {/* Stat cards */}
            <div className="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4 mb-8">
                {[
                    { label: 'Total Sekolah', value: stats.total_sekolah, bg: 'bg-blue-500', icon: 'ti-building' },
                    { label: 'Total Pengguna', value: stats.total_user, bg: 'bg-purple-500', icon: 'ti-users' },
                    { label: 'Total Siswa', value: stats.total_siswa, bg: 'bg-emerald-500', icon: 'ti-school' },
                    { label: 'Total Kelas', value: stats.total_kelas, bg: 'bg-orange-500', icon: 'ti-door' },
                    { label: 'Total Guru', value: stats.total_guru, bg: 'bg-rose-500', icon: 'ti-user-check' },
                    { label: 'Total TP', value: stats.total_tp, bg: 'bg-cyan-500', icon: 'ti-target-arrow' },
                ].map((s) => (
                    <div key={s.label} className={`rounded-2xl ${s.bg} p-5`}>
                        <i className={`ti ${s.icon} text-white/80 text-lg`} />
                        <p className="font-display font-700 text-3xl text-white mt-2">{s.value?.toLocaleString('id-ID') ?? 0}</p>
                        <p className="text-xs text-white/75 mt-1">{s.label}</p>
                    </div>
                ))}
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
        </SuperAdminLayout>
    );
}

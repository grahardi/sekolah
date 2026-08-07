import { Link } from '@inertiajs/react';
import SuperAdminLayout from '../../Layouts/SuperAdminLayout';

export default function SekolahDetail({ sekolah }) {
    return (
        <SuperAdminLayout title={sekolah.nama} breadcrumb={['Dashboard', sekolah.nama]}>
            <Link href="/admin-portal" className="text-sm text-teal font-medium mb-4 inline-block">&larr; Kembali ke daftar sekolah</Link>

            <div className="rounded-2xl bg-white border border-navy/10 p-6 mb-6">
                <div className="flex items-start justify-between flex-wrap gap-3">
                    <div>
                        <h2 className="font-display font-700 text-xl text-navy">{sekolah.nama}</h2>
                        <p className="text-sm text-navy/60 mt-1">
                            {[sekolah.alamat, sekolah.kecamatan, sekolah.kabupaten_kota, sekolah.provinsi].filter(Boolean).join(', ')}
                        </p>
                    </div>
                    <div className="flex gap-2">
                        <span className="text-xs font-mono bg-teal-light text-teal rounded-full px-3 py-1">NPSN {sekolah.npsn}</span>
                        {sekolah.status_sekolah && <span className="text-xs font-mono bg-navy/5 text-navy/60 rounded-full px-3 py-1">{sekolah.status_sekolah}</span>}
                    </div>
                </div>
                <div className="grid grid-cols-2 sm:grid-cols-4 gap-4 mt-5 pt-5 border-t border-navy/10">
                    <div>
                        <p className="text-xs text-navy/40">Bentuk Pendidikan</p>
                        <p className="text-sm font-medium text-navy">{sekolah.bentuk_pendidikan || '-'}</p>
                    </div>
                    <div>
                        <p className="text-xs text-navy/40">Jenjang</p>
                        <p className="text-sm font-medium text-navy">{sekolah.jenjang_pendidikan || '-'}</p>
                    </div>
                    <div>
                        <p className="text-xs text-navy/40">Total Pengguna</p>
                        <p className="text-sm font-medium text-navy">{sekolah.users_count}</p>
                    </div>
                    <div>
                        <p className="text-xs text-navy/40">Total Siswa</p>
                        <p className="text-sm font-medium text-navy">{sekolah.siswas_count}</p>
                    </div>
                </div>
            </div>

            <h3 className="font-display font-600 text-lg text-navy mb-3">Pengguna di Sekolah Ini</h3>
            <div className="rounded-2xl bg-white border border-navy/10 overflow-hidden">
                <table className="w-full text-sm">
                    <thead>
                        <tr className="bg-navy/5 text-left text-navy/60 text-xs uppercase tracking-wide">
                            <th className="px-5 py-3">Nama</th>
                            <th className="px-5 py-3">Email</th>
                            <th className="px-5 py-3">Peran</th>
                        </tr>
                    </thead>
                    <tbody>
                        {sekolah.users.length === 0 && (
                            <tr><td colSpan={3} className="px-5 py-6 text-center text-navy/40">Belum ada pengguna.</td></tr>
                        )}
                        {sekolah.users.map((u) => (
                            <tr key={u.id} className="border-t border-navy/5">
                                <td className="px-5 py-3 font-medium text-navy">{u.name}</td>
                                <td className="px-5 py-3 text-navy/60">{u.email}</td>
                                <td className="px-5 py-3">
                                    <span className="text-xs font-mono bg-navy/5 text-navy/60 rounded-full px-2.5 py-1">{u.role}</span>
                                </td>
                            </tr>
                        ))}
                    </tbody>
                </table>
            </div>
        </SuperAdminLayout>
    );
}

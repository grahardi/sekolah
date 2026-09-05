import { Link } from '@inertiajs/react';
import SuperAdminLayout from '../../Layouts/SuperAdminLayout';

export default function EditPagesIndex({ pages }) {
    return (
        <SuperAdminLayout title="Edit Pages" breadcrumb={['Edit Pages']}>
            <p className="text-navy/60 mb-6 max-w-2xl">
                Edit konten teks yang tampil di halaman detail program (mis. sekolah.co.id/program/buku-induk).
            </p>

            <div className="rounded-2xl bg-white border border-navy/10 overflow-hidden">
                <table className="w-full text-sm">
                    <thead className="bg-navy/5">
                        <tr className="text-left text-navy/50 text-xs uppercase">
                            <th className="px-4 py-3">Halaman</th>
                            <th className="px-4 py-3">Ringkasan</th>
                            <th className="px-4 py-3">Status</th>
                            <th className="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody>
                        {pages.map((p) => (
                            <tr key={p.id} className="border-t border-navy/5">
                                <td className="px-4 py-3 font-600 text-navy">{p.title}</td>
                                <td className="px-4 py-3 text-navy/60">{p.summary}</td>
                                <td className="px-4 py-3">
                                    <span className={`text-xs font-600 px-2.5 py-1 rounded-full ${p.status === 'Aktif' ? 'bg-emerald-100 text-emerald-800' : 'bg-navy/5 text-navy/50'}`}>{p.status}</span>
                                </td>
                                <td className="px-4 py-3 text-right">
                                    <Link href={`/admin-portal/edit-pages/${p.id}`} className="text-teal text-sm font-600">Edit &rarr;</Link>
                                </td>
                            </tr>
                        ))}
                    </tbody>
                </table>
            </div>
        </SuperAdminLayout>
    );
}

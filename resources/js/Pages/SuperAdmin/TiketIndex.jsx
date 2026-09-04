import { Link, router } from '@inertiajs/react';
import SuperAdminLayout from '../../Layouts/SuperAdminLayout';

const STATUS_STYLE = {
    terbuka: { label: 'Terbuka', bg: 'bg-yellow-100', text: 'text-yellow-800' },
    diproses: { label: 'Sedang Diproses', bg: 'bg-blue-100', text: 'text-blue-800' },
    selesai: { label: 'Selesai', bg: 'bg-emerald-100', text: 'text-emerald-800' },
};

export default function TiketIndex({ tiketList, filterStatus, jumlahBelumDibaca }) {
    const gantiFilter = (status) => {
        router.get('/admin-portal/tiket', status ? { status } : {}, { preserveState: true });
    };

    return (
        <SuperAdminLayout title="Tiket Dukungan" breadcrumb={['Tiket Dukungan']}>
            <p className="text-navy/60 mb-6 max-w-2xl">
                Tiket bantuan dari seluruh sekolah pengguna sekolah.co.id.
                {jumlahBelumDibaca > 0 && (
                    <span className="ml-2 inline-block bg-red-500 text-white text-xs font-bold px-2 py-0.5 rounded-full">{jumlahBelumDibaca} belum dibaca</span>
                )}
            </p>

            <div className="flex items-center gap-2 mb-5 flex-wrap">
                <button onClick={() => gantiFilter(null)} className={`text-sm px-3 py-1.5 rounded-lg ${!filterStatus ? 'bg-navy text-white' : 'bg-navy/5 text-navy/70'}`}>Semua</button>
                <button onClick={() => gantiFilter('terbuka')} className={`text-sm px-3 py-1.5 rounded-lg ${filterStatus === 'terbuka' ? 'bg-navy text-white' : 'bg-navy/5 text-navy/70'}`}>Terbuka</button>
                <button onClick={() => gantiFilter('diproses')} className={`text-sm px-3 py-1.5 rounded-lg ${filterStatus === 'diproses' ? 'bg-navy text-white' : 'bg-navy/5 text-navy/70'}`}>Sedang Diproses</button>
                <button onClick={() => gantiFilter('selesai')} className={`text-sm px-3 py-1.5 rounded-lg ${filterStatus === 'selesai' ? 'bg-navy text-white' : 'bg-navy/5 text-navy/70'}`}>Selesai</button>
            </div>

            <div className="rounded-2xl bg-white border border-navy/10 overflow-hidden">
                <table className="w-full text-sm">
                    <thead className="bg-navy/5">
                        <tr className="text-left text-navy/50 text-xs uppercase">
                            <th className="px-4 py-3">Subjek</th>
                            <th className="px-4 py-3">Sekolah</th>
                            <th className="px-4 py-3">Status</th>
                            <th className="px-4 py-3">Terakhir Dibalas</th>
                            <th className="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody>
                        {tiketList.length === 0 && (
                            <tr><td colSpan={5} className="px-4 py-10 text-center text-navy/40">Belum ada tiket masuk.</td></tr>
                        )}
                        {tiketList.map((t) => (
                            <tr key={t.id} className="border-t border-navy/5">
                                <td className="px-4 py-3 font-600 text-navy">
                                    {t.subjek}
                                    {t.ada_balasan_belum_dibaca_admin && (
                                        <span className="ml-2 bg-red-500 text-white text-[9px] font-bold px-1.5 py-0.5 rounded-full align-middle">BARU</span>
                                    )}
                                </td>
                                <td className="px-4 py-3 text-navy/70">{t.sekolah_nama}</td>
                                <td className="px-4 py-3">
                                    <span className={`text-xs font-600 px-2.5 py-1 rounded-full ${STATUS_STYLE[t.status]?.bg} ${STATUS_STYLE[t.status]?.text}`}>{t.label_status}</span>
                                </td>
                                <td className="px-4 py-3 text-navy/40 text-xs">{t.dibalas_terakhir_at ?? '-'}</td>
                                <td className="px-4 py-3 text-right">
                                    <Link href={`/admin-portal/tiket/${t.id}`} className="text-teal text-sm font-600">Buka &rarr;</Link>
                                </td>
                            </tr>
                        ))}
                    </tbody>
                </table>
            </div>
        </SuperAdminLayout>
    );
}

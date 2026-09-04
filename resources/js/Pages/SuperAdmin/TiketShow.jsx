import { useForm, Link } from '@inertiajs/react';
import SuperAdminLayout from '../../Layouts/SuperAdminLayout';

const STATUS_STYLE = {
    terbuka: { label: 'Terbuka', bg: 'bg-yellow-100', text: 'text-yellow-800' },
    diproses: { label: 'Sedang Diproses', bg: 'bg-blue-100', text: 'text-blue-800' },
    selesai: { label: 'Selesai', bg: 'bg-emerald-100', text: 'text-emerald-800' },
};

export default function TiketShow({ tiket }) {
    const { data, setData, post, processing, reset } = useForm({ pesan: '', status: tiket.status });

    const kirim = (e) => {
        e.preventDefault();
        post(`/admin-portal/tiket/${tiket.id}/balas`, {
            preserveScroll: true,
            onSuccess: () => reset('pesan'),
        });
    };

    return (
        <SuperAdminLayout title={tiket.subjek} breadcrumb={['Tiket Dukungan', tiket.subjek]}>
            <Link href="/admin-portal/tiket" className="text-sm text-navy/50 mb-4 inline-block">&larr; Kembali ke daftar tiket</Link>

            <div className="flex items-center gap-3 mb-2 flex-wrap">
                <span className={`text-xs font-600 px-2.5 py-1 rounded-full ${STATUS_STYLE[tiket.status]?.bg} ${STATUS_STYLE[tiket.status]?.text}`}>{tiket.label_status}</span>
                <p className="text-sm text-navy/50">{tiket.sekolah_nama} &middot; dibuat oleh {tiket.dibuat_oleh} &middot; {tiket.created_at}</p>
            </div>

            <div className="rounded-2xl bg-white border border-navy/10 p-5 my-5 max-h-[520px] overflow-y-auto flex flex-col gap-4">
                {tiket.pesan.map((p) => (
                    <div key={p.id} className={`flex ${p.dari_superadmin ? 'justify-start' : 'justify-end'}`}>
                        <div className={`max-w-[75%] rounded-xl px-4 py-2.5 ${p.dari_superadmin ? 'bg-navy/5 text-navy' : 'bg-navy text-white'}`}>
                            <p className="text-[10px] opacity-70 font-600 mb-1">{p.nama_pengirim}</p>
                            <p className="text-sm whitespace-pre-wrap">{p.pesan}</p>
                            <p className="text-[9px] opacity-60 mt-1.5">{p.waktu}</p>
                        </div>
                    </div>
                ))}
            </div>

            <form onSubmit={kirim} className="rounded-2xl bg-white border border-navy/10 p-4">
                <textarea
                    value={data.pesan}
                    onChange={(e) => setData('pesan', e.target.value)}
                    rows={3}
                    required
                    placeholder="Tulis balasan ke sekolah..."
                    className="w-full rounded-lg border border-navy/15 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal/40"
                />
                <div className="flex items-center justify-between mt-3">
                    <select
                        value={data.status}
                        onChange={(e) => setData('status', e.target.value)}
                        className="rounded-lg border border-navy/15 px-3 py-2 text-sm"
                    >
                        <option value="terbuka">Terbuka</option>
                        <option value="diproses">Sedang Diproses</option>
                        <option value="selesai">Selesai</option>
                    </select>
                    <button type="submit" disabled={processing} className="bg-teal text-white text-sm font-600 px-4 py-2 rounded-lg disabled:opacity-50">
                        Kirim Balasan
                    </button>
                </div>
            </form>
        </SuperAdminLayout>
    );
}

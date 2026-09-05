import { useForm } from '@inertiajs/react';
import SuperAdminLayout from '../../Layouts/SuperAdminLayout';

export default function KontenHalamanIndex({ fields }) {
    const initial = {};
    fields.forEach((f) => { initial[f.kunci] = f.nilai; });

    const { data, setData, put, processing } = useForm({ konten: initial });

    const simpan = (e) => {
        e.preventDefault();
        put('/admin-portal/konten-halaman');
    };

    const setNilai = (kunci, val) => {
        setData('konten', { ...data.konten, [kunci]: val });
    };

    const grup = {
        'Hero (Bagian Atas)': fields.filter((f) => f.kunci.startsWith('hero_')),
        'Statistik': fields.filter((f) => f.kunci.startsWith('stat_')),
        'Fitur Unggulan': fields.filter((f) => f.kunci.startsWith('fitur_')),
    };

    return (
        <SuperAdminLayout title="Konten Halaman Depan" breadcrumb={['Konten Halaman Depan']}>
            <p className="text-navy/60 mb-6 max-w-2xl">
                Edit teks yang tampil di halaman depan (landing page) sekolah.co.id. Perubahan langsung tampil setelah disimpan.
            </p>

            <form onSubmit={simpan} className="space-y-6">
                {Object.entries(grup).map(([judulGrup, itemFields]) => (
                    <div key={judulGrup} className="rounded-2xl bg-white border border-navy/10 p-5">
                        <p className="font-600 text-navy mb-4">{judulGrup}</p>
                        <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            {itemFields.map((f) => (
                                <div key={f.kunci} className={f.kunci.includes('desc') ? 'sm:col-span-2' : ''}>
                                    <label className="text-xs text-navy/50 block mb-1">{f.label}</label>
                                    {f.kunci.includes('desc') ? (
                                        <textarea
                                            value={data.konten[f.kunci] ?? ''}
                                            onChange={(e) => setNilai(f.kunci, e.target.value)}
                                            rows={2}
                                            className="w-full rounded-lg border border-navy/15 px-3 py-2 text-sm"
                                        />
                                    ) : (
                                        <input
                                            value={data.konten[f.kunci] ?? ''}
                                            onChange={(e) => setNilai(f.kunci, e.target.value)}
                                            className="w-full rounded-lg border border-navy/15 px-3 py-2 text-sm"
                                        />
                                    )}
                                </div>
                            ))}
                        </div>
                    </div>
                ))}

                <button type="submit" disabled={processing} className="bg-teal text-white text-sm font-600 px-5 py-2.5 rounded-lg disabled:opacity-50">
                    Simpan Semua Perubahan
                </button>
            </form>
        </SuperAdminLayout>
    );
}

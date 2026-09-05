import { useState } from 'react';
import { useForm, router } from '@inertiajs/react';
import SuperAdminLayout from '../../Layouts/SuperAdminLayout';

export default function ShowcaseIndex({ items }) {
    const [editing, setEditing] = useState(undefined); // undefined = form tersembunyi, null = tambah baru, object = edit

    const { data, setData, post, processing, reset, errors } = useForm({
        judul: '', subjudul: '', deskripsi: '', link: '', urutan: 0, gambar: null, aktif: true,
    });

    const bukaForm = (item = null) => {
        setEditing(item);
        setData({
            judul: item?.judul ?? '',
            subjudul: item?.subjudul ?? '',
            deskripsi: item?.deskripsi ?? '',
            link: item?.link ?? '',
            urutan: item?.urutan ?? 0,
            gambar: null,
            aktif: item?.aktif ?? true,
        });
    };

    const simpan = (e) => {
        e.preventDefault();
        const url = editing ? `/admin-portal/showcase/${editing.id}` : '/admin-portal/showcase';
        post(url, {
            forceFormData: true,
            onSuccess: () => { setEditing(undefined); reset(); },
        });
    };

    const hapus = (item) => {
        if (!confirm(`Hapus showcase "${item.judul}"?`)) return;
        router.delete(`/admin-portal/showcase/${item.id}`);
    };

    return (
        <SuperAdminLayout title="Showcase Gallery" breadcrumb={['Showcase Gallery']}>
            <p className="text-navy/60 mb-6 max-w-2xl">
                Kelola galeri screenshot yang tampil di section "Showcase" halaman depan sekolah.co.id.
            </p>

            {editing !== undefined && (
                <div className="rounded-2xl bg-white border border-navy/10 p-5 mb-6">
                    <p className="font-600 text-navy mb-4">{editing ? `Edit: ${editing.judul}` : 'Tambah Showcase Baru'}</p>
                    <form onSubmit={simpan} className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label className="text-xs text-navy/50 block mb-1">Judul</label>
                            <input value={data.judul} onChange={(e) => setData('judul', e.target.value)} className="w-full rounded-lg border border-navy/15 px-3 py-2 text-sm" required />
                            {errors.judul && <p className="text-red-500 text-xs mt-1">{errors.judul}</p>}
                        </div>
                        <div>
                            <label className="text-xs text-navy/50 block mb-1">Subjudul (mis. "Fisika · Getaran")</label>
                            <input value={data.subjudul} onChange={(e) => setData('subjudul', e.target.value)} className="w-full rounded-lg border border-navy/15 px-3 py-2 text-sm" />
                        </div>
                        <div className="sm:col-span-2">
                            <label className="text-xs text-navy/50 block mb-1">Deskripsi</label>
                            <textarea value={data.deskripsi} onChange={(e) => setData('deskripsi', e.target.value)} rows={2} className="w-full rounded-lg border border-navy/15 px-3 py-2 text-sm" />
                        </div>
                        <div>
                            <label className="text-xs text-navy/50 block mb-1">Link tujuan (opsional)</label>
                            <input value={data.link} onChange={(e) => setData('link', e.target.value)} placeholder="/lab/bandul" className="w-full rounded-lg border border-navy/15 px-3 py-2 text-sm" />
                        </div>
                        <div>
                            <label className="text-xs text-navy/50 block mb-1">Urutan</label>
                            <input type="number" value={data.urutan} onChange={(e) => setData('urutan', e.target.value)} className="w-full rounded-lg border border-navy/15 px-3 py-2 text-sm" />
                        </div>
                        <div className="sm:col-span-2">
                            <label className="text-xs text-navy/50 block mb-1">Screenshot {editing?.gambar && '(kosongkan kalau tidak diganti)'}</label>
                            <input type="file" accept="image/*" onChange={(e) => setData('gambar', e.target.files[0])} className="w-full text-sm" />
                            {editing?.gambar && (
                                <img src={`/storage/${editing.gambar}`} className="mt-2 h-24 rounded-lg border border-navy/10" />
                            )}
                        </div>
                        <div className="sm:col-span-2 flex items-center gap-3">
                            <button type="submit" disabled={processing} className="bg-teal text-white text-sm font-600 px-4 py-2 rounded-lg disabled:opacity-50">
                                {editing ? 'Simpan Perubahan' : 'Tambah Showcase'}
                            </button>
                            <button type="button" onClick={() => setEditing(undefined)} className="text-sm text-navy/50">Batal</button>
                        </div>
                    </form>
                </div>
            )}

            {editing === undefined && (
                <button onClick={() => bukaForm(null)} className="bg-navy text-white text-sm font-600 px-4 py-2 rounded-lg mb-6">
                    + Tambah Showcase Baru
                </button>
            )}

            <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                {items.map((item) => (
                    <div key={item.id} className="rounded-2xl bg-white border border-navy/10 overflow-hidden">
                        <div className="h-36 bg-navy/5">
                            {item.gambar && <img src={`/storage/${item.gambar}`} className="w-full h-full object-cover" />}
                        </div>
                        <div className="p-4">
                            <p className="text-xs text-navy/40">{item.subjudul}</p>
                            <p className="font-600 text-navy">{item.judul}</p>
                            {!item.aktif && <span className="text-[10px] bg-red-100 text-red-700 px-1.5 py-0.5 rounded mt-1 inline-block">Nonaktif</span>}
                            <div className="flex items-center gap-3 mt-3">
                                <button onClick={() => bukaForm(item)} className="text-teal text-xs font-600">Edit</button>
                                <button onClick={() => hapus(item)} className="text-red-500 text-xs font-600">Hapus</button>
                            </div>
                        </div>
                    </div>
                ))}
            </div>
        </SuperAdminLayout>
    );
}

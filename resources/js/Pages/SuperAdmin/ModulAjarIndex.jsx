import { useState } from 'react';
import { useForm, router } from '@inertiajs/react';
import SuperAdminLayout from '../../Layouts/SuperAdminLayout';

export default function ModulAjarIndex({ modules, mapelList, filterMapel }) {
    const [editing, setEditing] = useState(undefined);

    const { data, setData, post, processing, reset, errors } = useForm({
        mapel: '', kelas: 7, fase: 'D', title: '', deskripsi: '', file_key: '', drive_id: '', file_docx: null, aktif: true,
    });

    const gantiFilter = (mapel) => {
        router.get('/admin-portal/modul-ajar', mapel ? { mapel } : {}, { preserveState: true });
    };

    const bukaForm = (item = null) => {
        setEditing(item);
        setData({
            mapel: item?.mapel ?? '',
            kelas: item?.kelas ?? 7,
            fase: item?.fase ?? 'D',
            title: item?.title ?? '',
            deskripsi: item?.deskripsi ?? '',
            file_key: item?.file_key ?? '',
            drive_id: '',
            file_docx: null,
            aktif: item?.aktif ?? true,
        });
    };

    const simpan = (e) => {
        e.preventDefault();
        const url = editing ? `/admin-portal/modul-ajar/${editing.id}` : '/admin-portal/modul-ajar';
        post(url, { forceFormData: true, onSuccess: () => { setEditing(undefined); reset(); } });
    };

    const hapus = (item) => {
        if (!confirm(`Hapus modul "${item.title}"?`)) return;
        router.delete(`/admin-portal/modul-ajar/${item.id}`);
    };

    return (
        <SuperAdminLayout title="Modul Ajar" breadcrumb={['Modul Ajar']}>
            <p className="text-navy/60 mb-6 max-w-2xl">
                Kelola katalog Modul Ajar Kurikulum Merdeka yang tampil di sekolah.co.id/modul-ajar.
            </p>

            <div className="flex items-center gap-2 mb-5 flex-wrap">
                <button onClick={() => gantiFilter(null)} className={`text-sm px-3 py-1.5 rounded-lg ${!filterMapel ? 'bg-navy text-white' : 'bg-navy/5 text-navy/70'}`}>Semua Mapel</button>
                {mapelList.map((m) => (
                    <button key={m} onClick={() => gantiFilter(m)} className={`text-sm px-3 py-1.5 rounded-lg ${filterMapel === m ? 'bg-navy text-white' : 'bg-navy/5 text-navy/70'}`}>{m}</button>
                ))}
            </div>

            {editing !== undefined && (
                <div className="rounded-2xl bg-white border border-navy/10 p-5 mb-6">
                    <p className="font-600 text-navy mb-4">{editing ? `Edit: ${editing.title}` : 'Tambah Modul Ajar Baru'}</p>
                    <form onSubmit={simpan} className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label className="text-xs text-navy/50 block mb-1">Mata Pelajaran</label>
                            <input value={data.mapel} onChange={(e) => setData('mapel', e.target.value)} className="w-full rounded-lg border border-navy/15 px-3 py-2 text-sm" required />
                        </div>
                        <div>
                            <label className="text-xs text-navy/50 block mb-1">Kelas</label>
                            <select value={data.kelas} onChange={(e) => setData('kelas', e.target.value)} className="w-full rounded-lg border border-navy/15 px-3 py-2 text-sm">
                                <option value={7}>7</option><option value={8}>8</option><option value={9}>9</option>
                            </select>
                        </div>
                        <div className="sm:col-span-2">
                            <label className="text-xs text-navy/50 block mb-1">Judul (mis. "Bab 1: Bilangan Bulat")</label>
                            <input value={data.title} onChange={(e) => setData('title', e.target.value)} className="w-full rounded-lg border border-navy/15 px-3 py-2 text-sm" required />
                            {errors.title && <p className="text-red-500 text-xs mt-1">{errors.title}</p>}
                        </div>
                        <div className="sm:col-span-2">
                            <label className="text-xs text-navy/50 block mb-1">Deskripsi</label>
                            <textarea value={data.deskripsi} onChange={(e) => setData('deskripsi', e.target.value)} rows={2} className="w-full rounded-lg border border-navy/15 px-3 py-2 text-sm" />
                        </div>
                        {!editing && (
                            <div>
                                <label className="text-xs text-navy/50 block mb-1">File Key (unik, mis. "matematika-bab8-baru")</label>
                                <input value={data.file_key} onChange={(e) => setData('file_key', e.target.value)} className="w-full rounded-lg border border-navy/15 px-3 py-2 text-sm" required />
                                {errors.file_key && <p className="text-red-500 text-xs mt-1">{errors.file_key}</p>}
                            </div>
                        )}
                        <div>
                            <label className="text-xs text-navy/50 block mb-1">Link Google Drive (fallback, opsional)</label>
                            <input value={data.drive_id} onChange={(e) => setData('drive_id', e.target.value)} placeholder="ID file Drive" className="w-full rounded-lg border border-navy/15 px-3 py-2 text-sm" />
                        </div>
                        <div className="sm:col-span-2">
                            <label className="text-xs text-navy/50 block mb-1">Upload File DOCX {editing?.exists_locally && '(sudah ada di server - upload utk ganti)'}</label>
                            <input type="file" accept=".doc,.docx" onChange={(e) => setData('file_docx', e.target.files[0])} className="w-full text-sm" />
                        </div>
                        <div className="sm:col-span-2 flex items-center gap-3">
                            <button type="submit" disabled={processing} className="bg-teal text-white text-sm font-600 px-4 py-2 rounded-lg disabled:opacity-50">
                                {editing ? 'Simpan Perubahan' : 'Tambah Modul'}
                            </button>
                            <button type="button" onClick={() => setEditing(undefined)} className="text-sm text-navy/50">Batal</button>
                        </div>
                    </form>
                </div>
            )}

            {editing === undefined && (
                <button onClick={() => bukaForm(null)} className="bg-navy text-white text-sm font-600 px-4 py-2 rounded-lg mb-6">
                    + Tambah Modul Ajar Baru
                </button>
            )}

            <div className="rounded-2xl bg-white border border-navy/10 overflow-hidden">
                <table className="w-full text-sm">
                    <thead className="bg-navy/5">
                        <tr className="text-left text-navy/50 text-xs uppercase">
                            <th className="px-4 py-3">Mapel</th>
                            <th className="px-4 py-3">Judul</th>
                            <th className="px-4 py-3">Sumber File</th>
                            <th className="px-4 py-3">Status</th>
                            <th className="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody>
                        {modules.map((m) => (
                            <tr key={m.id} className="border-t border-navy/5">
                                <td className="px-4 py-3 text-navy/70">{m.mapel}</td>
                                <td className="px-4 py-3 font-600 text-navy">{m.title}</td>
                                <td className="px-4 py-3 text-xs text-navy/50">{m.sumber}</td>
                                <td className="px-4 py-3">
                                    <span className={`text-[10px] font-600 px-2 py-0.5 rounded-full ${m.aktif ? 'bg-emerald-100 text-emerald-800' : 'bg-navy/5 text-navy/40'}`}>{m.aktif ? 'Aktif' : 'Nonaktif'}</span>
                                </td>
                                <td className="px-4 py-3 text-right whitespace-nowrap">
                                    <button onClick={() => bukaForm(m)} className="text-teal text-xs font-600 mr-3">Edit</button>
                                    <button onClick={() => hapus(m)} className="text-red-500 text-xs font-600">Hapus</button>
                                </td>
                            </tr>
                        ))}
                    </tbody>
                </table>
            </div>
        </SuperAdminLayout>
    );
}

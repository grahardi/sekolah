import { useForm, Link } from '@inertiajs/react';
import SuperAdminLayout from '../../Layouts/SuperAdminLayout';

export default function ModulAjarForm({ modul }) {
    const isEdit = !!modul;

    const { data, setData, post, processing, errors } = useForm({
        mapel: modul?.mapel ?? '',
        kelas: modul?.kelas ?? 7,
        fase: modul?.fase ?? 'D',
        title: modul?.title ?? '',
        deskripsi: modul?.deskripsi ?? '',
        file_key: modul?.file_key ?? '',
        drive_id: modul?.drive_id ?? '',
        file_docx: null,
        aktif: modul?.aktif ?? true,
    });

    const simpan = (e) => {
        e.preventDefault();
        const url = isEdit ? `/admin-portal/modul-ajar/${modul.id}` : '/admin-portal/modul-ajar';
        post(url, { forceFormData: true });
    };

    return (
        <SuperAdminLayout title={isEdit ? `Edit: ${modul.title}` : 'Tambah Modul Ajar'} breadcrumb={['Modul Ajar', isEdit ? modul.title : 'Tambah Baru']}>
            <Link href="/admin-portal/modul-ajar" className="text-sm text-navy/50 mb-4 inline-block">&larr; Kembali ke daftar modul</Link>

            <form onSubmit={simpan} className="rounded-2xl bg-white border border-navy/10 p-5 max-w-2xl space-y-4">
                <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label className="text-xs text-navy/50 block mb-1">Mata Pelajaran</label>
                        <input value={data.mapel} onChange={(e) => setData('mapel', e.target.value)} className="w-full rounded-lg border border-navy/15 px-3 py-2 text-sm" required />
                        {errors.mapel && <p className="text-red-500 text-xs mt-1">{errors.mapel}</p>}
                    </div>
                    <div>
                        <label className="text-xs text-navy/50 block mb-1">Kelas</label>
                        <select value={data.kelas} onChange={(e) => setData('kelas', e.target.value)} className="w-full rounded-lg border border-navy/15 px-3 py-2 text-sm">
                            <option value={7}>7</option><option value={8}>8</option><option value={9}>9</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label className="text-xs text-navy/50 block mb-1">Judul (mis. "Bab 1: Bilangan Bulat")</label>
                    <input value={data.title} onChange={(e) => setData('title', e.target.value)} className="w-full rounded-lg border border-navy/15 px-3 py-2 text-sm" required />
                    {errors.title && <p className="text-red-500 text-xs mt-1">{errors.title}</p>}
                </div>

                <div>
                    <label className="text-xs text-navy/50 block mb-1">Deskripsi</label>
                    <textarea value={data.deskripsi} onChange={(e) => setData('deskripsi', e.target.value)} rows={3} className="w-full rounded-lg border border-navy/15 px-3 py-2 text-sm" />
                </div>

                {!isEdit && (
                    <div>
                        <label className="text-xs text-navy/50 block mb-1">File Key (unik, huruf kecil & strip, mis. "matematika-bab8-baru")</label>
                        <input value={data.file_key} onChange={(e) => setData('file_key', e.target.value)} className="w-full rounded-lg border border-navy/15 px-3 py-2 text-sm" required />
                        {errors.file_key && <p className="text-red-500 text-xs mt-1">{errors.file_key}</p>}
                    </div>
                )}

                <div>
                    <label className="text-xs text-navy/50 block mb-1">Link Google Drive (fallback kalau belum upload file, opsional)</label>
                    <input value={data.drive_id} onChange={(e) => setData('drive_id', e.target.value)} placeholder="ID file Drive" className="w-full rounded-lg border border-navy/15 px-3 py-2 text-sm" />
                </div>

                <div>
                    <label className="text-xs text-navy/50 block mb-1">
                        Upload File DOCX {modul?.exists_locally && <span className="text-emerald-600">(sudah ada di server - upload utk ganti)</span>}
                    </label>
                    <input type="file" accept=".doc,.docx" onChange={(e) => setData('file_docx', e.target.files[0])} className="w-full text-sm" />
                </div>

                {isEdit && (
                    <label className="flex items-center gap-2 text-sm text-navy/70">
                        <input type="checkbox" checked={data.aktif} onChange={(e) => setData('aktif', e.target.checked)} />
                        Aktif (tampil di halaman publik)
                    </label>
                )}

                <div className="flex items-center gap-3 pt-2">
                    <button type="submit" disabled={processing} className="bg-teal text-white text-sm font-600 px-5 py-2.5 rounded-lg disabled:opacity-50">
                        {isEdit ? 'Simpan Perubahan' : 'Tambah Modul'}
                    </button>
                    <Link href="/admin-portal/modul-ajar" className="text-sm text-navy/50">Batal</Link>
                </div>
            </form>
        </SuperAdminLayout>
    );
}

import { useForm, Link } from '@inertiajs/react';
import SuperAdminLayout from '../../Layouts/SuperAdminLayout';

export default function EditPagesForm({ page }) {
    const { data, setData, put, processing, errors } = useForm({
        title: page.title,
        status: page.status,
        summary: page.summary,
        detail: page.detail,
        href: page.href ?? '',
        cta: page.cta ?? '',
        demo_href: page.demo_href ?? '',
    });

    const simpan = (e) => {
        e.preventDefault();
        put(`/admin-portal/edit-pages/${page.id}`);
    };

    return (
        <SuperAdminLayout title={`Edit: ${page.title}`} breadcrumb={['Edit Pages', page.title]}>
            <Link href="/admin-portal/edit-pages" className="text-sm text-navy/50 mb-4 inline-block">&larr; Kembali ke daftar halaman</Link>

            <form onSubmit={simpan} className="rounded-2xl bg-white border border-navy/10 p-5 max-w-2xl space-y-4">
                <div>
                    <label className="text-xs text-navy/50 block mb-1">Judul</label>
                    <input value={data.title} onChange={(e) => setData('title', e.target.value)} className="w-full rounded-lg border border-navy/15 px-3 py-2 text-sm" required />
                    {errors.title && <p className="text-red-500 text-xs mt-1">{errors.title}</p>}
                </div>

                <div>
                    <label className="text-xs text-navy/50 block mb-1">Status</label>
                    <select value={data.status} onChange={(e) => setData('status', e.target.value)} className="w-full rounded-lg border border-navy/15 px-3 py-2 text-sm">
                        <option value="Aktif">Aktif</option>
                        <option value="Segera">Segera Hadir</option>
                    </select>
                </div>

                <div>
                    <label className="text-xs text-navy/50 block mb-1">Ringkasan Singkat (tampil di kartu program)</label>
                    <input value={data.summary} onChange={(e) => setData('summary', e.target.value)} className="w-full rounded-lg border border-navy/15 px-3 py-2 text-sm" required />
                    {errors.summary && <p className="text-red-500 text-xs mt-1">{errors.summary}</p>}
                </div>

                <div>
                    <label className="text-xs text-navy/50 block mb-1">Detail Lengkap (tampil di halaman detail)</label>
                    <textarea value={data.detail} onChange={(e) => setData('detail', e.target.value)} rows={6} className="w-full rounded-lg border border-navy/15 px-3 py-2 text-sm" required />
                    {errors.detail && <p className="text-red-500 text-xs mt-1">{errors.detail}</p>}
                </div>

                <div className="grid grid-cols-2 gap-4">
                    <div>
                        <label className="text-xs text-navy/50 block mb-1">Link Tujuan (href, opsional)</label>
                        <input value={data.href} onChange={(e) => setData('href', e.target.value)} placeholder="/buku-induk" className="w-full rounded-lg border border-navy/15 px-3 py-2 text-sm" />
                    </div>
                    <div>
                        <label className="text-xs text-navy/50 block mb-1">Teks Tombol (CTA, opsional)</label>
                        <input value={data.cta} onChange={(e) => setData('cta', e.target.value)} placeholder="Buka Buku Induk" className="w-full rounded-lg border border-navy/15 px-3 py-2 text-sm" />
                    </div>
                </div>

                <div>
                    <label className="text-xs text-navy/50 block mb-1">Link Demo (opsional)</label>
                    <input value={data.demo_href} onChange={(e) => setData('demo_href', e.target.value)} placeholder="/demo" className="w-full rounded-lg border border-navy/15 px-3 py-2 text-sm" />
                </div>

                <button type="submit" disabled={processing} className="bg-teal text-white text-sm font-600 px-5 py-2.5 rounded-lg disabled:opacity-50">
                    Simpan Perubahan
                </button>
            </form>
        </SuperAdminLayout>
    );
}

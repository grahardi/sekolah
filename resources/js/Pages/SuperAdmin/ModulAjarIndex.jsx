import { Link, router } from '@inertiajs/react';
import SuperAdminLayout from '../../Layouts/SuperAdminLayout';

function IconEdit(p) { return <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" {...p}><path d="M12 20h9" /><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4Z" /></svg>; }
function IconTrash(p) { return <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" {...p}><path d="M3 6h18" /><path d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2" /><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6" /><path d="M10 11v6M14 11v6" /></svg>; }
function IconPlus(p) { return <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" {...p}><path d="M12 5v14M5 12h14" /></svg>; }
function IconSearch(p) { return <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" {...p}><circle cx="11" cy="11" r="8" /><path d="m21 21-4.3-4.3" /></svg>; }

export default function ModulAjarIndex({ modules, mapelList, filterMapel, search }) {
    const gantiFilter = (mapel) => {
        router.get('/admin-portal/modul-ajar', { mapel: mapel || undefined, search: search || undefined }, { preserveState: true });
    };

    const cari = (e) => {
        e.preventDefault();
        const q = e.target.search.value;
        router.get('/admin-portal/modul-ajar', { mapel: filterMapel || undefined, search: q || undefined }, { preserveState: true });
    };

    const hapus = (item) => {
        if (!confirm(`Hapus modul "${item.title}"?`)) return;
        router.delete(`/admin-portal/modul-ajar/${item.id}`);
    };

    return (
        <SuperAdminLayout title="Modul Ajar" breadcrumb={['Modul Ajar']}>
            <div className="flex items-center justify-between mb-6 flex-wrap gap-3">
                <p className="text-navy/60 max-w-xl">
                    Kelola katalog Modul Ajar Kurikulum Merdeka yang tampil di sekolah.co.id/modul-ajar.
                </p>
                <Link href="/admin-portal/modul-ajar/create" className="inline-flex items-center gap-1.5 bg-teal text-white text-sm font-600 px-4 py-2 rounded-lg whitespace-nowrap">
                    <IconPlus className="w-4 h-4" /> Tambah Modul
                </Link>
            </div>

            <div className="flex items-center gap-3 mb-5 flex-wrap">
                <form onSubmit={cari} className="relative">
                    <IconSearch className="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-navy/30" />
                    <input name="search" defaultValue={search ?? ''} placeholder="Cari judul/deskripsi..." className="pl-9 pr-3 py-2 rounded-lg border border-navy/15 text-sm w-64" />
                </form>
                <button onClick={() => gantiFilter(null)} className={`text-sm px-3 py-1.5 rounded-lg ${!filterMapel ? 'bg-navy text-white' : 'bg-navy/5 text-navy/70'}`}>Semua Mapel</button>
                {mapelList.map((m) => (
                    <button key={m} onClick={() => gantiFilter(m)} className={`text-sm px-3 py-1.5 rounded-lg ${filterMapel === m ? 'bg-navy text-white' : 'bg-navy/5 text-navy/70'}`}>{m}</button>
                ))}
            </div>

            <div className="rounded-2xl bg-white border border-navy/10 overflow-hidden">
                <table className="w-full text-sm">
                    <thead className="bg-navy/5">
                        <tr className="text-left text-navy/50 text-xs uppercase">
                            <th className="px-4 py-3">Mapel</th>
                            <th className="px-4 py-3">Judul</th>
                            <th className="px-4 py-3">Sumber File</th>
                            <th className="px-4 py-3">Status</th>
                            <th className="px-4 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        {modules.data.length === 0 && (
                            <tr><td colSpan={5} className="px-4 py-10 text-center text-navy/40">Tidak ada modul yang cocok.</td></tr>
                        )}
                        {modules.data.map((m) => (
                            <tr key={m.id} className="border-t border-navy/5">
                                <td className="px-4 py-3 text-navy/70">{m.mapel}</td>
                                <td className="px-4 py-3">
                                    <Link href={`/admin-portal/modul-ajar/${m.id}/edit`} className="font-600 text-navy hover:text-teal">{m.title}</Link>
                                </td>
                                <td className="px-4 py-3 text-xs text-navy/50">{m.sumber}</td>
                                <td className="px-4 py-3">
                                    <span className={`text-[10px] font-600 px-2 py-0.5 rounded-full ${m.aktif ? 'bg-emerald-100 text-emerald-800' : 'bg-navy/5 text-navy/40'}`}>{m.aktif ? 'Aktif' : 'Nonaktif'}</span>
                                </td>
                                <td className="px-4 py-3 text-right whitespace-nowrap">
                                    <Link href={`/admin-portal/modul-ajar/${m.id}/edit`} className="inline-flex items-center gap-1 text-teal text-xs font-600 mr-2 px-2.5 py-1.5 rounded-lg bg-teal-light/60 hover:bg-teal-light">
                                        <IconEdit className="w-3.5 h-3.5" /> Edit
                                    </Link>
                                    <button onClick={() => hapus(m)} className="inline-flex items-center gap-1 text-red-600 text-xs font-600 px-2.5 py-1.5 rounded-lg bg-red-50 hover:bg-red-100">
                                        <IconTrash className="w-3.5 h-3.5" /> Hapus
                                    </button>
                                </td>
                            </tr>
                        ))}
                    </tbody>
                </table>
            </div>

            {modules.links && modules.links.length > 3 && (
                <div className="flex items-center gap-1 mt-5 flex-wrap">
                    {modules.links.map((link, i) => (
                        <button
                            key={i}
                            disabled={!link.url}
                            onClick={() => link.url && router.get(link.url, {}, { preserveState: true })}
                            className={`text-sm px-3 py-1.5 rounded-lg ${link.active ? 'bg-navy text-white' : link.url ? 'bg-navy/5 text-navy/70 hover:bg-navy/10' : 'text-navy/20 cursor-default'}`}
                            dangerouslySetInnerHTML={{ __html: link.label }}
                        />
                    ))}
                </div>
            )}
        </SuperAdminLayout>
    );
}

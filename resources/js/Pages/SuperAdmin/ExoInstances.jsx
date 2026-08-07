import { useState } from 'react';
import { useForm, router } from '@inertiajs/react';
import SuperAdminLayout from '../../Layouts/SuperAdminLayout';

export default function ExoInstances({ instances }) {
    const [showTambah, setShowTambah] = useState(false);
    const [editKey, setEditKey] = useState(null); // id instance yg lagi diedit license key-nya

    const formTambah = useForm({ nama: '', slug: '', path: '' });
    const formKey = useForm({ license_key: '' });
    const formDb = useForm({ db_host: '', db_port: '5432', db_name: '', db_user: '', db_pass: '' });
    const [editDb, setEditDb] = useState(null);

    const submitTambah = (e) => {
        e.preventDefault();
        formTambah.post('/admin-portal/exo', { onSuccess: () => { setShowTambah(false); formTambah.reset(); } });
    };

    const submitKey = (e, instance) => {
        e.preventDefault();
        formKey.put(`/admin-portal/exo/${instance.id}/license-key`, { onSuccess: () => { setEditKey(null); formKey.reset(); } });
    };

    const submitDb = (e, instance) => {
        e.preventDefault();
        formDb.put(`/admin-portal/exo/${instance.id}/db-creds`, { onSuccess: () => { setEditDb(null); formDb.reset(); } });
    };

    const tesKoneksi = (instance) => {
        router.post(`/admin-portal/exo/${instance.id}/test-connection`, {}, { preserveScroll: true });
    };

    const jalankan = (instance) => {
        if (! confirm(`Jalankan ${instance.nama}? Ini akan menjalankan perintah nohup di server.`)) return;
        router.post(`/admin-portal/exo/${instance.id}/run`, {}, { preserveScroll: true });
    };

    const hapus = (instance) => {
        if (! confirm(`Hapus ${instance.nama} dari daftar? (File di server tidak ikut terhapus)`)) return;
        router.delete(`/admin-portal/exo/${instance.id}`, { preserveScroll: true });
    };

    return (
        <SuperAdminLayout title="Extraordinary CBT" breadcrumb={['Extraordinary CBT']}>
            <p className="text-navy/60 mb-2 max-w-2xl">
                Kelola instance server Extraordinary CBT yang ter-install di server yang sama.
                Port cuma bisa dilihat (diatur langsung di server), License Key bisa diubah dari sini.
            </p>
            <p className="text-xs text-amber-700 bg-yellow-50 rounded-lg px-4 py-2.5 mb-6 max-w-2xl">
                <i className="ti ti-alert-triangle mr-1" /> Tombol "Jalankan" perlu izin eksekusi yang benar di server (lihat catatan admin).
                Kalau gagal, jalankan manual dulu lewat terminal untuk memastikan izinnya benar.
            </p>

            <button onClick={() => setShowTambah(!showTambah)} className="mb-5 px-4 py-2.5 rounded-lg bg-teal text-white text-sm font-medium hover:bg-teal/90">
                <i className="ti ti-plus mr-1" /> Tambah Instance
            </button>

            {showTambah && (
                <form onSubmit={submitTambah} className="rounded-2xl bg-white border border-navy/10 p-5 mb-6 max-w-lg">
                    <div className="grid grid-cols-1 gap-3">
                        <div>
                            <label className="text-xs font-medium text-navy/60">Nama Label</label>
                            <input value={formTambah.data.nama} onChange={(e) => formTambah.setData('nama', e.target.value)}
                                placeholder="mis. Ujian Sekolah, PSAJ" className="w-full mt-1 rounded-lg border border-navy/15 px-3 py-2 text-sm" />
                            {formTambah.errors.nama && <p className="text-xs text-red-600 mt-1">{formTambah.errors.nama}</p>}
                        </div>
                        <div>
                            <label className="text-xs font-medium text-navy/60">Slug (unik)</label>
                            <input value={formTambah.data.slug} onChange={(e) => formTambah.setData('slug', e.target.value)}
                                placeholder="mis. ujian, psaj, sma" className="w-full mt-1 rounded-lg border border-navy/15 px-3 py-2 text-sm" />
                            {formTambah.errors.slug && <p className="text-xs text-red-600 mt-1">{formTambah.errors.slug}</p>}
                        </div>
                        <div>
                            <label className="text-xs font-medium text-navy/60">Path Instalasi (absolut)</label>
                            <input value={formTambah.data.path} onChange={(e) => formTambah.setData('path', e.target.value)}
                                placeholder="/home/aginza/ujian" className="w-full mt-1 rounded-lg border border-navy/15 px-3 py-2 text-sm font-mono" />
                            {formTambah.errors.path && <p className="text-xs text-red-600 mt-1">{formTambah.errors.path}</p>}
                        </div>
                    </div>
                    <button type="submit" disabled={formTambah.processing} className="mt-4 px-4 py-2 rounded-lg bg-teal text-white text-sm font-medium disabled:opacity-50">
                        Simpan
                    </button>
                </form>
            )}

            <div className="grid grid-cols-1 sm:grid-cols-2 gap-5">
                {instances.length === 0 && (
                    <p className="text-navy/40 text-sm col-span-2">Belum ada instance terdaftar. Klik "Tambah Instance" di atas.</p>
                )}
                {instances.map((i) => (
                    <div key={i.id} className="rounded-2xl bg-white border border-navy/10 p-5">
                        <div className="flex items-start justify-between mb-3">
                            <div>
                                <p className="font-display font-700 text-navy">{i.nama}</p>
                                <p className="text-xs text-navy/40 font-mono">{i.path}</p>
                            </div>
                            <button onClick={() => hapus(i)} className="text-red-400 hover:text-red-600 text-xs">
                                <i className="ti ti-trash" />
                            </button>
                        </div>

                        <div className="flex items-center gap-4 text-xs text-navy/60 mb-4">
                            <span>Port: <span className="font-mono font-medium text-navy">{i.port_saat_ini || '-'}</span> <span className="text-navy/30">(view only)</span></span>
                            <span className={`px-2 py-0.5 rounded-full ${i.license_key_terisi ? 'bg-emerald-50 text-emerald-700' : 'bg-red-50 text-red-600'}`}>
                                {i.license_key_terisi ? 'License terisi' : 'License kosong'}
                            </span>
                            <span className={`px-2 py-0.5 rounded-full ${i.db_terisi ? 'bg-emerald-50 text-emerald-700' : 'bg-red-50 text-red-600'}`}>
                                {i.db_terisi ? 'DB terhubung' : 'DB belum diisi'}
                            </span>
                        </div>

                        {editKey === i.id ? (
                            <form onSubmit={(e) => submitKey(e, i)} className="mb-3">
                                <input
                                    type="text"
                                    value={formKey.data.license_key}
                                    onChange={(e) => formKey.setData('license_key', e.target.value)}
                                    placeholder="Masukkan Ecosystem/License Key"
                                    className="w-full rounded-lg border border-navy/15 px-3 py-2 text-sm font-mono mb-2"
                                    autoFocus
                                />
                                <div className="flex gap-2">
                                    <button type="submit" disabled={formKey.processing} className="px-3 py-1.5 rounded-lg bg-teal text-white text-xs font-medium disabled:opacity-50">Simpan</button>
                                    <button type="button" onClick={() => setEditKey(null)} className="px-3 py-1.5 rounded-lg bg-navy/5 text-navy/60 text-xs">Batal</button>
                                </div>
                            </form>
                        ) : (
                            <button onClick={() => setEditKey(i.id)} className="text-xs text-teal hover:underline mb-3 block">
                                <i className="ti ti-key mr-1" /> {i.license_key_terisi ? 'Ubah' : 'Isi'} License Key
                            </button>
                        )}

                        {editDb === i.id ? (
                            <form onSubmit={(e) => submitDb(e, i)} className="mb-3 space-y-2">
                                <input type="text" value={formDb.data.db_host} onChange={(e) => formDb.setData('db_host', e.target.value)} placeholder="Host (mis. localhost)" className="w-full rounded-lg border border-navy/15 px-3 py-2 text-xs font-mono" />
                                <input type="text" value={formDb.data.db_port} onChange={(e) => formDb.setData('db_port', e.target.value)} placeholder="Port" className="w-full rounded-lg border border-navy/15 px-3 py-2 text-xs font-mono" />
                                <input type="text" value={formDb.data.db_name} onChange={(e) => formDb.setData('db_name', e.target.value)} placeholder="Nama Database" className="w-full rounded-lg border border-navy/15 px-3 py-2 text-xs font-mono" />
                                <input type="text" value={formDb.data.db_user} onChange={(e) => formDb.setData('db_user', e.target.value)} placeholder="Username" className="w-full rounded-lg border border-navy/15 px-3 py-2 text-xs font-mono" />
                                <input type="password" value={formDb.data.db_pass} onChange={(e) => formDb.setData('db_pass', e.target.value)} placeholder="Password" className="w-full rounded-lg border border-navy/15 px-3 py-2 text-xs font-mono" />
                                <div className="flex gap-2">
                                    <button type="submit" disabled={formDb.processing} className="px-3 py-1.5 rounded-lg bg-teal text-white text-xs font-medium disabled:opacity-50">Simpan</button>
                                    <button type="button" onClick={() => setEditDb(null)} className="px-3 py-1.5 rounded-lg bg-navy/5 text-navy/60 text-xs">Batal</button>
                                </div>
                            </form>
                        ) : (
                            <div className="flex items-center gap-3 mb-3">
                                <button onClick={() => setEditDb(i.id)} className="text-xs text-teal hover:underline">
                                    <i className="ti ti-database mr-1" /> {i.db_terisi ? 'Ubah' : 'Isi'} Kredensial DB
                                </button>
                                {i.db_terisi && (
                                    <button onClick={() => tesKoneksi(i)} className="text-xs text-navy/50 hover:text-navy hover:underline">
                                        <i className="ti ti-plug mr-1" /> Tes Koneksi
                                    </button>
                                )}
                            </div>
                        )}

                        <button onClick={() => jalankan(i)} className="w-full px-3 py-2 rounded-lg bg-emerald-500 text-white text-sm font-medium hover:bg-emerald-600">
                            <i className="ti ti-player-play mr-1" /> Jalankan Server
                        </button>
                        {i.terakhir_dijalankan && (
                            <p className="text-[11px] text-navy/40 mt-2">Terakhir dijalankan: {new Date(i.terakhir_dijalankan).toLocaleString('id-ID')}</p>
                        )}
                    </div>
                ))}
            </div>
        </SuperAdminLayout>
    );
}

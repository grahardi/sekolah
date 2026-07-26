import { useMemo, useState } from 'react';
import LabLayout from '../../Layouts/LabLayout';

const SUBJECT_COLORS = {
    'Matematika': 'bg-sky-400',
    'IPA': 'bg-emerald-400',
    'IPS': 'bg-amber-400',
    'Bahasa Indonesia': 'bg-rose-400',
    'Bahasa Inggris': 'bg-violet-400',
    'PPKn': 'bg-orange-400',
    'Pendidikan Agama': 'bg-lime-400',
    'Seni Budaya': 'bg-fuchsia-400',
    'PJOK': 'bg-cyan-400',
    'Informatika': 'bg-pink-400',
};

// Shuffle sekali per kunjungan halaman (bukan re-render), untuk kesan "acak"
// yang stabil selama pengguna menjelajah, bukan berubah tiap detik.
function shuffle(array) {
    const copy = [...array];
    for (let i = copy.length - 1; i > 0; i--) {
        const j = Math.floor(Math.random() * (i + 1));
        [copy[i], copy[j]] = [copy[j], copy[i]];
    }
    return copy;
}

export default function Index({ modules = [], mapelList = [] }) {
    const [query, setQuery] = useState('');
    const [activeMapel, setActiveMapel] = useState('Semua');
    const [randomPicks] = useState(() => shuffle(modules).slice(0, 6));

    const filtered = useMemo(() => {
        return modules.filter((m) => {
            const matchesMapel = activeMapel === 'Semua' || m.mapel === activeMapel;
            const q = query.trim().toLowerCase();
            const matchesQuery = !q || m.title.toLowerCase().includes(q) || m.desc.toLowerCase().includes(q) || m.mapel.toLowerCase().includes(q);
            return matchesMapel && matchesQuery;
        });
    }, [modules, activeMapel, query]);

    return (
        <LabLayout title="Modul Ajar" breadcrumb={['Home', 'Modul Ajar']}>
            <p className="text-navy/60 mb-6 max-w-2xl">
                Modul Ajar SMP Kurikulum Merdeka (Fase D, kelas 7-9), siap unduh dalam
                format PDF/DOCX per mata pelajaran.
            </p>

            {/* Pencarian */}
            <div className="relative mb-6 max-w-lg">
                <input
                    type="text"
                    value={query}
                    onChange={(e) => setQuery(e.target.value)}
                    placeholder="Cari modul, mis. 'Pythagoras' atau 'teks deskripsi'..."
                    className="w-full rounded-lg border border-navy/15 bg-white px-4 py-2.5 text-sm text-navy placeholder:text-navy/40 focus:outline-none focus:ring-2 focus:ring-teal/40"
                />
            </div>

            {/* Tab mapel */}
            <div className="flex flex-wrap gap-2 mb-8">
                {['Semua', ...mapelList].map((mapel) => (
                    <button
                        key={mapel}
                        onClick={() => setActiveMapel(mapel)}
                        className={`text-sm font-medium rounded-full px-4 py-2 transition-colors ${
                            activeMapel === mapel
                                ? 'bg-teal text-white'
                                : 'bg-white border border-navy/15 text-navy/70 hover:border-teal/40'
                        }`}
                    >
                        {mapel}
                    </button>
                ))}
            </div>

            {/* Modul pilihan acak - hanya tampil saat belum ada pencarian/filter */}
            {activeMapel === 'Semua' && query === '' && (
                <div className="mb-10">
                    <h2 className="font-display font-600 text-lg text-navy mb-3">Modul Pilihan Hari Ini</h2>
                    <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                        {randomPicks.map((m, i) => (
                            <ModuleCard key={`${m.mapel}-${m.title}`} m={m} />
                        ))}
                    </div>
                </div>
            )}

            <h2 className="font-display font-600 text-lg text-navy mb-3">
                {activeMapel === 'Semua' ? `Semua Modul (${filtered.length})` : `${activeMapel} (${filtered.length})`}
            </h2>
            {filtered.length === 0 ? (
                <p className="text-navy/50 text-sm">Tidak ada modul yang cocok dengan pencarianmu.</p>
            ) : (
                <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                    {filtered.map((m) => (
                        <ModuleCard key={`${m.mapel}-${m.title}`} m={m} />
                    ))}
                </div>
            )}
        </LabLayout>
    );
}

function ModuleCard({ m }) {
    const color = SUBJECT_COLORS[m.mapel] || 'bg-slate-400';

    return (
        <div className="rounded-2xl bg-white border border-navy/10 flex flex-col min-h-[210px] overflow-hidden">
            <div className={`h-3 w-full ${color}`} />
            <div className="p-5 flex flex-col flex-1 justify-between">
                <div>
                    <div className="flex items-center justify-between mb-1.5">
                        <span className="text-xs font-mono text-navy/40">{m.mapel} · Kelas {m.kelas}</span>
                        <span className="text-[10px] font-mono uppercase tracking-wide bg-navy/5 text-navy/50 rounded-full px-2 py-0.5">
                            {m.tipe}
                        </span>
                    </div>
                    <h3 className="font-display font-600 text-navy leading-snug">{m.title}</h3>
                    <p className="text-sm text-navy/60 mt-2">{m.desc}</p>
                </div>
                <div className="mt-4 flex items-center justify-between">
                    <a
                        href={m.download_url}
                        target="_blank"
                        rel="noopener noreferrer"
                        className="inline-flex items-center gap-1.5 text-sm font-medium bg-teal-light text-teal rounded-lg px-3 py-1.5 hover:bg-teal hover:text-white transition-colors"
                    >
                        <DownloadIcon className="w-4 h-4" />
                        Unduh
                    </a>
                    <span className="text-[10px] text-navy/40">via {m.sumber}</span>
                </div>
            </div>
        </div>
    );
}

function DownloadIcon(p) {
    return (
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" {...p}>
            <path d="M12 3v12m0 0l-4-4m4 4l4-4M4 21h16" strokeLinecap="round" strokeLinejoin="round" />
        </svg>
    );
}

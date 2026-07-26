import { useState } from 'react';
import LabLayout from '../../Layouts/LabLayout';

const ANIMAL_PARTS = [
    { key: 'nukleus', label: 'Nukleus', cx: 200, cy: 150, r: 35, color: '#8FD3FE', desc: 'Mengatur seluruh aktivitas sel dan menyimpan materi genetik (DNA).' },
    { key: 'mitokondria', label: 'Mitokondria', cx: 110, cy: 220, r: 22, color: '#FBBF24', desc: 'Tempat respirasi sel untuk menghasilkan energi (ATP).' },
    { key: 'ribosom', label: 'Ribosom', cx: 290, cy: 100, r: 8, color: '#E63946', desc: 'Tempat sintesis protein.' },
    { key: 'membran', label: 'Membran Sel', cx: 200, cy: 200, r: 150, color: '#7C8AA5', desc: 'Mengatur keluar-masuknya zat dari dan ke dalam sel.' },
];

const PLANT_PARTS = [
    { key: 'nukleus', label: 'Nukleus', cx: 200, cy: 150, r: 35, color: '#8FD3FE', desc: 'Mengatur seluruh aktivitas sel dan menyimpan materi genetik (DNA).' },
    { key: 'kloroplas', label: 'Kloroplas', cx: 120, cy: 230, r: 24, color: '#3ABF6B', desc: 'Tempat berlangsungnya fotosintesis, mengandung klorofil.' },
    { key: 'vakuola', label: 'Vakuola', cx: 280, cy: 220, r: 40, color: '#8FD3FE', desc: 'Menyimpan cadangan makanan, air, dan zat sisa metabolisme.' },
    { key: 'dinding', label: 'Dinding Sel', cx: 200, cy: 200, r: 160, color: '#1B4332', desc: 'Memberi bentuk tetap dan melindungi sel tumbuhan.' },
];

export default function CellStructure({ category = 'Biologi' }) {
    const [mode, setMode] = useState('hewan');
    const [selected, setSelected] = useState(null);
    const parts = mode === 'hewan' ? ANIMAL_PARTS : PLANT_PARTS;

    return (
        <LabLayout title="Struktur Sel Hewan & Tumbuhan" breadcrumb={['Home', 'Simulasi', category]}>
            <div className="grid grid-cols-1 lg:grid-cols-[1fr_320px] gap-6">
                <div className="rounded-2xl bg-[#0F1729] border border-slate/20 flex items-center justify-center p-4">
                    <svg viewBox="0 0 400 400" className="w-full max-w-md">
                        {[...parts].reverse().map((p) => (
                            <circle
                                key={p.key}
                                cx={p.cx} cy={p.cy} r={p.r}
                                fill={p.color}
                                fillOpacity={selected === p.key ? 0.9 : 0.5}
                                stroke={selected === p.key ? '#FFFFFF' : 'none'}
                                strokeWidth={2}
                                className="cursor-pointer transition-all"
                                onClick={() => setSelected(p.key)}
                            />
                        ))}
                    </svg>
                </div>
                <div className="control-panel space-y-4">
                    <div className="flex gap-2">
                        {['hewan', 'tumbuhan'].map((m) => (
                            <button
                                key={m}
                                onClick={() => { setMode(m); setSelected(null); }}
                                className={`flex-1 text-sm rounded-lg py-2 capitalize ${mode === m ? 'bg-amber text-ink' : 'bg-white/10 text-slate'}`}
                            >
                                Sel {m}
                            </button>
                        ))}
                    </div>
                    <p className="text-xs text-slate/70">Klik bagian sel pada diagram untuk melihat penjelasannya.</p>
                    <div className="space-y-2">
                        {parts.map((p) => (
                            <button
                                key={p.key}
                                onClick={() => setSelected(p.key)}
                                className={`w-full text-left rounded-lg px-3 py-2 text-sm transition-colors ${
                                    selected === p.key ? 'bg-amber/20 text-amber' : 'text-slate hover:bg-white/5'
                                }`}
                            >
                                {p.label}
                            </button>
                        ))}
                    </div>
                    {selected && (
                        <div className="pt-2 border-t border-slate/15">
                            <p className="text-paper text-sm">{parts.find((p) => p.key === selected)?.desc}</p>
                        </div>
                    )}
                </div>
            </div>
        </LabLayout>
    );
}

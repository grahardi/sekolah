import { useState } from 'react';
import LabLayout from '../../Layouts/LabLayout';

const MALE_PARTS = [
    { key: 'testis', label: 'Testis', cx: 180, cy: 260, r: 22, desc: 'Menghasilkan sel sperma dan hormon testosteron.' },
    { key: 'vas-deferens', label: 'Vas Deferens', cx: 200, cy: 200, r: 12, desc: 'Menyalurkan sperma dari testis menuju uretra.' },
];

const FEMALE_PARTS = [
    { key: 'ovarium', label: 'Ovarium', cx: 150, cy: 180, r: 20, desc: 'Menghasilkan sel telur (ovum) dan hormon estrogen-progesteron.' },
    { key: 'tuba-falopi', label: 'Tuba Falopi', cx: 150, cy: 140, r: 12, desc: 'Tempat terjadinya pembuahan (fertilisasi) sel telur oleh sperma.' },
    { key: 'rahim', label: 'Rahim (Uterus)', cx: 200, cy: 200, r: 35, desc: 'Tempat berkembangnya janin selama masa kehamilan.' },
];

export default function HumanReproduction({ category = 'Biologi' }) {
    const [mode, setMode] = useState('perempuan');
    const [selected, setSelected] = useState(null);
    const parts = mode === 'perempuan' ? FEMALE_PARTS : MALE_PARTS;

    return (
        <LabLayout title="Sistem Reproduksi Manusia" breadcrumb={['Home', 'Simulasi', category]}>
            <div className="grid grid-cols-1 lg:grid-cols-[1fr_320px] gap-6">
                <div className="rounded-2xl bg-[#0F1729] border border-slate/20 flex items-center justify-center p-4">
                    <svg viewBox="0 0 350 350" className="w-full max-w-md">
                        {parts.map((p) => (
                            <circle
                                key={p.key}
                                cx={p.cx} cy={p.cy} r={p.r}
                                fill={selected === p.key ? '#FBBF24' : 'rgba(251,191,36,0.4)'}
                                stroke={selected === p.key ? '#FFFFFF' : 'none'}
                                strokeWidth={2}
                                className="cursor-pointer"
                                onClick={() => setSelected(p.key)}
                            />
                        ))}
                    </svg>
                </div>
                <div className="control-panel space-y-4">
                    <div className="flex gap-2">
                        {['perempuan', 'laki-laki'].map((m) => (
                            <button
                                key={m}
                                onClick={() => { setMode(m); setSelected(null); }}
                                className={`flex-1 text-sm rounded-lg py-2 capitalize ${mode === m ? 'bg-amber text-ink' : 'bg-white/10 text-slate'}`}
                            >
                                {m}
                            </button>
                        ))}
                    </div>
                    <p className="text-xs text-slate/70">Klik bagian organ pada diagram untuk melihat fungsinya.</p>
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

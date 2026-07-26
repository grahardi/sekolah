import { useState } from 'react';
import LabLayout from '../../Layouts/LabLayout';

const ORGANS = [
    { key: 'mulut', label: 'Mulut', cx: 200, cy: 40, r: 20, desc: 'Mencerna makanan secara mekanik (kunyah) dan kimiawi (enzim amilase pada air liur).' },
    { key: 'kerongkongan', label: 'Kerongkongan', cx: 200, cy: 90, r: 14, desc: 'Menyalurkan makanan dari mulut ke lambung lewat gerak peristaltik.' },
    { key: 'lambung', label: 'Lambung', cx: 220, cy: 150, r: 35, desc: 'Mencerna makanan dengan asam lambung dan enzim pepsin.' },
    { key: 'usus-halus', label: 'Usus Halus', cx: 200, cy: 240, r: 55, desc: 'Tempat pencernaan kimiawi selesai dan penyerapan sari makanan.' },
    { key: 'usus-besar', label: 'Usus Besar', cx: 210, cy: 320, r: 40, desc: 'Menyerap air dan membentuk sisa makanan menjadi feses.' },
];

export default function DigestiveSystem({ category = 'Biologi' }) {
    const [selected, setSelected] = useState(null);

    return (
        <LabLayout title="Sistem Pencernaan Manusia" breadcrumb={['Home', 'Simulasi', category]}>
            <div className="grid grid-cols-1 lg:grid-cols-[1fr_320px] gap-6">
                <div className="rounded-2xl bg-[#0F1729] border border-slate/20 flex items-center justify-center p-4">
                    <svg viewBox="0 0 400 400" className="w-full max-w-md">
                        {ORGANS.map((o) => (
                            <circle
                                key={o.key}
                                cx={o.cx} cy={o.cy} r={o.r}
                                fill={selected === o.key ? '#FBBF24' : 'rgba(251,191,36,0.4)'}
                                stroke={selected === o.key ? '#FFFFFF' : 'none'}
                                strokeWidth={2}
                                className="cursor-pointer transition-all"
                                onClick={() => setSelected(o.key)}
                            />
                        ))}
                    </svg>
                </div>
                <div className="control-panel space-y-3">
                    <p className="text-xs text-slate/70">Klik tiap organ untuk melihat fungsinya (urutan dari atas: mulut ke usus besar).</p>
                    {ORGANS.map((o) => (
                        <button
                            key={o.key}
                            onClick={() => setSelected(o.key)}
                            className={`w-full text-left rounded-lg px-3 py-2 text-sm transition-colors ${
                                selected === o.key ? 'bg-amber/20 text-amber' : 'text-slate hover:bg-white/5'
                            }`}
                        >
                            {o.label}
                        </button>
                    ))}
                    {selected && (
                        <div className="pt-2 border-t border-slate/15">
                            <p className="text-paper text-sm">{ORGANS.find((o) => o.key === selected)?.desc}</p>
                        </div>
                    )}
                </div>
            </div>
        </LabLayout>
    );
}

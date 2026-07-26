import { useState } from 'react';
import LabLayout from '../../Layouts/LabLayout';

const PAIRS = ['A-T', 'T-A', 'G-C', 'C-G'];
const COLORS = { A: '#8FD3FE', T: '#FBBF24', G: '#3ABF6B', C: '#E63946' };

export default function DnaStructure({ category = 'Biologi' }) {
    const [rungs] = useState(() => Array.from({ length: 10 }, () => PAIRS[Math.floor(Math.random() * PAIRS.length)]));
    const [selected, setSelected] = useState(null);

    return (
        <LabLayout title="Struktur DNA" breadcrumb={['Home', 'Simulasi', category]}>
            <div className="grid grid-cols-1 lg:grid-cols-[1fr_320px] gap-6">
                <div className="rounded-2xl bg-[#0F1729] border border-slate/20 flex items-center justify-center p-4">
                    <svg viewBox="0 0 300 420" className="h-full max-h-[420px]">
                        {rungs.map(([left, , right], i) => {
                            const y = 20 + i * 38;
                            const wave = Math.sin(i * 0.6) * 40;
                            const x1 = 150 + wave;
                            const x2 = 150 - wave;
                            return (
                                <g key={i} className="cursor-pointer" onClick={() => setSelected(i)}>
                                    <line x1={x1} y1={y} x2={x2} y2={y} stroke={selected === i ? '#FFFFFF' : '#7C8AA5'} strokeWidth={selected === i ? 3 : 1.5} />
                                    <circle cx={x1} cy={y} r={9} fill={COLORS[left]} />
                                    <circle cx={x2} cy={y} r={9} fill={COLORS[right]} />
                                </g>
                            );
                        })}
                    </svg>
                </div>
                <div className="control-panel space-y-4">
                    <p className="text-xs text-slate/70">
                        Klik salah satu pasangan basa untuk menyorotinya. Aturan pasangan basa: A selalu berpasangan
                        dengan T, G selalu berpasangan dengan C (komplementer).
                    </p>
                    <div className="flex gap-3 text-xs flex-wrap">
                        {Object.entries(COLORS).map(([base, color]) => (
                            <span key={base} className="flex items-center gap-1.5">
                                <span className="h-2.5 w-2.5 rounded-full" style={{ background: color }} />
                                Basa {base}
                            </span>
                        ))}
                    </div>
                    {selected !== null && (
                        <div className="pt-2 border-t border-slate/15">
                            <p className="text-paper text-sm">
                                Pasangan basa ke-{selected + 1}: <strong>{rungs[selected]}</strong>
                            </p>
                        </div>
                    )}
                </div>
            </div>
        </LabLayout>
    );
}

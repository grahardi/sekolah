import { useRef, useState } from 'react';
import LabLayout from '../../Layouts/LabLayout';

export default function ProbabilityStats({ category = 'Matematika' }) {
    const [mode, setMode] = useState('Dadu');
    const sides = mode === 'Dadu' ? 6 : 2;
    const labels = mode === 'Dadu' ? ['1', '2', '3', '4', '5', '6'] : ['Angka', 'Gambar'];
    const [counts, setCounts] = useState(Array(sides).fill(0));
    const [totalRolls, setTotalRolls] = useState(0);

    const roll = (times) => {
        const newCounts = [...counts];
        for (let i = 0; i < times; i++) {
            const idx = Math.floor(Math.random() * sides);
            newCounts[idx]++;
        }
        setCounts(newCounts);
        setTotalRolls((t) => t + times);
    };

    const reset = () => { setCounts(Array(sides).fill(0)); setTotalRolls(0); };
    const switchMode = (m) => {
        setMode(m);
        setCounts(Array(m === 'Dadu' ? 6 : 2).fill(0));
        setTotalRolls(0);
    };

    const maxCount = Math.max(...counts, 1);
    const theoreticalProb = (1 / sides) * 100;
    const colors = ['bg-sky-400', 'bg-amber-400', 'bg-emerald-400', 'bg-rose-400', 'bg-violet-400', 'bg-lime-400'];

    return (
        <LabLayout title="Peluang & Statistika" breadcrumb={['Home', 'Simulasi', category]}>
            <div className="grid grid-cols-1 lg:grid-cols-[1fr_320px] gap-6">
                <div className="rounded-2xl bg-[#0F1729] border border-slate/20 p-6 flex flex-col justify-end min-h-[380px]">
                    <div className="flex items-end justify-center gap-3 h-64">
                        {counts.map((c, i) => (
                            <div key={i} className="flex flex-col items-center gap-2 flex-1">
                                <span className="text-paper text-xs font-mono">{c}</span>
                                <div
                                    className={`w-full rounded-t-md ${colors[i % colors.length]}`}
                                    style={{ height: `${(c / maxCount) * 200 || 2}px` }}
                                />
                                <span className="text-slate text-xs font-mono">{labels[i]}</span>
                            </div>
                        ))}
                    </div>
                </div>
                <div className="control-panel space-y-5">
                    <div className="flex gap-2">
                        {['Dadu', 'Koin'].map((m) => (
                            <button key={m} onClick={() => switchMode(m)} className={`flex-1 text-sm rounded-lg py-2 ${mode === m ? 'bg-amber text-ink' : 'bg-white/10 text-slate'}`}>
                                {m}
                            </button>
                        ))}
                    </div>
                    <div className="flex gap-2">
                        <button onClick={() => roll(1)} className="flex-1 rounded-lg bg-amber text-ink font-medium py-2 hover:brightness-95">Kocok 1x</button>
                        <button onClick={() => roll(100)} className="flex-1 rounded-lg bg-white/10 text-paper font-medium py-2 hover:bg-white/20">Kocok 100x</button>
                    </div>
                    <button onClick={reset} className="w-full rounded-lg bg-alert/20 text-alert font-medium py-2 hover:bg-alert/30">Reset</button>
                    <div className="pt-2 border-t border-slate/15 space-y-1">
                        <div className="flex justify-between"><span className="text-slate">Total percobaan</span><span className="dial-readout">{totalRolls}</span></div>
                        <div className="flex justify-between"><span className="text-slate">Peluang teoritis</span><span className="dial-readout">{theoreticalProb.toFixed(1)}%</span></div>
                    </div>
                    <p className="text-xs text-slate/70">
                        Semakin banyak percobaan, distribusi hasil semakin mendekati peluang teoritis (Hukum Bilangan Besar).
                    </p>
                </div>
            </div>
        </LabLayout>
    );
}

import { useEffect, useRef, useState } from 'react';
import LabLayout from '../../Layouts/LabLayout';

export default function SequenceSeries({ category = 'Matematika' }) {
    const canvasRef = useRef(null);
    const [type, setType] = useState('Aritmetika');
    const [first, setFirst] = useState(2);
    const [diffOrRatio, setDiffOrRatio] = useState(3);
    const [n, setN] = useState(10);

    const terms = Array.from({ length: n }, (_, i) =>
        type === 'Aritmetika' ? first + diffOrRatio * i : first * Math.pow(diffOrRatio, i)
    );
    const sum = terms.reduce((a, b) => a + b, 0);

    useEffect(() => {
        const canvas = canvasRef.current;
        const ctx = canvas.getContext('2d');
        ctx.clearRect(0, 0, canvas.width, canvas.height);

        const maxVal = Math.max(...terms.map(Math.abs), 1);
        const barWidth = canvas.width / terms.length;
        const baseY = canvas.height - 30;

        terms.forEach((val, i) => {
            const h = (Math.abs(val) / maxVal) * (canvas.height - 60);
            const x = i * barWidth + 4;
            const y = val >= 0 ? baseY - h : baseY;
            ctx.fillStyle = i % 2 === 0 ? '#FBBF24' : '#8FD3FE';
            ctx.fillRect(x, y, barWidth - 8, h);
        });

        ctx.strokeStyle = '#7C8AA5';
        ctx.beginPath(); ctx.moveTo(0, baseY); ctx.lineTo(canvas.width, baseY); ctx.stroke();
    }, [terms]);

    return (
        <LabLayout title="Barisan & Deret" breadcrumb={['Home', 'Simulasi', category]}>
            <div className="grid grid-cols-1 lg:grid-cols-[1fr_320px] gap-6">
                <div className="rounded-2xl bg-[#0F1729] border border-slate/20 flex items-center justify-center p-4">
                    <canvas ref={canvasRef} width={480} height={300} className="max-w-full" />
                </div>
                <div className="control-panel space-y-5">
                    <div className="flex gap-2">
                        {['Aritmetika', 'Geometri'].map((t) => (
                            <button key={t} onClick={() => setType(t)} className={`flex-1 text-sm rounded-lg py-2 ${type === t ? 'bg-amber text-ink' : 'bg-white/10 text-slate'}`}>
                                {t}
                            </button>
                        ))}
                    </div>
                    <div>
                        <div className="flex justify-between text-slate mb-1"><span>Suku pertama (a)</span><span className="dial-readout">{first}</span></div>
                        <input type="range" min={-10} max={10} value={first} onChange={(e) => setFirst(Number(e.target.value))} className="w-full accent-amber" />
                    </div>
                    <div>
                        <div className="flex justify-between text-slate mb-1"><span>{type === 'Aritmetika' ? 'Beda (b)' : 'Rasio (r)'}</span><span className="dial-readout">{diffOrRatio}</span></div>
                        <input
                            type="range"
                            min={type === 'Aritmetika' ? -5 : 0.2}
                            max={type === 'Aritmetika' ? 5 : 2.5}
                            step={type === 'Aritmetika' ? 1 : 0.1}
                            value={diffOrRatio}
                            onChange={(e) => setDiffOrRatio(Number(e.target.value))}
                            className="w-full accent-amber"
                        />
                    </div>
                    <div>
                        <div className="flex justify-between text-slate mb-1"><span>Banyak suku (n)</span><span className="dial-readout">{n}</span></div>
                        <input type="range" min={3} max={20} value={n} onChange={(e) => setN(Number(e.target.value))} className="w-full accent-amber" />
                    </div>
                    <div className="pt-2 border-t border-slate/15">
                        <div className="flex justify-between"><span className="text-slate">Jumlah n suku (Sn)</span><span className="dial-readout">{sum.toFixed(1)}</span></div>
                    </div>
                </div>
            </div>
        </LabLayout>
    );
}

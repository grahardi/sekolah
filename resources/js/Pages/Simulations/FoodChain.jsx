import { useEffect, useRef, useState } from 'react';
import LabLayout from '../../Layouts/LabLayout';

export default function FoodChain({ category = 'Biologi' }) {
    const canvasRef = useRef(null);
    const historyRef = useRef({ prey: [], predator: [] });
    const stateRef = useRef({ prey: 50, predator: 20, t: 0 });

    const [growthRate, setGrowthRate] = useState(0.1);
    const [predationRate, setPredationRate] = useState(0.01);
    const [running, setRunning] = useState(true);

    useEffect(() => {
        historyRef.current = { prey: [], predator: [] };
        stateRef.current = { prey: 50, predator: 20, t: 0 };
    }, []);

    useEffect(() => {
        const canvas = canvasRef.current;
        const ctx = canvas.getContext('2d');
        let raf;
        let frame = 0;

        const draw = () => {
            frame++;
            if (running && frame % 4 === 0) {
                const s = stateRef.current;
                const dPrey = growthRate * s.prey - predationRate * s.prey * s.predator;
                const dPred = predationRate * s.prey * s.predator * 0.4 - 0.08 * s.predator;
                s.prey = Math.max(0, Math.min(300, s.prey + dPrey));
                s.predator = Math.max(0, Math.min(300, s.predator + dPred));
                historyRef.current.prey.push(s.prey);
                historyRef.current.predator.push(s.predator);
                if (historyRef.current.prey.length > 150) {
                    historyRef.current.prey.shift();
                    historyRef.current.predator.shift();
                }
            }

            ctx.clearRect(0, 0, canvas.width, canvas.height);
            const h = historyRef.current;
            const maxVal = 300;

            const drawLine = (data, color) => {
                ctx.strokeStyle = color;
                ctx.lineWidth = 2;
                ctx.beginPath();
                data.forEach((v, i) => {
                    const x = (i / 150) * canvas.width;
                    const y = canvas.height - (v / maxVal) * canvas.height;
                    i === 0 ? ctx.moveTo(x, y) : ctx.lineTo(x, y);
                });
                ctx.stroke();
            };
            drawLine(h.prey, '#3ABF6B');
            drawLine(h.predator, '#E63946');

            raf = requestAnimationFrame(draw);
        };
        raf = requestAnimationFrame(draw);
        return () => cancelAnimationFrame(raf);
    }, [growthRate, predationRate, running]);

    const reset = () => {
        stateRef.current = { prey: 50, predator: 20, t: 0 };
        historyRef.current = { prey: [], predator: [] };
    };

    return (
        <LabLayout title="Ekosistem & Rantai Makanan" breadcrumb={['Home', 'Simulasi', category]}>
            <div className="grid grid-cols-1 lg:grid-cols-[1fr_320px] gap-6">
                <div className="rounded-2xl bg-[#0F1729] border border-slate/20 flex items-center justify-center p-4">
                    <canvas ref={canvasRef} width={480} height={300} className="max-w-full" />
                </div>
                <div className="control-panel space-y-5">
                    <div className="flex gap-4 text-xs">
                        <span className="flex items-center gap-1.5"><span className="h-2 w-2 rounded-full bg-[#3ABF6B]" />Mangsa (herbivora)</span>
                        <span className="flex items-center gap-1.5"><span className="h-2 w-2 rounded-full bg-[#E63946]" />Pemangsa (karnivora)</span>
                    </div>
                    <div>
                        <div className="flex justify-between text-slate mb-1"><span>Laju reproduksi mangsa</span><span className="dial-readout">{growthRate.toFixed(2)}</span></div>
                        <input type="range" min={0.02} max={0.3} step={0.01} value={growthRate} onChange={(e) => setGrowthRate(Number(e.target.value))} className="w-full accent-amber" />
                    </div>
                    <div>
                        <div className="flex justify-between text-slate mb-1"><span>Tingkat pemangsaan</span><span className="dial-readout">{predationRate.toFixed(3)}</span></div>
                        <input type="range" min={0.002} max={0.03} step={0.001} value={predationRate} onChange={(e) => setPredationRate(Number(e.target.value))} className="w-full accent-amber" />
                    </div>
                    <div className="flex gap-2">
                        <button onClick={() => setRunning((r) => !r)} className="flex-1 rounded-lg bg-amber text-ink font-medium py-2">{running ? 'Jeda' : 'Lanjutkan'}</button>
                        <button onClick={reset} className="flex-1 rounded-lg bg-alert/20 text-alert font-medium py-2">Reset</button>
                    </div>
                    <p className="text-xs text-slate/70">
                        Populasi mangsa & pemangsa saling mempengaruhi - pola naik-turun ini disebut siklus predator-mangsa (Lotka-Volterra).
                    </p>
                </div>
            </div>
        </LabLayout>
    );
}
